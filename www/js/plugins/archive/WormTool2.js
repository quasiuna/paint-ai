plugins.WormTool2 = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Worm2';
        this.icon = 'fa-worm';
        this.worms = [];
    }

    draw(e) {
        if (!this.drawing) {
            this.addWorm(e);
        }
        this.moveWorms();
        this.checkCollisions();
    }

    addWorm(e) {
        const color = '#' + Math.floor(Math.random()*16777215).toString(16);
        const size = Math.random() * 10 + 5;
        const mousePos = this.getMousePos(this.canvas, e);
        let newWorm = {
            x: mousePos.x - this.canvas.offsetLeft,
            y: mousePos.y - this.canvas.offsetTop,
            dx: Math.random() * 4 - 2,
            dy: Math.random() * 4 - 2,
            size: size,
            color: color,
            length: 1
        };
        this.worms.push(newWorm);
        this.drawing = true;
    }

    moveWorms() {
        this.worms.forEach((worm) => {
            this.ctx.fillStyle = worm.color;
            this.ctx.beginPath();
            this.ctx.arc(worm.x, worm.y, worm.size, 0, Math.PI * 2);
            this.ctx.closePath();
            this.ctx.fill();

            worm.x += worm.dx;
            worm.y += worm.dy;

            if (worm.x < 0 || worm.x > this.canvas.width) worm.dx *= -1;
            if (worm.y < 0 || worm.y > this.canvas.height) worm.dy *= -1;
        });
    }

    checkCollisions() {
        for (let i = 0; i < this.worms.length; ++i) {
            for (let j = i + 1; j < this.worms.length; ++j) {
                let dist = Math.hypot(this.worms[i].x - this.worms[j].x, this.worms[i].y - this.worms[j].y);
                if (dist < this.worms[i].size + this.worms[j].size) {
                    if (this.worms[i].length >= this.worms[j].length) {
                        this.worms[i].length += this.worms[j].length;
                        this.worms[i].size *= 2;
                        this.worms.splice(j, 1);
                    } else {
                        this.worms[j].length += this.worms[i].length;
                        this.worms[j].size *= 2;
                        this.worms.splice(i, 1);
                    }
                    break;
                }
            }
        }
    }

    customUI(container) {
        // This tool may not have a specific UI yet
    }
}
