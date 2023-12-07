const canvas = document.getElementById("paintCanvas");
const ctx = canvas.getContext("2d");

const observer = new ResizeObserver((entries) => {
  canvas.width = canvas.clientWidth;
  canvas.height = canvas.clientHeight;
});
observer.observe(canvas);
