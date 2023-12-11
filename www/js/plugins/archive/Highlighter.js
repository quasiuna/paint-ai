plugins.Highlighter = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Highlighter';
        this.icon = 'fa-highlighter';
        this.neonColors = ['#39FF14', '#FF3F92', '#3FA9FF', '#FFFA1F'];
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.lineCap = 'round';
        this.ctx.globalAlpha = 0.5;
        this.ctx.strokeStyle = this.getRandomNeonColor();
        this.ctx.lineTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }

    getRandomNeonColor() {
        return this.neonColors[Math.floor(Math.random() * this.neonColors.length)];
    }

    customUI(container) {
        // Create a brush size selector
        const brushSizeSelector = document.createElement('input');
        brushSizeSelector.type = 'range';
        brushSizeSelector.min = '5';
        brushSizeSelector.max = '30';
        brushSizeSelector.value = '15';
        brushSizeSelector.onchange = (e) => {
            this.ctx.lineWidth = e.target.value;
        };

        // Append the brush size selector to the provided container
        container.appendChild(brushSizeSelector);
    }
}