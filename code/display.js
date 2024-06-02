const body = document.querySelector("body");
const rotationWarning = document.querySelector(".rotation-warning");

const gameplayContainer = document.querySelector(".gameplay-container");

const inventoryBtns = document.querySelectorAll(".inventory-btn");
const closeInventory = document.querySelector(".close-inventory");

const mobileSeparators = document.querySelectorAll(".mobile-separator");
const desktopSeparator = document.querySelectorAll(".desktop-sparator");

const fullscreenBtn = document.querySelector(".fullscreen-btn");
const fullscreenBtnEnter = document.querySelector(".fullscreen-enter");
const fullscreenBtnExit = document.querySelector(".fullscreen-exit");

const inventory = document.querySelector(".inventory");
const inventoryGrid = document.querySelector(".inventory-grid");

const craftGrid = document.querySelector(".craft-grid");

const fisrtChoices = document.querySelectorAll(".first-choice");
const secondChoices = document.querySelectorAll(".second-choice");
const thirdChoices = document.querySelectorAll(".third-choice");

const phase = "";
function handleOrientation() {
  if (window.screen.width >= window.screen.height) {
    //landscape
    console.log("orientation: landscape");
    rotationWarning.classList.add("hidden");
    return "landscape";
  } else {
    //portrait
    console.log("orientation: portait");
    body.style.overflow = "hidden";
    rotationWarning.classList.remove("hidden");
    return "portrait";
  }
}
handleOrientation();

window.addEventListener("orientationchange", () => {
  handleOrientation();
});

function responsiveAspectRatio() {
  let aspectRatio = window.screen.width / window.screen.height;
  console.log("aspect ratio: " + aspectRatio);
  if (window.screen.height < 600) {
    if (aspectRatio < 1.8375) {
      gameplayContainer.style.height = "65%";
    } else {
      gameplayContainer.style.height = "75%";
    }
  }
}
responsiveAspectRatio();

function mobileOrDesktop() {
  if (window.screen.height < 600) {
    //mobile
    console.log("view: mobile");

    body.style.overflow = "scroll";

    mobileSeparators.forEach((e) => e.classList.remove("hidden"));
    desktopSeparator.forEach((e) => e.classList.add("hidden"));
    inventoryBtns[1].style.display = "none";

    inventoryBtns[0].style.top = `-${inventoryBtns[0].offsetHeight + 10}px`;

    fullscreenBtn.classList.remove("hidden");

    let gameplayHeight = gameplayContainer.clientHeight;
    let gameplayWidth = gameplayHeight / (9 / 16);
    gameplayContainer.style.width = `${gameplayWidth}px`;

    mobileSeparators.forEach(
      (e) =>
        (e.style.width = `${(window.screen.width - gameplayWidth - 75) / 2}px`)
    );
  } else {
    //desktop
    console.log("view: desktop");

    body.style.overflow = "hidden";

    mobileSeparators.forEach((e) => e.classList.add("hidden"));
    desktopSeparator.forEach((e) => e.classList.remove("hidden"));
    fullscreenBtn.classList.add("hidden");

    inventoryBtns[1].style.right = `-${inventoryBtns[1].offsetHeight + 15}px`;

    let gameplayHeight = gameplayContainer.clientHeight;
    let gameplayWidth = gameplayHeight / (9 / 16);
    gameplayContainer.style.width = `${gameplayWidth}px`;

    if (
      window.screen.width <= gameplayContainer.offsetWidth + 30 &&
      handleOrientation() === "portrait"
    ) {
      gameplayContainer.style.height = "55%";
      console.log("pass");
      // mobileOrDesktop()
    }
  }
}
mobileOrDesktop();

fullscreenBtn.addEventListener("click", () => {
  if (fullscreenBtnEnter.classList.contains("hidden")) {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
    fullscreenBtnEnter.classList.remove("hidden");
    fullscreenBtnExit.classList.add("hidden");
  } else {
    if (body.requestFullscreen) {
      body.requestFullscreen();
    }
    fullscreenBtnEnter.classList.add("hidden");
    fullscreenBtnExit.classList.remove("hidden");
  }
});

//Creating the inventory btns


window.addEventListener("resize", () => {
  // window.location.reload()
});

const style = document.createElement("style");
body.appendChild(style);

function changeAmbientColor(phase) {
  if (phase === "overworld") {
    body.style.backgroundColor = "#1E283E";
    style.innerHTML = "body::backdrop{background-color: #1E283E;";
  }
  if (phase === "nether") {
    body.style.backgroundColor = "#300D0D";
    style.innerHTML = "body::backdrop{background-color: #300D0D;";
  }
  if (phase === "end") {
    body.style.backgroundColor = "#8A628A";
    style.innerHTML = "body::backdrop{background-color: #8A628A;";
  }
}

fisrtChoices.forEach((e) =>
  e.addEventListener("click", () => {
    changeAmbientColor("overworld");
  })
);
secondChoices.forEach((e) =>
  e.addEventListener("click", () => {
    changeAmbientColor("nether");
  })
);
thirdChoices.forEach((e) =>
  e.addEventListener("click", () => {
    changeAmbientColor("end");
  })
);
