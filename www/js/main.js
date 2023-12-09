document.addEventListener("DOMContentLoaded", function () {
  const colorButtons = document.querySelectorAll(".color-button");
  const selectedColorButton = document.querySelector(".selected-color-button");

  // Set initial background color for each color button
  colorButtons.forEach((button) => {
    const color = button.getAttribute("data-color");
    button.style.backgroundColor = color;

    // Click event for each color button
    button.addEventListener("click", function () {
      // Remove selected class from all color buttons
      colorButtons.forEach((btn) => btn.classList.remove("selected"));

      // Add selected class to clicked button
      this.classList.add("selected");

      // Update the selected color button's background color
      selectedColorButton.style.backgroundColor =
        this.getAttribute("data-color");
    });
  });
});

function showProgress(selector) {
  const container = document.querySelector(selector);
  if (!container) {
      console.error('Selector not found');
      return;
  }

  container.style.display = 'block';
  const progressBar = document.createElement('div');
  progressBar.className = 'progress-bar';
  container.appendChild(progressBar);

  // Start the animation
  setTimeout(() => {
      progressBar.style.width = '100%';
  }, 100); // Delay to ensure the transition occurs
}

function hideProgress(selector) {
  const container = document.querySelector(selector);
  if (!container) {
      console.error('Selector not found');
      return;
  }

  container.style.display = 'none';
}

class TextFader {
  constructor(elementId, texts, duration = 30000) {
    this.texts = texts;
    this.duration = duration;
    this.element = document.getElementById(elementId);
    this.currentIndex = -1;
    this.timePerText = this.duration / this.texts.length;
    this.fadeTimeout = null;
  }

  start() {
    this.element.style.display = 'block';
    this.currentIndex = -1;
    this.updateText();
  }

  updateText() {
    this.currentIndex++;
    this.element.textContent = this.texts[this.currentIndex];
    setTimeout(() => this.updateText(), this.timePerText);
  }

  stop() {
    this.element.style.display = 'none';
  }
}

function getRandomRoboDev() {
  const names = [
      "Aurorix",
      "Bjornbot",
      "Cybergard",
      "Drakestrom",
      "Eirvik",
      "Fluxhart",
      "GrimnirX",
      "Helixbane",
      "Ironclad",
      "JotunCore",
      "Kaleidostar",
      "LokiTech",
      "Mystiron",
      "Nebulon",
      "Odinframe",
      "Pulsevalk",
      "Quantarion",
      "Runegeist",
      "SteamGrim",
      "ThorForge"
  ];
  return names[Math.floor(Math.random() * names.length)];
}


