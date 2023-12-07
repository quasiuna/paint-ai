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

function fadeIdea() {
  const ideas = document.querySelectorAll('.ideas .idea');
  let currentIdea = null;

  function getRandomIdea() {
      if (ideas.length === 0) return null;
      return ideas[Math.floor(Math.random() * ideas.length)];
  }

  function changeIdea() {
      if (currentIdea) {
          currentIdea.style.display = 'none';
      }
      const newIdea = getRandomIdea();
      
      if (newIdea !== currentIdea) {
          newIdea.style.display = 'block';
          currentIdea = newIdea;
      }
  }

  changeIdea();
  setInterval(changeIdea, 3000);
}
