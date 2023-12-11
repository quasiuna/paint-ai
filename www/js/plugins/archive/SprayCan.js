plugins.SprayCan = class extends Tool {
    constructor(name) {
        super('Spray Can');
        this.name = name;
        this.description = 'Spray Paint';
        this.icon = 'fa-spray-can';
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.fillStyle = "red";
        this.ctx.beginPath();
        this.ctx.arc(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop, 10, 0, 2 * Math.PI);
        this.ctx.fill();
        this.ctx.beginPath();
        this.ctx.moveTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    customUI(container) {
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
        container.appendChild(brushSizeSelector);
    }
}