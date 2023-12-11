plugins.SprayCanTool = class extends Tool {
  constructor(name) {
    super(name);
    this.name = name;
    this.description = "Spray Can";
    this.icon = "fa-spray-can";
  }

  createSprayPaintGradient() {
    // Create the gradient colors array
    const gradientColors = ["rgba(0, 0, 255, 1)", "rgba(0, 87, 186, 1)", "rgba(255, 191, 134, 1)", "rgba(255, 255, 0, 1)", "rgba(191, 255, 0, 1)", "rgba(186, 186, 0, 1)", "rgba(255, 0, 0, 1)"];
  
    // Create the gradient object
    const gradient = document.createElement("canvas");
    gradient.width = 100;
    gradient.height = 100;
    this.ctx.fillStyle = "linear-gradient(to bottom right, " + gradientColors.join(", ") + ")";
    this.ctx.fillRect(0, 0, gradient.width, gradient.height);
  
    return gradient;
  }
  
  drawSprayPaintPattern(ctx, gradient) {
      // Assuming the gradient is a canvas gradient object
      this.ctx.fillStyle = gradient;
  
      // Randomly distribute the spray particles
      for (let i = 0; i < 100; i++) {
          let radius = Math.random() * 20; // random radius
          let x = Math.random() * ctx.canvas.width;
          let y = Math.random() * ctx.canvas.height;
          this.ctx.beginPath();
          this.ctx.arc(x, y, radius, 0, 2 * Math.PI);
          this.ctx.fill();
      }
  }

  draw(e) {
    if (!this.drawing) return;
    let mousePos = this.getMousePos(this.canvas, e);

    // Get the current canvas width and height
    let width = this.canvas.width;
    let height = this.canvas.height;

    // Calculate the angle and speed of the spray can
    let angle = -Math.atan2(mousePos.y - height / 2, mousePos.x - width / 2);
    let speed = 30;

    // Set a canvas for the spray paint effect
    const sprayPaintCanvas = document.createElement("canvas");
    sprayPaintCanvas.width = 100;
    sprayPaintCanvas.height = 100;

    // Get the 2D context of the spray paint canvas
    const sprayPaintContext = sprayPaintCanvas.getContext("2d");

    // Create a gradient for the spray paint effect
    const gradient = this.createSprayPaintGradient();

    // Draw the spray paint pattern
    this.drawSprayPaintPattern(sprayPaintContext, gradient);

    // Use the 2D context of the current canvas
    this.ctx.globalCompositeOperation = "destination-out";

    // Draw the spray paint pattern on the current canvas
    this.ctx.drawImage(sprayPaintCanvas, mousePos.x - width / 2, mousePos.y - height / 2, width, height);
  }

  customUI(container) {
    const sprayPatternSize = document.createElement("input");
    sprayPatternSize.type = "range";
    sprayPatternSize.min = 1;
    sprayPatternSize.max = 50;
    sprayPatternSize.value = 20;
    sprayPatternSize.onchange = (e) => {
      const size = parseInt(e.target.value);
      document.getElementById("spray-size").textContent = size + "px";
    };
    container.appendChild(sprayPatternSize);

    const sizeElement = document.createElement("p");
    sizeElement.id = "spray-size";
    sizeElement.textContent = "20px";
    container.appendChild(sizeElement);
  }
};


