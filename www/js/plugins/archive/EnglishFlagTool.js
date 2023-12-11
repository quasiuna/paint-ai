/**
 * Created with the standard prompt, plus:
 * only paints the English flag at 16 x 16 px
 */
class EnglishFlagTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'EnglishFlagTool';
        this.description = 'English Flag';
        this.icon = 'fa-flag';
        this.flagWidth = 16;
        this.flagHeight = 16;
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);

        const x = mousePos.x - this.canvas.offsetLeft;
        const y = mousePos.y - this.canvas.offsetTop;

        // Clear previous drawings
        this.ctx.clearRect(x, y, this.flagWidth, this.flagHeight);

        // Draw the flag background
        this.ctx.fillStyle = 'white';
        this.ctx.fillRect(x, y, this.flagWidth, this.flagHeight);

        // Draw the red cross
        this.ctx.fillStyle = 'red';
        this.ctx.fillRect(x + (this.flagWidth / 2) - 1, y, 2, this.flagHeight); // vertical part
        this.ctx.fillRect(x, y + (this.flagHeight / 2) - 1, this.flagWidth, 2); // horizontal part
    }
}

loadPlugin(EnglishFlagTool);
