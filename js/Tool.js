class Tool {
    constructor(name) {
        this.name = name;
        this.description = 'Example Description';
        this.icon = null;

        this.canvas = null;
        this.ctx = null;
        this.drawing = false;
        this.animated = false; // change to true to have the draw() method called continuously
        this.selected = false;
    }

    getMousePos(canvas, evt) {
        let rect = this.canvas.getBoundingClientRect();
        let scaleX = this.canvas.width / rect.width;    // relationship bitmap vs. element for X
        let scaleY = this.canvas.height / rect.height;  // relationship bitmap vs. element for Y
    
        return {
          x: (evt.clientX - rect.left) * scaleX,  // scale mouse coordinates after they have
          y: (evt.clientY - rect.top) * scaleY    // been adjusted to be relative to element
        };
    }

    startDrawing(e) {
        if (this.selected) {
          this.drawing = true;
          this.draw(e);
        }
    }

    stopDrawing() {
        this.drawing = false;
        this.ctx.beginPath();
    }

    init(canvasId) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');

        this.canvas.addEventListener('mousedown', this.startDrawing.bind(this));
        this.canvas.addEventListener('mouseup', this.stopDrawing.bind(this));
        this.canvas.addEventListener('mousemove', this.draw.bind(this));
    }

    addToolButton(container) {
        this.createToolButton(container, this.icon, this.description);
    }

    customUI(container) {
        // custom controls for this tool
    }

    createToolButton(container, iconClass, description) {
        var toolButton = document.createElement('div');
        toolButton.className = 'tool-button';
        toolButton.title = this.description;
        toolButton.dataset.plugin = this.name;

        var icon = document.createElement('i');
        icon.className = 'fas ' + iconClass;
        icon.description = description;

        toolButton.appendChild(icon);
        container.appendChild(toolButton);
    }

    activate() {
        this.addToolButton(document.querySelector("#tools"));
        this.customUI(document.querySelector("#custom"));

        if (this.animated) {
            this.startAnimationLoop();
        }
    }

    startAnimationLoop() {
        const animateStep = () => {
            if (this.selected) {
                this.draw();
            }
            window.requestAnimationFrame(animateStep);
        };
        window.requestAnimationFrame(animateStep);
    }
}