/**
 * Created with the standard prompt, plus:
 * only paints the English flag at 16 x 16 px
 */
class EnglishFlagTool extends Plugin {
    constructor() {
        super('EnglishFlagTool');
        this.canvas = null;
        this.ctx = null;
        this.flagWidth = 16;
        this.flagHeight = 16;
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.canvas.addEventListener('mousedown', this.paintFlag.bind(this));
    }

    paintFlag(e) {
        const x = e.clientX - this.canvas.offsetLeft;
        const y = e.clientY - this.canvas.offsetTop;

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

    renderUI(container) {
        const button = document.createElement('button');
        button.innerText = 'English Flag Tool';
        button.onclick = this.activate.bind(this);

        container.appendChild(button);
    }

    activate() {
        // Activation code for the English Flag Tool, if necessary
    }
}

loadPlugin(EnglishFlagTool);
addPluginUI('EnglishFlagTool', 'toolbarContainer');
activatePlugin('EnglishFlagTool');
