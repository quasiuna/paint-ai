class Animator {
    constructor() {
      this.paused = false;
      this.frameCount = 0;
      this.speed = 10;
      this.minSpeed = 1;
      this.maxSpeed = 10;
    }
  
    init() {
      window.requestAnimationFrame(this.loop.bind(this));
  
      document.getElementById("pause").addEventListener("click", (e) => {
        this.pause();
      });
  
      this.setPauseButtonState();
  
      const speedSelector = document.createElement('input');
      speedSelector.type = 'range';
      speedSelector.min = this.minSpeed;
      speedSelector.max = this.maxSpeed;
      speedSelector.value = this.speed;
      speedSelector.onchange = (e) => {
          this.speed = e.target.value;
      }
  
      document.getElementById("sidebar").appendChild(speedSelector);
    }
  
    pause() {
      this.paused = !this.paused;
      this.setPauseButtonState();
      window.requestAnimationFrame(this.loop.bind(this));
    }
  
    loop() {
      if (!this.paused) {
        if (this.frameCount % (this.speed - (this.maxSpeed - 1)) === 0) {
          for (let p in plugins) {
            if (typeof plugins[p].ctx !== null) {
              plugins[p].update();
            }
          }
        }
  
        this.frameCount++;
        window.requestAnimationFrame(this.loop.bind(this));
      }
    }
  
    setPauseButtonState() {
      document.getElementById("pause").innerHTML = this.paused ? '<i class="fa fa-play"></i>' : '<i class="fa fa-pause"></i>';
    }
  }
  
  window.Animator = new Animator();
  window.Animator.init();

