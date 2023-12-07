class PenTool extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Pen';
        this.icon = 'fa-pen-nib';
    }

    draw(e) {
        if (!this.drawing) return;

        // important: use getMousePos instead of e.clientX
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

        // Create a brush size selector
        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '1';
        brushSizeSelector.max = '10';
        brushSizeSelector.value = '3';
        brushSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        }

        // Append the UI elements to the provided container
        container.appendChild(colorPicker);
        container.appendChild(brushSizeSelector);
    }
}

loadPlugin(PenTool);
