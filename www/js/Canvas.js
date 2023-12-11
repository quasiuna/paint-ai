const canvas = document.getElementById("paintCanvas");
const ctx = canvas.getContext("2d");

let savedImageData; // To store the image data

const saveCanvasImage = () => {
  savedImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
};

const redrawCanvasImage = () => {
  if (savedImageData) {
    ctx.putImageData(savedImageData, 0, 0);
  }
};

// Save the canvas content before resizing
window.addEventListener("resize", saveCanvasImage);

const observer = new ResizeObserver((entries) => {
  // Save the current image data before resizing
  saveCanvasImage();

  // Resize the canvas
  canvas.width = canvas.clientWidth;
  canvas.height = canvas.clientHeight;

  // Redraw the image data after resizing
  redrawCanvasImage();
});

observer.observe(canvas);
