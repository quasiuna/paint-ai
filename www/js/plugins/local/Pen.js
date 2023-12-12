plugins.Pen = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Pen';
        this.icon = 'fa-pen-nib';
    }

    paint(e) {
        if (!this.painting) return;

        // important: use getMousePos instead of e.clientX
        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.lineCap = 'round';
        this.ctx.lineTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    customUI(container) {
        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.title = 'Brush Size';
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '1';
        brushSizeSelector.max = '10';
        brushSizeSelector.value = '3';
        brushSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        }
        container.appendChild(brushSizeSelector);
    }
}
