plugins.EggTool = class extends Tool {
  constructor(name) {
    super(name);
    this.name = name;
    this.description = "Egg";
    this.icon = "fa-egg";
  }
  draw(e) {
    if (!this.drawing) return;
    let mousePos = this.getMousePos(this.canvas, e);
    const eggWidth = this.eggWidth;
    const eggHeight = this.eggHeight;
    this.ctx.beginPath();
    this.ctx.ellipse(mousePos.x - this.canvas.offsetLeft, mousePos.y - this.canvas.offsetTop, eggWidth / 2, eggHeight / 2, Math.PI / 4, 0, 2 * Math.PI);
    this.ctx.fillStyle = this.fillColor;
    this.ctx.fill();
    this.ctx.closePath();
  }
  customUI(container) {
    const colorPicker = document.createElement("input");
    colorPicker.type = "color";
    colorPicker.onchange = (e) => {
      this.fillColor = e.target.value;
    };
    const eggWidthSelector = document.createElement("input");
    eggWidthSelector.type = "range";
    eggWidthSelector.min = "10";
    eggWidthSelector.max = "100";
    eggWidthSelector.value = "30";
    eggWidthSelector.onchange = (e) => {
      this.eggWidth = e.target.value;
    };
    const eggHeightSelector = document.createElement("input");
    eggHeightSelector.type = "range";
    eggHeightSelector.min = "10";
    eggHeightSelector.max = "100";
    eggHeightSelector.value = "40";
    eggHeightSelector.onchange = (e) => {
      this.eggHeight = e.target.value;
    };
    container.appendChild(colorPicker);
    container.appendChild(eggWidthSelector);
    container.appendChild(eggHeightSelector);
  }
}
