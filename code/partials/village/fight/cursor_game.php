 <div class="cursor-game-container">
     <a href="./router.php?action=fight" class="cursor-gale-close-btn">X</a>
     <span class="cursor-game-speed"><?= $mobs[$sceneData["mob"]]["speed"] ?></span>
     <div class="mob-health"><?= $mobs[$sceneData["mob"]]["name"] . ": " . $mobs[$sceneData["mob"]]["pv"] ?> <img src="./assets/img/heart.png"></div>
     <img class="mob-img" src="./assets/img/iron_golem.png" alt="Leave the fight">

     <div class=" cursor-game-rect">
         <div class="cursor-game-cursor"></div>
         <div class="zone-out-cursor-game zone-cursor-game ">
             <div class="zone-in-cursor-game zone-cursor-game "></div>
         </div>
     </div>
 </div>
 <style>
     .cursor-gale-close-btn {
         position: absolute;
         top: 2%;
         left: 2%;
         background-color: red;
         width: 3%;
         aspect-ratio: 1/1;
         display: flex;
         align-items: center;
         justify-content: center;
         border: solid 1px #fff;
         border-radius: 10px;
     }

     .mob-health {
         margin: 3px;
         display: flex;
         align-items: center;
         justify-content: center;
     }

     .mob-health img {
         height: 100%;
         margin: 3px;
     }

     .cursor-game-container {
         height: 100%;
         width: 100%;
         position: absolute;
         top: 0;
         left: 0;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
     }

     .mob-img {
         height: 55%;
     }

     .cursor-game-rect {
         position: relative;
         background-color: var(--inventory-color);
         height: 15%;
         width: 90%;
         border: solid 3px #fff;
         border-radius: 10px;
         overflow: hidden;
     }

     .cursor-game-cursor {
         background-color: #fff;
         position: absolute;
         left: 0;
         height: 100%;
         width: 1%;
         z-index: 5;
         transition: left .1;
     }

     .zone-cursor-game {
         height: 100%;
         position: absolute;
         top: 0;
     }

     .zone-in-cursor-game {
         background-color: rgb(0, 255, 0);
     }

     .zone-out-cursor-game {
         background-color: rgb(255, 150, 0);
     }

     .choices-btn {
         display: none;
     }
 </style>

 <script>
     const zones = document.querySelectorAll(".zone-cursor-game")
     const zonesPos = [];
     zones.forEach((zone, zoneIndex) => {
         const width = zoneIndex === 0 ? randomIntFromInterval(10, 25) : randomIntFromInterval(25, 40);
         zone.style.width = `${width}%`;
         const left = randomIntFromInterval(width + 5, 95) - width;
         zone.style.left = `${left}%`;
         zonesPos[zoneIndex] = [left, left + width];
     })
     console.log(zonesPos);

     const cursor = document.querySelector(".cursor-game-cursor");
     //moving the cursor every x time
     //  cursorMoveInterval = setInterval(moveCursor, 50);
     let goRight = true;
     const speed = parseInt(document.querySelector(".cursor-game-speed").innerText);

     function moveCursor() {
         //fct that moves the cursor
         let currentPos = parseInt(cursor.style.left) || 0;
         let newPos = 0
         //if the cursor if all the way to the right,make it go left
         if (currentPos >= 100) {
             goRight = false;
         }
         //if the cursor if all the way to the left, make it go right
         if (currentPos <= 0) {
             goRight = true;
         }
         newPos = goRight ? currentPos + speed : currentPos - speed;
         cursor.style.left = `${newPos}%`;
     }


     window.addEventListener("keydown", (e) => {
         if (e.keyCode === 32) {
             clearInterval(cursorMoveInterval);
             const cursorPos = parseInt(cursor.style.left) || 0;
             zones.forEach((zone, zoneIndex) => {
                 if (cursorPos > zonesPos[zoneIndex][0] && cursorPos < zonesPos[zoneIndex][1]) {
                     if (zoneIndex === 0) {
                         console.log("perfect");
                     } else {
                         console.log("W");
                     }
                 } else {
                     console.log("L");
                 }
             })
         }
     })

     function randomIntFromInterval(min, max) { // min and max included 
         return Math.floor(Math.random() * (max - min + 1) + min);
     }
 </script>