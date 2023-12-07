/**
 * Created with the standard prompt, plus:
 * Give me a tool that only paints Norway flags
 */
class NorwayFlagTool extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Norway Flag';
        this.icon = 'fa-flag';
    }

    draw(e) {
        if (!this.drawing) return;
        let mousePos = this.getMousePos(this.canvas, e);
        this.drawFlag(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
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
}

loadPlugin(NorwayFlagTool);
