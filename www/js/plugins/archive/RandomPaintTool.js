plugins.RandomPaintTool = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Random Paint';
        this.icon = 'fa-paint-brush';

        // Set initial color
        this.currentColor = `#${Math.floor(Math.random()*16777215).toString(16)}`;
    }

    draw(e) {
        if (!this.drawing) return;

        // Update color randomly on each draw call
        this.ctx.strokeStyle = this.currentColor = `#${Math.floor(Math.random()*16777215).toString(16)}`;
        
        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.lineCap = 'round';
        this.ctx.lineTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    customUI(container) {
        // Create a brush size selector
        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '1';
        brushSizeSelector.max = '50'; // Larger max for more expressive strokes
        brushSizeSelector.value = '5'; // Default size value
        brushSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        }

        // Append the brush size selector to the provided container
        container.appendChild(brushSizeSelector);
    }
}
