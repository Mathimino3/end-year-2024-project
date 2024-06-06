<!-- making variables accesibles in js -->
<div class="hidden">
    <span class="current-scene hidden"><?= $currentScene ?></span>
    <span class="cursor-game-speed hidden"><?= $mobs[$sceneData["mob"]]["speed"] ?></span>
    <span class="mob-damages hidden"><?= $mobs[$sceneData["mob"]]["damage"] ?></span>
    <span class="mob-name hidden"><?= $mobs[$sceneData["mob"]]["name"] ?></span>
</div>

<div class="cursor-game-container">
    <?php
    $playerInfos["mob"] = $sceneData["mob"];
    if ($playerInfos["mobPv"] === null) {
        $playerInfos["mobPv"] = $mobs[$sceneData["mob"]]["pv"];
    }
    file_put_contents('./player_infos.json', json_encode($playerInfos));
    ?>
    <a href="./router.php?action=fighting" class="cursor-game-close-btn">X</a>

    <div class="mob-health health"><?= $mobs[$sceneData["mob"]]["name"] . ": " . $playerInfos["mobPv"] ?> <img src="./assets/img/heart.png"></div>
    <img class="mob-img" src="./assets/mobs/<?= $sceneData["mob"] ?>.png" alt="Leave the fight">

    <div class=" cursor-game-rect">
        <div class="cursor-game-cursor"></div>
        <div class="zone-out-cursor-game zone-cursor-game ">
            <div class="zone-in-cursor-game zone-cursor-game "></div>
        </div>
    </div>

</div>
<style>
    .cursor-game-close-btn {
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
        font-size: 1.2rem;
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
        margin: 3% 0;
    }

    .cursor-game-cursor {
        background-color: #fff;
        position: absolute;
        left: 0;
        height: 100%;
        width: 1%;
        z-index: 5;
        /* transition: left .1; */
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
        background-color: #ef3926;
    }

    .choices-btn {
        display: none;
    }
</style>

<script>
    const speed = parseInt(document.querySelector(".cursor-game-speed").innerText);
    const mobDamage = parseInt(document.querySelector(".mob-damages").innerText);
    const currentScene = document.querySelector(".current-scene").innerText;


    const zones = document.querySelectorAll(".zone-cursor-game")
    const zonesPos = [];
    zones.forEach((zone, zoneIndex) => {
        const width = zoneIndex === 0 ? randomIntFromInterval(15, 30) : randomIntFromInterval(25, 40);
        zone.style.width = `${width}%`;
        const left = randomIntFromInterval(width + 5, 95) - width;
        zone.style.left = `${left}%`;
        zonesPos[zoneIndex] = [left, left + width];
    })

    const cursor = document.querySelector(".cursor-game-cursor");
    //moving the cursor every x time
    cursorMoveInterval = setInterval(moveCursor, 50);
    let goRight = true;


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
            stopCursor();
        }
    })

    function stopCursor() {
        //Stop the cursor
        clearInterval(cursorMoveInterval);
        //cursor position
        const cursorPos = parseInt(cursor.style.left) || 0;
        //if the cursor is in the outer zone
        if (cursorPos > zonesPos[0][0] && cursorPos < zonesPos[0][1]) {
            //if it also is in the inner zone
            if (cursorPos > zonesPos[1][0] && cursorPos < zonesPos[1][1]) {
                console.log("perfect hit");
                attack(7);

            } else {
                console.log("hit");
                attack(4);
            }
        } else {
            getAttacked(mobDamage);
        }
    }

    function attack(damage) {
        window.location = `./router.php?action=fight&fightAction=attack&damage=${damage}&scene=${currentScene}`
    }

    function getAttacked(damage) {
        window.location = `./router.php?action=fight&fightAction=getAttacked&damage=${damage}&scene=${currentScene}`
    }

    function randomIntFromInterval(min, max) { // min and max included 
        return Math.floor(Math.random() * (max - min + 1) + min);
    }
</script>