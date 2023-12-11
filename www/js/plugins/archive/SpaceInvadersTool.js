/**
 * Created with the standard prompt, plus:
 * Start an interactive game of space invaders using the cursor keys and spacebar
 */
class SpaceInvadersTool extends Tool {
    constructor(name) {
        super(name);
        this.name = 'SpaceInvadersTool';
        this.description = 'Space Invaders!';
        this.icon = 'fa-space-shuttle';
        this.player = null;
        this.aliens = [];
        this.projectiles = [];
        this.lastTime = 0;
    }

    activate() {
        super.activate();

        this.player = {
            x: this.canvas.width / 2,
            y: this.canvas.height - 30,
            width: 20,
            height: 20,
            speed: 5
        };

        window.addEventListener('keydown', this.handleKeyDown.bind(this));
        window.addEventListener('keyup', this.handleKeyUp.bind(this));

        this.populateAliens();
        requestAnimationFrame(this.gameLoop.bind(this));
    }

    populateAliens() {
        for (let i = 0; i < 5; i++) {
            for (let j = 0; j < 10; j++) {
                this.aliens.push({
                    x: 30 + j * 50,
                    y: 30 + i * 50,
                    width: 20,
                    height: 20
                });
            }
        }
    }

    handleKeyDown(e) {
        if (e.key === 'ArrowLeft') this.player.x -= this.player.speed;
        if (e.key === 'ArrowRight') this.player.x += this.player.speed;
        if (e.key === ' ') this.shoot();
    }

    handleKeyUp(e) {
        // Additional key up logic can be added here
    }

    shoot() {
        this.projectiles.push({
            x: this.player.x + this.player.width / 2,
            y: this.player.y,
            width: 5,
            height: 10,
            speed: 7
        });
    }

    gameLoop(timestamp) {
        const deltaTime = timestamp - this.lastTime;
        this.lastTime = timestamp;

        this.update(deltaTime);
        this.draw();

        requestAnimationFrame(this.gameLoop.bind(this));
    }

    update(deltaTime) {
        this.projectiles.forEach(projectile => {
            projectile.y -= projectile.speed;
        });

        this.projectiles = this.projectiles.filter(projectile => projectile.y > 0);
    }

    draw() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Draw player
        this.ctx.fillStyle = 'blue';
        this.ctx.fillRect(this.player.x, this.player.y, this.player.width, this.player.height);

        // Draw aliens
        this.aliens.forEach(alien => {
            this.ctx.fillStyle = 'red';
            this.ctx.fillRect(alien.x, alien.y, alien.width, alien.height);
        });

        // Draw projectiles
        this.projectiles.forEach(projectile => {
            this.ctx.fillStyle = 'green';
            this.ctx.fillRect(projectile.x, projectile.y, projectile.width, projectile.height);
        });
    }
}

loadPlugin(SpaceInvadersTool);
