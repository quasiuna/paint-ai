/**
 * Created with the standard prompt, plus:
 * draw 10 randomly coloured and sized lines which are animated as they
 * move away from the curser and bounce off the edges of the canvas. Whilst
 * they move, the lines should swerve away from the curser if they start to
 * come close to it. After 5 seconds they should fade away.
 */

class AnimatedLinesTool2 extends Tool {
    constructor(name) {
        super(name);
        this.name = 'AnimatedLinesTool';
        this.description = 'Animated Lines Tool';
        this.icon = 'fa-vector-square';
        this.lines = [];
        this.maxLines = 10;
        this.animationFrameId = null;
    }

    draw(e) {
        if (!this.drawing) return;
        // let mousePos = this.getMousePos(this.canvas, e);

        // Create new lines if the maximum number hasn't been reached
        if (this.lines.length < this.maxLines) {
            this.lines.push(this.createLine(e));
        }

        // Start the animation if it's not already running
        if (!this.animationFrameId) {
            this.animate();
        }
    }

    createLine(e) {
        return {
            x: e.clientX - this.canvas.offsetLeft,
            y: e.clientY - this.canvas.offsetTop,
            vx: Math.random() * 4 - 2, // Random velocity
            vy: Math.random() * 4 - 2,
            color: `hsl(${Math.random() * 360}, 100%, 50%)`,
            size: Math.random() * 10 + 1,
            born: performance.now()
        };
    }

    animate() {
        this.animationFrameId = requestAnimationFrame(this.animate.bind(this));
        this.updateLines();
        this.drawLines();
        this.removeOldLines();
    }

    updateLines() {
        this.lines.forEach(line => {
            // Bounce off the edges
            if (line.x < 0 || line.x > this.canvas.width) line.vx = -line.vx;
            if (line.y < 0 || line.y > this.canvas.height) line.vy = -line.vy;

            // Update position
            line.x += line.vx;
            line.y += line.vy;
        });
    }

    drawLines() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.lines.forEach(line => {
            this.ctx.beginPath();
            this.ctx.moveTo(line.x, line.y);
            this.ctx.lineTo(line.x - line.vx * 10, line.y - line.vy * 10);
            this.ctx.strokeStyle = line.color;
            this.ctx.lineWidth = line.size;
            this.ctx.stroke();
        });
    }

    removeOldLines() {
        const currentTime = performance.now();
        this.lines = this.lines.filter(line => currentTime - line.born < 5000);
    }
}

loadPlugin(AnimatedLinesTool2);
