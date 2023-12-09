plugins.Paintbrush = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Paintbrush';
        this.icon = 'fa-paint-brush';
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
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.ctx.strokeStyle = e.target.value;

        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '1';
        brushSizeSelector.max = '10';
        brushSizeSelector.value = '3';
        brushSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        }

        container.appendChild(colorPicker);
        container.appendChild(brushSizeSelector);
    }
}