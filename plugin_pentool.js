class PenTool extends Plugin {
    constructor() {
        super('PenTool');
        this.canvas = null;
        this.ctx = null;
        this.drawing = false;
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
        this.ctx.beginPath();
    }

    draw(e) {
        if (!this.drawing) return;
        // this.ctx.lineWidth = 3;
        this.ctx.lineCap = 'round';

        this.ctx.lineTo(e.clientX - this.canvas.offsetLeft, e.clientY - this.canvas.offsetTop);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(e.clientX - this.canvas.offsetLeft, e.clientY - this.canvas.offsetTop);
    }

    renderUI(container) {
        const button = document.createElement('button');
        button.innerText = 'Pen Tool';
        button.onclick = this.activate.bind(this);

        // Create a color picker
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.ctx.strokeStyle = e.target.value;

        // Create a brush size selector
        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '1';
        brushSizeSelector.max = '10';
        brushSizeSelector.value = '3';
        brushSizeSelector.onchange = (e) => {
            console.log('here');
            this.ctx.lineWidth = e.target.value;
        }

        // Append the UI elements to the provided container
        container.appendChild(button);
        container.appendChild(colorPicker);
        container.appendChild(brushSizeSelector);
    }

    activate() {
        // Activation code for the Pen Tool, if necessary
    }
}

loadPlugin(PenTool);
addPluginUI('PenTool', 'toolbarContainer');
activatePlugin('PenTool');