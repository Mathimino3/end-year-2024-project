 <div class="cursor-game-container">
     <div class="mob-health"><?= $mobs[$sceneData["mob"]]["name"] . ": " . $mobs[$sceneData["mob"]]["pv"] ?></div>
     <img class="mob-img" src="./assets/img/iron_golem.png"">

     <div class=" cursor-game-rect">
     <div class="cursor-game-cursor"></div>
     <div class="green-zone-cursor-game zone-cursor-game "></div>
 </div>
 </div>
 <script>
     const cursor = document.querySelector(".cursor-game-cursor");
     //moving the cursor every x time
     setInterval(moveCursor, 50);
     let goRight = true;

     function moveCursor() {
         //fct that moves the cursor
         let currentPos = parseInt(cursor.style.left) || 0;
         let newPos = 0
         //if the cursor if all the way to the right,make it go left
         if (currentPos > 100) {
             goRight = false;
         }
         //if the cursor if all the way to the left, make it go right
         if (currentPos < 0) {
             goRight = true;
         }
         newPos = goRight ? currentPos + speed : currentPos - speed;
         cursor.style.left = `${newPos}%`;
     }
 </script>

 <style>
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
         transition: left 0.1s;
     }

     green-zone-cursor-game {
         background-color: rgb(0, 255, 0);
         height: 100px;
         width: 10px;
         position: absolute;
         top: 0;
         left: 0;
     }

     .choices-btn {
         display: none;
     }
 </style>