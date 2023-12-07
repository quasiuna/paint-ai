/**
 * Created with the standard prompt, plus:
 * Give me a tool that only paints Norway flags
 */
class NorwayFlagTool extends Plugin {
    constructor() {
        super('NorwayFlagTool');
        this.canvas = null;
        this.ctx = null;
        this.drawing = false;
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
        this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
        this.canvas.addEventListener('mousemove', this.draw.bind(this));
    }

    startDrawing(e) {
        this.drawing = true;
        this.drawFlag(e.clientX - this.canvas.offsetLeft, e.clientY - this.canvas.offsetTop);
    }

    stopDrawing() {
        this.drawing = false;
    }

    draw(e) {
        if (!this.drawing) return;
        this.drawFlag(e.clientX - this.canvas.offsetLeft, e.clientY - this.canvas.offsetTop);
    }

    drawFlag(x, y) {
        const flagWidth = 60;
        const flagHeight = 40;
        const crossWidth = 10;

        // Draw flag background
        this.ctx.fillStyle = 'white';
        this.ctx.fillRect(x, y, flagWidth, flagHeight);

        // Draw the red rectangles
        this.ctx.fillStyle = 'red';
        this.ctx.fillRect(x, y, flagWidth, crossWidth); // Top horizontal red
        this.ctx.fillRect(x, y + flagHeight - crossWidth, flagWidth, crossWidth); // Bottom horizontal red
        this.ctx.fillRect(x, y, crossWidth, flagHeight); // Left vertical red
        this.ctx.fillRect(x + flagWidth - crossWidth, y, crossWidth, flagHeight); // Right vertical red

        // Draw the blue cross
        this.ctx.fillStyle = 'blue';
        this.ctx.fillRect(x + crossWidth * 2, y + crossWidth, crossWidth * 2, flagHeight - crossWidth * 2); // Vertical blue
        this.ctx.fillRect(x + crossWidth, y + crossWidth * 2, flagWidth - crossWidth * 2, crossWidth * 2); // Horizontal blue
    }

    renderUI(container) {
        const button = document.createElement('button');
        button.innerText = 'Norway Flag Tool';
        button.onclick = this.activate.bind(this);

        container.appendChild(button);
    }

    activate() {
        // Activation code for the Norway Flag Tool, if necessary
    }
}

loadPlugin(NorwayFlagTool);
addPluginUI('NorwayFlagTool', 'toolbarContainer');
activatePlugin('NorwayFlagTool');