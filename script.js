document.addEventListener('DOMContentLoaded', function () {
    // Select all tool buttons
    var toolButtons = document.querySelectorAll('.tool-button');

    // Function to handle click event on each tool button
    toolButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove 'selected' class from all tool buttons
            toolButtons.forEach(function (btn) {
                btn.classList.remove('selected');
            });

            // Add 'selected' class to the clicked tool button
            this.classList.add('selected');
        });
    });

    const colorButtons = document.querySelectorAll('.color-button');
    const selectedColorButton = document.querySelector('.selected-color-button');

    // Set initial background color for each color button
    colorButtons.forEach(button => {
        const color = button.getAttribute('data-color');
        button.style.backgroundColor = color;

        // Click event for each color button
        button.addEventListener('click', function() {
            // Remove selected class from all color buttons
            colorButtons.forEach(btn => btn.classList.remove('selected'));

            // Add selected class to clicked button
            this.classList.add('selected');

            // Update the selected color button's background color
            selectedColorButton.style.backgroundColor = this.getAttribute('data-color');
        });
    });
});

document.getElementById('newPlugin').addEventListener('click', function() {
    document.getElementById('aiInteraction').style.display = 'block';
});

document.querySelectorAll("#plugins button").forEach(el => {
    el.addEventListener('click', function(e) {
        console.log("Click plugin", this.dataset.plugin);
        loadExistingPlugin(this.dataset.plugin);
    });
})

function sendInputToAI() {
    var userInput = document.getElementById('userInput').value;
}

class Plugin {
    constructor(name) {
        this.name = name;
    }

    init() {
        // Basic initialization code common to all plugins
    }

    renderUI(container) {
        // Code to render the plugin's UI in the provided container
    }

    activate() {
        // Code to activate the plugin's functionality
    }
}

const canvas = document.getElementById("paintCanvas");
canvas.width = 800;
canvas.height = 300;

const pluginRegistry = {};

function loadPlugin(pluginDefinition) {
    const plugin = new pluginDefinition();
    plugin.init("paintCanvas");
    pluginRegistry[plugin.name] = plugin;
}

function addPluginUI(pluginName, containerId) {
    const container = document.getElementById(containerId);
    const plugin = pluginRegistry[pluginName];
    if (plugin && container) {
        plugin.renderUI(container);
    }
}

function activatePlugin(pluginName) {
    const plugin = pluginRegistry[pluginName];
    if (plugin) {
        plugin.activate();
    }
}

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
    fetch('/server.php?method=load&plugin=' + plugin, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        loadPluginDynamically(data.pluginCode);
    });
}

function sendInputToAI() {
    console.log("Loading new plugin with AI");
    var userInput = document.getElementById('userInput').value;
    
    fetch('/server.php?method=ai', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ input: userInput })
    })
    .then(response => response.json())
    .then(data => {
        loadPluginDynamically(data.pluginCode);
    });
}


