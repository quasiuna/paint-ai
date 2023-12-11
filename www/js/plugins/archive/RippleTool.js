plugins.RippleTool = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Ripple';
        this.icon = 'fa-water'; // Example icon class
        this.maxRadius = 100; // Max radius of the ripple
        this.rippleInterval = null; // Interval for the ripple to expand
        this.currentRadius = 0; // Current radius of the ripple
        this.lineWidth = 2; // Line width of the ripple
        this.color = '#000'; // Start color
        this.rippleStartPos = {x: 0, y: 0}; // Position to start the ripple
    }

    startDrawing(e) {
        this.drawing = true;
        this.rippleStartPos = this.getMousePos(this.canvas, e);
        if (this.rippleInterval) clearInterval(this.rippleInterval);
        this.currentRadius = 0;
        this.rippleInterval = setInterval(() => { this.drawRipple(); }, 30);
    }

    stopDrawing() {
        this.drawing = false;
        if (this.rippleInterval) {
            clearInterval(this.rippleInterval);
            this.rippleInterval = null;
        }
        this.ctx.beginPath();
    }

    // Custom draw function for RippleTool
    drawRipple() {
        if (this.currentRadius > this.maxRadius) {
            this.stopDrawing();
            return;
        }

        this.ctx.lineWidth = this.lineWidth;
        this.ctx.strokeStyle = this.color;
        this.ctx.beginPath();
        this.ctx.arc(this.rippleStartPos.x, this.rippleStartPos.y, this.currentRadius, 0, Math.PI * 2);
        this.ctx.stroke();
        this.currentRadius += 1;
    }

    draw(e) {
        // No continual drawing behavior for the RippleTool; the ripples are drawn in intervals
    }

    customUI(container) {
        // Create a color picker
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.color = e.target.value;

        // Create a max radius selector
        const maxRadiusSelector = document.createElement('input');
        maxRadiusSelector.type = 'range';
        maxRadiusSelector.min = '10';
        maxRadiusSelector.max = '200';
        maxRadiusSelector.value = String(this.maxRadius);
        maxRadiusSelector.onchange = (e) => {
            this.maxRadius = e.target.value;
        }

        // Append the UI elements to the provided container
        container.appendChild(colorPicker);
        container.appendChild(maxRadiusSelector);
    }
}
