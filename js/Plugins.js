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
        const previousPluginButton = sidebar.querySelector(".tool-button.selected");
        if (previousPluginButton) {
          previousPluginButton.classList.remove("selected");
          const previousPlugin = plugins[previousPluginButton.dataset.plugin];
          previousPlugin.selected = false;
        }

        toolButton.classList.add("selected");

        const plugin = plugins[toolButton.dataset.plugin];
        if (plugin) {
          plugin.selected = true;
          plugin.init("paintCanvas");
        }
      }
    });
  }

  const createPluginButton = document.getElementById("createPlugin");
  const newPluginButton = document.getElementById("newPlugin");

  if (newPluginButton) {
    newPluginButton.addEventListener("click", function () {
      document.getElementById("overlay").style.display = "block";
      document.getElementById("aiInteraction").style.display = "block";
      document.getElementById("aiInteractionForm").style.display = "block";
      createPluginButton.innerText = 'Submit';
      createPluginButton.disabled = false;
    });
  }

  if (createPluginButton) {
    createPluginButton.addEventListener("click", function () {
      console.log("Loading new plugin with AI");
      showProgress("#newPluginProgressContainer");
      document.getElementById("aiInteractionForm").style.display = "none";
      this.innerText = "Please wait...";
      this.disabled = true;

      const status = document.getElementById("createPluginStatus");
      status.innerText = "Finding an available AI Robot Developer...";

      var tool_name = document.getElementById("tool_name").value;
      var tool_description = document.getElementById("tool_description").value;

      fetch("/server.php?method=banter", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: tool_name,
          description: tool_description
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          const banter = document.createElement('div');
          const roboDev = getRandomRoboDev();
          const fader = new TextFader("createPluginStatus", [
            "Ok, found available robo developer...",
            roboDev + " has been assigned to build your tool!",
            roboDev + " is reading your instructions...",
            "Ok, got it! Let me tell you a joke while I write this code...",
            roboDev + ": " + data.banter,
            roboDev + ": " + data.banter,
            "Ok, that joke was pretty bad, I get it.",
            "Almost there...",
            "Just getting the code tested...",
          ]);
          fader.start();
        });

      fetch("/server.php?method=ai", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          name: tool_name,
          description: tool_description
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (typeof data.pluginCode != "undefined") {
            loadPluginDynamically(data.pluginCode);
            loadPlugin(data.tool);

            status.style.fontWeight = 'bold';
            status.style.color = 'green';
            status.innerHTML = "<i class='fa fa-check'></i> It worked! Enjoy your " + data.tool;

            setTimeout(() => hideModal(), 1000);
          } else {
            const error = data.error || "Bad news! The code failed testing. Sadly, it has been deleted...try again?";
            console.error(error);
            status.style.fontWeight = 'bold';
            status.style.color = 'red';
            status.innerText = error;
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

  const loadPluginsButton = document.getElementById("loadPlugins");
  
  if (loadPluginsButton) {
    loadPluginsButton.addEventListener("click", () => {
      document.querySelectorAll("#plugins div[data-plugin]").forEach((el) => {
        loadExistingPlugin(el.dataset.plugin);
      });

      document.getElementById("plugins").remove();
    });
  }
});
