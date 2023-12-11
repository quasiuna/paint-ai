plugins.WormTool = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Worm';
        this.icon = 'fa-worm';
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.fillStyle = this.getRandomColor();
        const randomSize = this.getRandomSize();
        this.ctx.beginPath();
        this.ctx.arc(
            mousePos.x - this.canvas.offsetLeft,
            mousePos.y - this.canvas.offsetTop,
            randomSize,
            0,
            2 * Math.PI
        );
        this.ctx.fill();
    }

    getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    getRandomSize() {
        return Math.floor(Math.random() * (20 - 5 + 1)) + 5;
    }
    
    customUI(container) {
        // Worm plugin may not need specific UI elements, but you could add them here.
    }
}

// Assuming 'plugins' is a globally accessible variable.
if (typeof plugins !== "object") {
    plugins = {}; // Initialize if it does not exist
}