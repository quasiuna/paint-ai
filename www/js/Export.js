function showExportOptions() {
  // List of supported image formats
  const formats = ["image/png", "image/jpeg", "image/webp", "image/bmp"];

  // Create a selection dialog
  let formatSelection =
    '<label>Select Image Format:</label><select id="formatSelector">';
  formats.forEach((format) => {
    formatSelection += `<option value="${format}">${format
      .split("/")[1]
      .toUpperCase()}</option>`;
  });
  formatSelection += '</select><button id="saveButton">Save</button>';
  formatSelection +=
    '<label for="transparentBackgroundCheckbox"> <input type="checkbox" id="transparentBackgroundCheckbox"> Transparent Background</label>';


  const modal = document.getElementById("exportModal");
  modal.innerHTML += `<div id="exportOptions">${formatSelection}</div>`;
  showModal("#exportModal");

  // Event listener for save button
  document.getElementById("saveButton").addEventListener("click", function () {
    saveImage(document.getElementById("formatSelector").value);
  });
}

function saveImage(format) {
  const originalCanvas = document.getElementById("paintCanvas");
  const transparentBackgroundCheckbox = document.getElementById(
    "transparentBackgroundCheckbox"
  ).checked;

  let canvasToExport = originalCanvas;

  if (!transparentBackgroundCheckbox) {
    // Create a temporary canvas
    const tempCanvas = document.createElement("canvas");
    tempCanvas.width = originalCanvas.width;
    tempCanvas.height = originalCanvas.height;
    const ctx = tempCanvas.getContext("2d");

    // Draw a white background
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);

    // Draw the original canvas content over the white background
    ctx.drawImage(originalCanvas, 0, 0);

    // Use the temporary canvas for export
    canvasToExport = tempCanvas;
  }

  const url = canvasToExport.toDataURL(format);

  // Create a temporary link to trigger the download
  const link = document.createElement("a");
  link.download = "exported-image." + format.split("/")[1];
  link.href = url;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  // Optionally, remove the export options
  document.getElementById("exportOptions").remove();
  hideModal();
}

document.getElementById("exportButton").addEventListener("click", () => {
  const exportButton = document.getElementById("saveButton");

  if (!exportButton) {
    showExportOptions();
  } else {
    showModal("#exportModal");
  }
});

document.getElementById("overlay").addEventListener("click", function () {
  hideModal();
});

function showModal(selector) {
  const el = document.querySelector(selector);

  if (el) {
    document.getElementById("overlay").style.display = "block";
    el.style.display ="block";
  }
}

function hideModal() {
  document.getElementById("overlay").style.display = "none";
  document.querySelectorAll(".modal").forEach((el) => {
    el.style.display = "none";
  });
}
