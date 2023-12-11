```javascript
plugins.MysteryBrush = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Mystery Brush';
        this.icon = 'fa-paint-brush';
        this.colors = ['#FF6347', '#4682B4', '#32CD32', '#FFD700', '#DA70D6'];
        this.shapes = ['circle', 'square', 'triangle'];
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        let x = mousePos.x - this.canvas.offsetLeft;
        let y = mousePos.y - this.canvas.offsetTop;
        let shapeIndex = Math.floor(Math.random() * this.shapes.length);
        let colorIndex = Math.floor(Math.random() * this.colors.length);

        this.ctx.fillStyle = this.colors[colorIndex];
        switch (this.shapes[shapeIndex]) {
            case 'circle':
                this.ctx.beginPath();
                this.ctx.arc(x, y, Math.random() * 20 + 5, 0, Math.PI * 2);
                this.ctx.fill();
                break;
            case 'square':
                let size = Math.random() * 20 + 5;
                this.ctx.fillRect(x - size / 2, y - size / 2, size, size);
                break;
            case 'triangle':
                this.ctx.beginPath();
                this.ctx.moveTo(x, y - 15);
                this.ctx.lineTo(x + 15, y + 15);
                this.ctx.lineTo(x - 15, y + 15);
                this.ctx.closePath();
                this.ctx.fill();
                break;
            default:
                this.ctx.fillRect(x - 5, y - 5, 10, 10);
                break;
        }
    }

    customUI(container) {
        // Mystery Brush doesn't need UI controls for now
    }
}

// Make sure that your 'plugins' object is declared in the global scope before this point.
// Here's an example of how it should be initialized.
// window.plugins = window.plugins || {};
```