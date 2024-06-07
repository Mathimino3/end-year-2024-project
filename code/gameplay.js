//Getting variables from the php code
const currentRegion = document.querySelector(".current-region").innerText;
const currentScene = document.querySelector(".current-scene").innerText;
const currentChoices = JSON.parse(
  document.querySelector(".current-choices").innerText
);

const haveBlocksBeenBroken =
  document.querySelector(".have-blocks-been-broken").innerText === "1"
    ? true
    : false;

const haveBlocksBeenPlaced =
  document.querySelector(".have-blocks-been-placed").innerText === "1"
    ? true
    : false;

//
const gameplayContainerr = document.querySelector(".gameplay-container");
//Setting the pin position
const pin = document.querySelector(".pin");

const choiceBtns = document.querySelectorAll(".choices-btn");
choiceBtns.forEach((e, i) => {
  //There is 8 btns but 2 verion of each one (so 4 btn * 2) : one for the mobile and one for the desktop.
  //So we don't want i to be higher than 3 but the loop still need to iterate 8 times to go trought all btns
  if (i > 3) {
    i = i - 4;
  }
  //checking if the coordinates are valid/exist
  if ("pinCoordinates" in currentChoices[i]) {
    //Handeling the display of the pin
    e.addEventListener("mouseover", () => {
      pin.style.left = currentChoices[i]["pinCoordinates"][0] + "%";
      pin.style.top = currentChoices[i]["pinCoordinates"][1] + "%";
      pin.classList.remove("hidden");
    });
    e.addEventListener("mouseout", () => {
      pin.classList.add("hidden");
    });
  }
});

const layerImg = document.querySelector(".img-layer");
// Creating the canvas to detect some specifics locations of the images
//the canvas is at opacity 0
const canvas = document.querySelector(".layer-canvas");
const ctx = canvas.getContext("2d", { willReadFrequently: true });
gameplayContainerr.appendChild(canvas);

function checkPixelColor(x, y) {
  //fct that get the color of a pixel of an image in the cavas at given coordinates
  //the img src is set in the index
  if (layerImg.src === "") {
    return;
  }
  //Making the canvas size = to the one of the gamplay
  canvas.width = gameplayContainerr.clientWidth;
  canvas.height = gameplayContainerr.clientHeight;

  // Drawing the image
  ctx.drawImage(
    layerImg,
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

const destroyAnimation = document.querySelector(".destroy-animation");
const destroyAnimationImg = document.querySelector(".destroy-animation img");
destroyAnimationImg.src = "./assets/destroy_stages/destroy_stage_0.png";

const layerOutlineBreak = document.querySelector(".layer-outline-break");
const layerOutlinePlace = document.querySelector(".layer-outline-place");

const neededItemsToPlace = document.querySelector(".needed-items-to-place");

let interaction = null;
gameplayContainerr.addEventListener("mousemove", function (e) {
  if (layerImg.src !== "") {
    //geting x and y coordinates relative to the gamplay container
    const rect = gameplayContainerr.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    //getting the color of the pixel at the location of the pointer on the canvas image
    const color = checkPixelColor(x, y);
    switch (color) {
      case "255, 0, 0":
        if (haveBlocksBeenBroken) {
          break;
        }
        //We can break this part
        interaction = "breakable";
        layerOutlineBreak.style.opacity = "1";
        break;
      case "0, 255, 0":
        if (haveBlocksBeenPlaced) {
          break;
        }
        //we can place on this part
        interaction = "placeable";
        layerOutlinePlace.style.opacity = "1";
        neededItemsToPlace.classList.remove("hidden");
        break;
      default:
        interaction = null;
        layerOutlineBreak.style.opacity = "0";
        layerOutlinePlace.style.opacity = "0";
        neededItemsToPlace.classList.add("hidden");
        break;
    }
  }
});

gameplayContainerr.addEventListener("mouseup", () => {
  switch (interaction) {
    case "breakable":
      breakBlocks();
      break;
    case "placeable":
      placeBlocks();
      break;
  }
});

function breakBlocks() {
  window.location = `router.php?action=breakBlocks&region=${currentRegion}&scene=${currentScene}`;
}

function placeBlocks() {
  window.location = `router.php?action=placeBlocks&region=${currentRegion}&scene=${currentScene}`;
}

// hiding the chat if it is empty
if (
  document.querySelector(".chat-text").innerText === "" ||
  document.querySelector(".chat-text").innerText === null
) {
  document.querySelector(".chat").style.display = "none";
}
