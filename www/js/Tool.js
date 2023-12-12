class Tool {
    constructor(name) {
        this.name = name;
        this.description = 'Example Description';
        this.icon = null;
        this.canvas = null;
        this.ctx = null;
        this.painting = false;
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

    startPainting(e) {
        if (this.selected) {
          this.painting = true;
          this.paint(e);
        }
    }

    stopPainting() {
        this.painting = false;
        this.ctx.beginPath();
    }

    init(canvasId) {
        if (this.ctx != null) {
            this.ctx.restore();
        }
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.ctx.save();

        this.canvas.addEventListener('mousedown', this.startPainting.bind(this));
        this.canvas.addEventListener('mouseup', this.stopPainting.bind(this));
        this.canvas.addEventListener('mousemove', this.paint.bind(this));
    }

    paint() {
        // handle the user painting with the mouse
    }

    update () {
        // for animated plugins...this method will be called frequently by requestAnimationFrame
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
        this._addToolButton(document.querySelector("#tools"));

        const pluginUI = document.createElement('div');
        pluginUI.className = 'tool-custom-ui';
        document.querySelector("#custom").appendChild(pluginUI);
        this.customUI(pluginUI);

        var num = pluginUI.querySelectorAll('*').length;
        if (num == 0) {
            pluginUI.remove();
        } else {
            const pluginUIHeading = document.createElement("p");
            pluginUIHeading.innerText = this.name;
            pluginUI.prepend(pluginUIHeading);
            enableTooltips();
        }
    }

    _addToolButton(container) {
        this.createToolButton(container, this.icon, this.description);
    }
}
