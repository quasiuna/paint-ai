/**
 * Created with the standard prompt, plus:
 * Add a spray paint effect
 */
class SprayPaintTool extends Plugin {
    constructor() {
        super('SprayPaintTool');
        this.canvas = null;
        this.ctx = null;
        this.drawing = false;
        this.density = 50; // Adjust the density of the spray
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
        this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
        this.canvas.addEventListener('mousemove', this.draw.bind(this));
    }

    startDrawing(e) {
        this.drawing = true;
        this.draw(e);
    }

    stopDrawing() {
        this.drawing = false;
    }

    draw(e) {
        if (!this.drawing) return;
        const mouseX = e.clientX - this.canvas.offsetLeft;
        const mouseY = e.clientY - this.canvas.offsetTop;
        
        for (let i = 0; i < this.density; i++) {
            const offsetX = Math.random() * 20 - 10; // Random spray effect within 20px area
            const offsetY = Math.random() * 20 - 10;
            this.ctx.fillRect(mouseX + offsetX, mouseY + offsetY, 1, 1); // Draw small dots
        }
    }

    renderUI(container) {
        console.log('SprayPaintTool - renderUI');
        console.log(container);
        // Create a button for the spray paint tool
        const button = document.createElement('button');
        button.innerText = 'Spray Paint Tool';
        button.onclick = this.activate.bind(this);

        // Create a color picker
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.ctx.fillStyle = e.target.value;

        // Create a density selector
        const densitySelector = document.createElement('input');
        densitySelector.type = 'range';
        densitySelector.min = '10';
        densitySelector.max = '100';
        densitySelector.value = '50';
        densitySelector.onchange = (e) => {
            console.log('Density changed');
            this.density = e.target.value;
        }

        // Append the UI elements to the provided container
        container.appendChild(button);
        container.appendChild(colorPicker);
        container.appendChild(densitySelector);
    }

    activate() {
        // Activation code for the Spray Paint Tool, if necessary
    }
}

loadPlugin(SprayPaintTool);
addPluginUI('SprayPaintTool', 'toolbarContainer');
activatePlugin('SprayPaintTool');