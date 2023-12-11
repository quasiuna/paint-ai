plugins.BouncyBall = class extends Tool {
    constructor(name) {
        super(name);
        this.name = name;
        this.description = 'Bouncy Ball';
        this.icon = 'fa-circle';
        this.animated = true;

        this.ball = {
            x: 50,
            y: 50,
            radius: 20,
            dx: Math.random() * 6 - 3,
            dy: Math.random() * 6 - 3
        };
    }

    draw(e) {
        let canvasWidth = this.canvas.width;
        let canvasHeight = this.canvas.height;

        this.ball.x += this.ball.dx;
        this.ball.y += this.ball.dy;

        if (this.ball.x + this.ball.radius > canvasWidth || this.ball.x - this.ball.radius < 0) {
            this.ball.dx = -this.ball.dx;
        }

        if (this.ball.y + this.ball.radius > canvasHeight || this.ball.y - this.ball.radius < 0) {
            this.ball.dy = -this.ball.dy;
        }

        // this.ctx.clearRect(0, 0, canvasWidth, canvasHeight);
        this.ctx.beginPath();
        this.ctx.arc(this.ball.x, this.ball.y, this.ball.radius, 0, 2 * Math.PI);
        this.ctx.fillStyle = '#000000';
        this.ctx.fill();
        this.ctx.closePath();
    }
}