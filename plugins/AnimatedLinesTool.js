/**
 * Created with the standard prompt, plus:
 * draws animated random lines that fly off in random directions when painted
 */
class AnimatedLinesTool extends Plugin {
    constructor() {
        super('AnimatedLinesTool');
        this.canvas = null;
        this.ctx = null;
        this.drawing = false;
        this.lines = [];
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
        this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
        this.canvas.addEventListener('mousemove', this.addLine.bind(this));
        requestAnimationFrame(this.animate.bind(this));
    }

    startDrawing(e) {
        this.drawing = true;
    }

    stopDrawing() {
        this.drawing = false;
    }

    addLine(e) {
        if (!this.drawing) return;
        const x = e.clientX - this.canvas.offsetLeft;
        const y = e.clientY - this.canvas.offsetTop;
        const angle = Math.random() * Math.PI * 2;
        const length = Math.random() * 20 + 5;
        const speed = Math.random() * 5 + 1;

        this.lines.push({
            x, y, angle, length, speed,
            opacity: 1.0
        });
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.lines.forEach(line => {
            this.drawAnimatedLine(line);
            // Update line position and opacity
            line.x += Math.cos(line.angle) * line.speed;
            line.y += Math.sin(line.angle) * line.speed;
            line.opacity -= 0.02;
        });
        // Remove lines that are fully faded
        this.lines = this.lines.filter(line => line.opacity > 0);

        requestAnimationFrame(this.animate.bind(this));
    }

    drawAnimatedLine(line) {
        this.ctx.beginPath();
        this.ctx.moveTo(line.x, line.y);
        this.ctx.lineTo(line.x + Math.cos(line.angle) * line.length, line.y + Math.sin(line.angle) * line.length);
        this.ctx.strokeStyle = `rgba(0, 0, 0, ${line.opacity})`;
        this.ctx.stroke();
    }

    renderUI(container) {
        console.log('AnimatedLinesTool - renderUI');
        const button = document.createElement('button');
        button.innerText = 'Animated Lines Tool';
        button.onclick = this.activate.bind(this);
        container.appendChild(button);
    }

    activate() {
        // Activation code for the Animated Lines Tool, if necessary
    }
}

loadPlugin(AnimatedLinesTool);
addPluginUI('AnimatedLinesTool', 'toolbarContainer');
activatePlugin('AnimatedLinesTool');
