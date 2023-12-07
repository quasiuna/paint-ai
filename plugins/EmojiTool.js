/**
 * Created with the standard prompt, plus:
 * Add a tool that lets me add random emojis
 */

class EmojiTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'EmojiTool';
        this.description = 'Emojis';
        this.icon = 'fa-smile';
        this.emojis = ['😀', '😂', '👍', '🚀', '💖', '🐱', '🌟', '🎨'];
    }

    draw(e) {
        if (!this.drawing) return;
        let mousePos = this.getMousePos(this.canvas, e);
        const randomEmoji = this.emojis[Math.floor(Math.random() * this.emojis.length)];
        const fontSize = 32; // can be adjustable
        this.ctx.font = `${fontSize}px Arial`;
        this.ctx.fillText(randomEmoji, mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
    }
}

loadPlugin(EmojiTool);
