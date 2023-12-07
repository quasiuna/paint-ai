document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("newPlugin").addEventListener("click", function () {
    document.getElementById("aiInteraction").style.display = "block";
  });

  function sendInputToAI() {
    var userInput = document.getElementById("userInput").value;
  }

  const pluginRegistry = {};

  function loadPlugin(pluginDefinition) {
    const plugin = new pluginDefinition(pluginDefinition.constructor.name);
    console.log("Loading plugin [" + plugin.name + "]");

    if (typeof pluginRegistry[name] == "undefined") {
      pluginRegistry[plugin.name] = plugin;
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
        loadPluginDynamically(data.pluginCode);
      });
  }

  function sendInputToAI() {
    console.log("Loading new plugin with AI");
    var userInput = document.getElementById("userInput").value;

    fetch("/server.php?method=ai", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ input: userInput }),
    })
      .then((response) => response.json())
      .then((data) => {
        loadPluginDynamically(data.pluginCode);
      });
  }

  document.getElementById("loadPlugins").addEventListener("click", () => {
    document.querySelectorAll("#plugins div[data-plugin]").forEach((el) => {
      loadExistingPlugin(el.dataset.plugin);
    });

    document.getElementById("plugins").remove();
  });
});
