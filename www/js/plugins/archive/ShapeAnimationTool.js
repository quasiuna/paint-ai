/**
 * Created with the standard prompt, plus:
 * draws 17 randomly sized, random-coloured squares, cicles, rectangles and
 * triangles, that animate as they fly and fade away whilst spinning, dancing,
 * growing, shrinking and bouncing away in random directions
 */
class ShapeAnimationTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'ShapeAnimationTool';
        this.description = 'Animated shapes';
        this.icon = 'fa-draw-polygon';
        this.shapes = [];
        this.animationFrameId = null;
    }

    addShapes() {
        for (let i = 0; i < 17; i++) {
            const shapeType = ['square', 'circle', 'rectangle', 'triangle'][Math.floor(Math.random() * 4)];
            const color = `#${Math.floor(Math.random()*16777215).toString(16)}`;
            const size = Math.random() * 50 + 10;
            const x = Math.random() * this.canvas.width;
            const y = Math.random() * this.canvas.height;
            const dx = Math.random() * 4 - 2; // velocity x
            const dy = Math.random() * 4 - 2; // velocity y
            const dr = Math.random() * 0.1 - 0.05; // rotation speed

            this.shapes.push({ shapeType, color, x, y, size, dx, dy, dr, rotation: 0 });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.shapes.forEach(shape => {
            this.ctx.save();
            this.ctx.fillStyle = shape.color;
            this.ctx.translate(shape.x + shape.size / 2, shape.y + shape.size / 2);
            shape.rotation += shape.dr;
            this.ctx.rotate(shape.rotation);
            this.ctx.translate(-shape.size / 2, -shape.size / 2);

            switch (shape.shapeType) {
                case 'square':
                    this.ctx.fillRect(0, 0, shape.size, shape.size);
                    break;
                case 'circle':
                    this.ctx.beginPath();
                    this.ctx.arc(shape.size / 2, shape.size / 2, shape.size / 2, 0, Math.PI * 2);
                    this.ctx.fill();
                    break;
                case 'rectangle':
                    this.ctx.fillRect(0, 0, shape.size * 2, shape.size);
                    break;
                case 'triangle':
                    this.ctx.beginPath();
                    this.ctx.moveTo(shape.size / 2, 0);
                    this.ctx.lineTo(shape.size, shape.size);
                    this.ctx.lineTo(0, shape.size);
                    this.ctx.closePath();
                    this.ctx.fill();
                    break;
            }

            this.ctx.restore();

            // Update position and check bounds
            shape.x += shape.dx;
            shape.y += shape.dy;
            shape.size *= 0.99; // gradually shrink
            if (shape.x < 0 || shape.x > this.canvas.width) shape.dx *= -1;
            if (shape.y < 0 || shape.y > this.canvas.height) shape.dy *= -1;
        });

        this.animationFrameId = requestAnimationFrame(this.animate.bind(this));
    }

    draw(e) {
        if (!this.drawing) return;
        cancelAnimationFrame(this.animationFrameId);
        this.shapes = [];
        this.addShapes();
        this.animate();
    }
}

loadPlugin(ShapeAnimationTool);
