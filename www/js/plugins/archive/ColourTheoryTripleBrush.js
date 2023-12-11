
plugins.ColourTheoryTripleBrush = class extends Tool {
  constructor(name) {
    super(name);
    this.name = name;
    this.description = 'Colour Theory Triple Brush';
    this.icon = 'fa-brush';
  }

  draw(e) {
    if (!this.drawing) return;

    let mousePos = this.getMousePos(this.canvas, e);

    // Logic for color theory related colors
    const mainColor = this.getRandomColor();
    const relatedColors = this.getRelatedColors(mainColor);

    this.ctx.lineCap = 'round';

    // Draw main line with the main color
    this.ctx.strokeStyle = mainColor;
    this.drawLine(mousePos);

    // Draw two related lines with related colors
    relatedColors.forEach((color, index) => {
      this.ctx.strokeStyle = color;
      // Offset each new line by brushSize * 2 position
      this.drawLine({
        x: mousePos.x - this.canvas.offsetLeft + ((index + 1) * this.ctx.lineWidth * 2),
        y: mousePos.y - this.canvas.offsetTop
      });
    });
  }

  drawLine(mousePos) {
    this.ctx.lineTo(mousePos.x, mousePos.y);
    this.ctx.stroke();
    this.ctx.beginPath();
    this.ctx.moveTo(mousePos.x, mousePos.y);
  }

  getRandomColor() {
    // Generate a random hue
    const hue = Math.floor(Math.random() * 360);
    // Return the CSS HSL color string
    return `hsl(${hue}, 100%, 50%)`;
  }

  getRelatedColors(color) {
    const [hue, saturation, lightness] = color.match(/\d+/g);

    // Calculate two related hues by adding and subtracting 30 from the main hue
    let relatedHue1 = (parseInt(hue) + 30) % 360;
    let relatedHue2 = (parseInt(hue) - 30 + 360) % 360;

    // Return the related CSS HSL color strings
    return [
      `hsl(${relatedHue1}, ${saturation}%, ${lightness}%)`,
      `hsl(${relatedHue2}, ${saturation}%, ${lightness}%)`
    ];
  }

  customUI(container) {
    // Create a brush size selector
    const brushSizeSelector = document.createElement('input');
    brushSizeSelector.type = 'range';
    brushSizeSelector.min = '1';
    brushSizeSelector.max = '20';
    brushSizeSelector.value = this.ctx.lineWidth || '3';
    brushSizeSelector.onchange = (e) => {
      this.ctx.lineWidth = e.target.value;
    }

    // Append the UI elements to the provided container
    container.appendChild(brushSizeSelector);
  }
};
