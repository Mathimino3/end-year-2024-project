const currentRegion = document.querySelector(".current-region").innerText;
const currentScene = document.querySelector(".current-scene").innerText;

const gameplayContainerr = document.querySelector(".gameplay-container");
//Setting the pin position
const pin = document.querySelector(".pin");

const choiceBtns = document.querySelectorAll(".choices-btn");
choiceBtns.forEach((e) => {
  //checking if the coordinates are valid/exist
  if (
    parseInt(e.children[0].innerText) >= 0 &&
    parseInt(e.children[1].innerText) >= 0
  ) {
    //Handeling the display of the pin
    e.addEventListener("mouseover", () => {
      pin.style.left = e.children[0].innerText + "%";
      pin.style.top = e.children[1].innerText + "%";
      pin.classList.remove("hidden");
    });
    e.addEventListener("mouseout", () => {
      pin.classList.add("hidden");
    });
  }
});

const img = document.querySelector(".img-canvas");
// Creating the canvas to detect some specifics locations of the images
//the canvas is at opacity 0
const canvas = document.querySelector(".layer-canvas");
const ctx = canvas.getContext("2d", { willReadFrequently: true });
gameplayContainerr.appendChild(canvas);

function checkPixelColor(x, y) {
  //fct that get the color of a pixel of an image in the cavas at given coordinates
  //the img src is set in the index
  if (img.src === "") {
    return;
  }
  //Making the canvas size = to the one of the gamplay
  canvas.width = gameplayContainerr.clientWidth;
  canvas.height = gameplayContainerr.clientHeight;

  // Drawing the image
  ctx.drawImage(
    img,
    0,
    0,
    gameplayContainerr.clientWidth,
    gameplayContainerr.clientHeight
  );

  // Getting image color data
  const imageData = ctx.getImageData(x, y, 1, 1).data;

  // getting the rgb color
  return `${imageData[0]}, ${imageData[1]}, ${imageData[2]}`;
}

gameplayContainerr.addEventListener("mousemove", function (event) {
  //geting x and y coordinates relative to the gamplay container
  const rect = gameplayContainerr.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;
  //getting the color of the pixel at the location of the pointer on the canvas image
  const color = checkPixelColor(x, y);
  switch (color) {
    case "246, 0, 255":
      console.log("breakable");
      gameplayContainerr.style.cursor = "crosshair";
      break;
    default:
      gameplayContainerr.style.cursor = "default";
      break;
  }
});
