/**
 * Created with the standard prompt, plus:
 * Create a 'fill with color' tool
 */
class FillTool extends Plugin {
    constructor() {
        super('FillTool');
        this.canvas = null;
        this.ctx = null;
        this.fillColor = '#000000';
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('click', this.fillCanvas.bind(this));
    }

    fillCanvas() {
        this.ctx.fillStyle = this.fillColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }

    renderUI(container) {
        console.log('FillTool - renderUI');
        console.log(container);
        // Create a button for the fill tool
        const button = document.createElement('button');
        button.innerText = 'Fill Tool';
        button.onclick = this.activate.bind(this);

        // Create a color picker for fill color
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.fillColor = e.target.value;

        // Append the UI elements to the provided container
        container.appendChild(button);
        container.appendChild(colorPicker);
    }

    activate() {
        // Activation code for the Fill Tool, if necessary
    }
}

loadPlugin(FillTool);
addPluginUI('FillTool', 'toolbarContainer');
activatePlugin('FillTool');