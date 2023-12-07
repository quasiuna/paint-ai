/**
 * Created with the standard prompt, plus:
 * Create a 'fill with color' tool
 */
class FillTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'FillTool';
        this.description = 'Fill Colour';
        this.icon = 'fa-fill-drip';
        this.fillColor = '#000000';
    }

    draw(e) {
        if (!this.drawing) return;
        this.ctx.fillStyle = this.fillColor;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
    }

    customUI(container) {
        // Create a color picker for fill color
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.fillColor = e.target.value;

        // Append the UI elements to the provided container
        container.appendChild(colorPicker);
    }
}

loadPlugin(FillTool);
