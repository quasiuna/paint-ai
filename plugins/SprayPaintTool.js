/**
 * Created with the standard prompt, plus:
 * Add a spray paint effect
 */
class SprayPaintTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'SprayPaintTool';
        this.description = 'Spray Paint';
        this.icon = 'fa-spray-can';
        this.density = 50; // Adjust the density of the spray
    }

    draw(e) {
        if (!this.drawing) return;
        let mousePos = this.getMousePos(this.canvas, e);
        const mouseX = mousePos.x - this.canvas.offsetLeft;
        const mouseY = mousePos.y - this.canvas.offsetTop;
        
        for (let i = 0; i < this.density; i++) {
            const offsetX = Math.random() * 20 - 10; // Random spray effect within 20px area
            const offsetY = Math.random() * 20 - 10;
            this.ctx.fillRect(mouseX + offsetX, mouseY + offsetY, 1, 1); // Draw small dots
        }
    }

    customUI(container) {
        // Create a color picker
        const colorPicker = document.createElement('input');
        colorPicker.type = 'color';
        colorPicker.onchange = (e) => this.ctx.fillStyle = e.target.value;

        // Create a density selector
        const densitySelector = document.createElement('input');
        densitySelector.type = 'range';
        densitySelector.min = '10';
        densitySelector.max = '100';
        densitySelector.value = '50';
        densitySelector.onchange = (e) => {
            this.density = e.target.value;
        }

        container.appendChild(colorPicker);
        container.appendChild(densitySelector);
    }
}

loadPlugin(SprayPaintTool);
