/**
 * Created with the standard prompt, plus:
 * draws animated random lines that fly off in random directions when painted
 */
plugins.AnimatedLines = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Animated Lines Tool';
        this.icon = 'fa-bezier-curve';
        this.lines = [];
        this.line_speed = 5;
        this.line_length = 5;
        this.line_decay = 1;
        this.line_multiplier = 1;
        this.activated = false;
    }

    paint(e) {
        if (!this.painting) return;
        let mousePos = this.getMousePos(this.canvas, e);

        for (let l = 1; l <= parseInt(this.line_multiplier); l++) {
            const x = mousePos.x - this.canvas.offsetLeft;
            const y = mousePos.y - this.canvas.offsetTop;
            const angle = Math.random() * Math.PI * 2;
            const length = Math.random() * 20 + parseInt(this.line_length);
            const speed = Math.random() * parseInt(this.line_speed);

            this.lines.push({
                x, y, angle, length, speed,
                opacity: 1.0
            });
        }
    }

    update() {
        if (this.ctx == null) {
            return;
        }

        this.lines.forEach(line => {
            this.ctx.beginPath();
            this.ctx.moveTo(line.x, line.y);
            this.ctx.lineTo(line.x + Math.cos(line.angle) * line.length, line.y + Math.sin(line.angle) * line.length);
            const rgb = this.hexToRgb(this.color);
            this.ctx.strokeStyle = `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${line.opacity})`;
            this.ctx.stroke();

            // Update line position and opacity
            line.x += Math.cos(line.angle) * line.speed;
            line.y += Math.sin(line.angle) * line.speed;
            line.opacity -= 0.02 * this.line_decay;
        });
        // Remove lines that are fully faded
        this.lines = this.lines.filter(line => line.opacity > 0);
    }


    customUI(container) {
        // Create a line length control
        const lineLengthControl = document.createElement('input');
        lineLengthControl.type = 'range';
        lineLengthControl.title = 'Line Length';
        lineLengthControl.min = '1';
        lineLengthControl.max = '100';
        lineLengthControl.value = '5';
        lineLengthControl.onchange = (e) => {
            this.line_length = e.target.value;
        };

        container.appendChild(lineLengthControl);

        // Create a line decay control
        const lineDecayControl = document.createElement('input');
        lineDecayControl.type = 'range';
        lineDecayControl.title = 'Line Decay';
        lineDecayControl.min = '0.01';
        lineDecayControl.max = '20';
        lineDecayControl.step = '0.01';
        lineDecayControl.value = '1';
        lineDecayControl.onchange = (e) => {
            this.line_decay = e.target.value;
        };

        container.appendChild(lineDecayControl);

        // Create a line decay control
        const lineSpeedControl = document.createElement('input');
        lineSpeedControl.type = 'range';
        lineSpeedControl.title = 'Line Speed';
        lineSpeedControl.min = '0.01';
        lineSpeedControl.max = '20';
        lineSpeedControl.step = '0.01';
        lineSpeedControl.value = '1';
        lineSpeedControl.onchange = (e) => {
            this.line_speed = e.target.value;
        };

        container.appendChild(lineSpeedControl);

        // Create a line multiplier control
        const lineMultiplierControl = document.createElement('input');
        lineMultiplierControl.type = 'range';
        lineMultiplierControl.title = 'Line Multiplier';
        lineMultiplierControl.min = '1';
        lineMultiplierControl.max = '10';
        lineMultiplierControl.value = '1';
        lineMultiplierControl.onchange = (e) => {
            this.line_multiplier = e.target.value;
        };

        container.appendChild(lineMultiplierControl);
    }
}
