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
      console.log("OK");
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
        console.log(data);
        loadPluginDynamically(data.pluginCode);
      });
  }

  document.querySelectorAll(".add-plugin").forEach((el) => {
    el.addEventListener("click", function (e) {
      console.log("Click plugin", this.dataset.plugin);
      loadExistingPlugin(this.dataset.plugin);
    });
  });

  function showExportOptions() {
    console.log("show export options");
    // List of supported image formats
    const formats = ["image/png", "image/jpeg", "image/webp", "image/bmp"];

    // Create a selection dialog
    let formatSelection =
      '<label>Select Image Format:</label><select id="formatSelector">';
    formats.forEach((format) => {
      formatSelection += `<option value="${format}">${format
        .split("/")[1]
        .toUpperCase()}</option>`;
    });
    formatSelection += '</select><button id="saveButton">Save</button>';
    formatSelection +=
      '<input type="checkbox" id="whiteBackgroundCheckbox"> <label for="whiteBackgroundCheckbox">Export with white background</label>';

    // Display the selection
    document.getElementById(
      "features"
    ).innerHTML += `<div id="exportOptions">${formatSelection}</div>`;

    // Event listener for save button
    document
      .getElementById("saveButton")
      .addEventListener("click", function () {
        saveImage(document.getElementById("formatSelector").value);
      });
  }

//   function saveImage(format) {
//     const canvas = document.getElementById("paintCanvas");
//     const url = canvas.toDataURL(format);

//     // Create a temporary link to trigger the download
//     const link = document.createElement("a");
//     link.download = "exported-image." + format.split("/")[1];
//     link.href = url;
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);

//     // Optionally, remove the export options
//     document.getElementById("exportOptions").remove();
//   }

function saveImage(format) {
    const originalCanvas = document.getElementById('paintCanvas');
    const whiteBackground = document.getElementById('whiteBackgroundCheckbox').checked;

    let canvasToExport = originalCanvas;

    if (whiteBackground) {
        // Create a temporary canvas
        const tempCanvas = document.createElement('canvas');
        tempCanvas.width = originalCanvas.width;
        tempCanvas.height = originalCanvas.height;
        const ctx = tempCanvas.getContext('2d');

        // Draw a white background
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

        // Draw the original canvas content over the white background
        ctx.drawImage(originalCanvas, 0, 0);

        // Use the temporary canvas for export
        canvasToExport = tempCanvas;
    }

    const url = canvasToExport.toDataURL(format);

    // Create a temporary link to trigger the download
    const link = document.createElement('a');
    link.download = 'exported-image.' + format.split('/')[1];
    link.href = url;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Optionally, remove the export options
    document.getElementById('exportOptions').remove();
}

  document.getElementById("exportButton").addEventListener("click", () => {
    showExportOptions();
  });
});
