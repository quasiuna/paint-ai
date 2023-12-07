/**
 * Created with the standard prompt, plus:
 * Add a tool that lets me add random emojis
 */

class EmojiTool extends Plugin {
    constructor() {
        super('EmojiTool');
        this.canvas = null;
        this.ctx = null;
        this.emojis = ['😀', '😂', '👍', '🚀', '💖', '🐱', '🌟', '🎨'];
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('click', this.addEmoji.bind(this));
    }

    addEmoji(e) {
        const randomEmoji = this.emojis[Math.floor(Math.random() * this.emojis.length)];
        const fontSize = 32; // can be adjustable
        this.ctx.font = `${fontSize}px Arial`;
        this.ctx.fillText(randomEmoji, e.clientX - this.canvas.offsetLeft, e.clientY - this.canvas.offsetTop);
    }

    renderUI(container) {
        console.log('EmojiTool - renderUI');
        console.log(container);

        // Create a button for the emoji tool
        const button = document.createElement('button');
        button.innerText = 'Emoji Tool';
        button.onclick = this.activate.bind(this);

        // Append the button to the provided container
        container.appendChild(button);
    }

    activate() {
        // Activation code for the Emoji Tool, if necessary
    }
}

loadPlugin(EmojiTool);
addPluginUI('EmojiTool', 'toolbarContainer');
activatePlugin('EmojiTool');