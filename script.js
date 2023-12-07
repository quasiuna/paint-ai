document.addEventListener("DOMContentLoaded", function () {
  const sidebar = document.querySelector("#sidebar");

  sidebar.addEventListener("click", (e) => {
    let toolButton = null;

    if (e.target.classList.contains("tool-button")) {
      toolButton = e.target;
    } else {
      toolButton = e.target.closest(".tool-button");
    }

    if (toolButton) {
      sidebar.querySelectorAll(".tool-button").forEach((el) => {
        el.classList.remove("selected");
      });
      toolButton.classList.add("selected");
    }
  });

  const colorButtons = document.querySelectorAll(".color-button");
  const selectedColorButton = document.querySelector(".selected-color-button");

  // Set initial background color for each color button
  colorButtons.forEach((button) => {
    const color = button.getAttribute("data-color");
    button.style.backgroundColor = color;

    // Click event for each color button
    button.addEventListener("click", function () {
      // Remove selected class from all color buttons
      colorButtons.forEach((btn) => btn.classList.remove("selected"));

      // Add selected class to clicked button
      this.classList.add("selected");

      // Update the selected color button's background color
      selectedColorButton.style.backgroundColor =
        this.getAttribute("data-color");
    });
  });

  const canvas = document.getElementById("paintCanvas");
  const ctx = canvas.getContext("2d");

  const observer = new ResizeObserver((entries) => {
    canvas.width = canvas.clientWidth;
    canvas.height = canvas.clientHeight;
  });
  observer.observe(canvas);
});

document.getElementById("newPlugin").addEventListener("click", function () {
  document.getElementById("aiInteraction").style.display = "block";
});

function sendInputToAI() {
  var userInput = document.getElementById("userInput").value;
}

const pluginRegistry = {};

function loadPlugin(pluginDefinition) {
    const plugin = new pluginDefinition(pluginDefinition.constructor.name);
  console.log('Loading plugin [' + plugin.name + ']');

  if (typeof pluginRegistry[name] == "undefined") {
    console.log('OK');
    pluginRegistry[plugin.name] = plugin;
    plugin.activate();
  } else {
    console.log('plugin [' + name + '] already loaded'); 
  }
}

// function addPluginUI(pluginName, containerId) {
//     const container = document.getElementById(containerId);
//     const plugin = pluginRegistry[pluginName];
//     if (plugin && container) {
//         plugin.renderUI(container);
//     }
// }

// function activatePlugin(pluginName) {
//     const plugin = pluginRegistry[pluginName];
//     if (plugin) {
//         plugin.activate();
//     }
// }

function loadPluginDynamically(pluginCode) {
  // Caution: Executing arbitrary code can be very dangerous!
  // Ensure safety measures here

  // Assuming pluginCode is a string of JavaScript code
  // that defines a plugin following the plugin structure
  eval(pluginCode);

  // Now, the new plugin should be available for use
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

document.querySelectorAll(".add-plugin").forEach((el) => {
  el.addEventListener("click", function (e) {
    console.log("Click plugin", this.dataset.plugin);
    loadExistingPlugin(this.dataset.plugin);
  });
});
