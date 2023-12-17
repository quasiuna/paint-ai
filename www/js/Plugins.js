const plugins = {};

document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector("#sidebar");

  if (sidebar) {
    sidebar.addEventListener("click", (e) => {
      let toolButton = null;

      if (e.target.classList.contains("tool-button")) {
        toolButton = e.target;
      } else {
        toolButton = e.target.closest(".tool-button");
      }

      if (toolButton) {
        selectPlugin(toolButton.dataset.plugin);
      }
    });
  }

  const createPluginButton = document.getElementById("createPlugin");
  const newPluginButton = document.getElementById("newPlugin");

  if (newPluginButton) {
    newPluginButton.addEventListener("click", function () {
      const start = document.getElementById("start");
      if (start) {
        start.style.display = "none";
      }
      showModal("#aiInteraction");
      document.getElementById("aiInteractionForm").style.display = "block";
      createPluginButton.innerText = "Submit";
      createPluginButton.disabled = false;
    });
  }

  if (createPluginButton) {
    createPluginButton.addEventListener("click", function () {
      var tool_name_input = document.getElementById("tool_name");
      var tool_description_input = document.getElementById("tool_description");

      if (tool_name_input.validity.valueMissing) {
        return;
      }
      if (tool_description_input.validity.valueMissing) {
        return;
      }

      var tool_name = tool_name_input.value;
      var tool_description = tool_description_input.value;

      console.log("Loading new plugin with AI");
      showProgress("#newPluginProgressContainer");
      document.getElementById("aiInteractionForm").style.display = "none";
      this.innerText = "Please wait...";
      this.disabled = true;

      const status = document.getElementById("createPluginStatus");
      const roboDev = getRandomRoboDev();
      status.innerText = "Finding an available AI Robot Developer...";

      const fader = new TextFader(
        "createPluginStatus",
        [
          "Ok, found available robo developer...",
          roboDev + " has been assigned to build your tool!",
          roboDev + " is reading your instructions...",
          "Almost there...",
          "Just testing the code ...",
        ],
        30000
      );
      fader.start();

      fetch("/server.php?method=ai", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: tool_name,
          description: tool_description,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (typeof data.pluginCode != "undefined") {
            loadPluginDynamically(data.pluginCode);
            loadPlugin(data.tool);

            status.style.fontWeight = "bold";
            status.style.color = "green";
            status.innerHTML = "<i class='fa fa-check'></i> It worked! Enjoy your " + data.tool;
            fader.stop();

            setTimeout(() => {
              hideModal();
              setTimeout(() => {
                showModal("#newToolSuccess");
                setTimeout(() => hideModal(), 2000);
              }, 30);
            }, 1000);
          } else {
            const error = data.error || "Bad news! The code failed testing. Sadly, it has been deleted...try again?";
            console.error(error);
            status.style.fontWeight = "bold";
            status.style.color = "red";
            status.innerText = error;
            fader.stop();
          }

          hideProgress("#newPluginProgressContainer");
        });
    });
  }

  function loadPlugin(pluginDefinition) {
    const pluginTest = new PluginTester(pluginDefinition, plugins[pluginDefinition]);
    pluginTest.run();

    if (!pluginTest.passed) {
      console.error(`Plugin tests failed, cannot load [${pluginDefinition}]`);
      return;
    }

    const plugin = new plugins[pluginDefinition](pluginDefinition);

    if (typeof plugins[name] == "undefined") {
      plugins[plugin.name] = plugin;
      plugin.activate();
      selectPlugin(pluginDefinition);
    } else {
      console.log("plugin [" + name + "] already loaded");
    }
  }

  function loadPluginDynamically(pluginCode) {
    eval(pluginCode);
  }

  function loadExistingPlugin(plugin) {
    console.log("Loading existing plugin [" + plugin + "]");
    fetch("/server.php?method=load&plugin=" + plugin, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        if (typeof data.pluginCode != "undefined") {
          loadPluginDynamically(data.pluginCode);
          loadPlugin(data.tool);
        } else {
          console.error(data.error || "Plugin cannot be loaded");
        }
      });
  }

  function deleteExistingPlugin(plugin) {
    console.log("Deleting existing plugin [" + plugin + "]");
    fetch("/server.php?method=delete", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        name: plugin,
      })
    })
      .then((response) => response.json())
      .then((data) => {
        location.reload();
      });
  }

  function selectPlugin(pluginClass) {
    const previousPluginButton = sidebar.querySelector(".tool-button.selected");
    if (previousPluginButton) {
      previousPluginButton.classList.remove("selected");
      const previousPlugin = plugins[previousPluginButton.dataset.plugin];
      previousPlugin.selected = false;
    }

    let toolButton = document.querySelector(".tool-button[data-plugin='" + pluginClass + "']");
    toolButton.classList.add("selected");

    const plugin = plugins[pluginClass];
    
    if (plugin) {
      plugin.selected = true;
      plugin.init("paintCanvas");
    }
  }

  const loadPluginsButton = document.getElementById("loadPlugins");

  if (loadPluginsButton) {
    loadPluginsButton.addEventListener("click", () => {
      document.querySelectorAll("#plugins [data-plugin]").forEach((el) => {
        loadExistingPlugin(el.dataset.plugin);
      });

      document.getElementById("plugins").remove();
    });
  }

  document.querySelectorAll("#plugins [data-plugin]").forEach((el) => {
    el.addEventListener("click", (e) => {
      let plugin = e.currentTarget.dataset.plugin;
      loadExistingPlugin(plugin);
    });
  });

  document.querySelectorAll("#plugins [data-delete]").forEach((el) => {
    el.addEventListener("click", (e) => {
      if (confirm('Are you sure?')) {
        let plugin = e.currentTarget.dataset.delete;
        deleteExistingPlugin(plugin);
      }
    });
  });
});
