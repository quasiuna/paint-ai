
const plugins = plugins || {};

plugins.Pencil = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Pencil';
        this.icon = 'fa-pencil-alt';
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.lineCap = 'round';
        this.ctx.lineTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    customUI(container) {
        // Create a color picker
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.ctx.strokeStyle = e.target.value;

        // Create a pencil size selector
        const pencilSizeSelector = document.createElement('input');
        pencilSizeSelector.type = 'range';
        pencilSizeSelector.min = '1';
        pencilSizeSelector.max = '5';
        pencilSizeSelector.value = '1';
        pencilSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        }

        // Append the UI elements to the provided container
        container.appendChild(colorPicker);
        container.appendChild(pencilSizeSelector);
    }
}
