plugins.PencilTool = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Pencil';
        this.icon = 'fa-pencil';
    }

    draw(e) {
        if (!this.drawing) return;

        let mousePos = this.getMousePos(this.canvas, e);
        this.ctx.lineTo(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop);
        this.ctx.stroke();
    }

    customUI(container) {
        // No custom UI required for the pencil tool
    }
}