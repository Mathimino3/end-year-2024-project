<?php
$scenes = json_decode(file_get_contents("./scenes.json"), true);
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);

//Get where the player is in the game
$currentRegion = $playerInfos["currentRegion"] === "" ? "spawn" : $playerInfos["currentRegion"];
$currentScene = $playerInfos["currentScene"] === "" ? "spawn0" : $playerInfos["currentScene"];

$regionJson = json_decode(file_get_contents("./regions/" . $currentRegion . ".json"), true);
$sceneData = $regionJson[$currentScene];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="height=device-height, 
        width=device-width, initial-scale=1.0, minimum-scale=1.0, 
        maximum-scale=1.0, user-scalable=no">
    <script src="./display.js" defer></script>
    <script src="./gameplay.js" type="module" defer></script>
    <script src="./inventory/inventory.js" type="module" defer></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Game</title>
</head>

<body>
    <!-- making $currentRegion and $currentScene accesible by the js code -->
    <span class="current-region hidden"><?= $currentRegion ?></span>
    <span class="current-scene hidden"><?= $currentScene ?></span>

    <div class="btn-separator first-btn-separator mobile-separator">

        <div class="inventory-btn  btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>

        <a class="choices-btn btn first-choice">
            <p>Overworld</p>
        </a>
        <a class="choices-btn btn second-choice">
            <p>Nether</p>
        </a>
    </div>

    <div class="gameplay-container">
        <div class="inventory-btn inventory-btn btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>

        <img class="gameplay-img" src="./assets/gameplay_img/<?= $sceneData["backgroundImg"] ?>.png" alt="">
        <canvas class="layer-canvas"></canvas>
        <img class="img-canvas" src="<?= $sceneData["layerImage"] ?>">

        <div class="chat">
            <p><?= $sceneData["chatText"] ?></p>
        </div>

        <div class="pin hidden">
            <img src="./assets/img/pin.png" alt="">
        </div>
    </div>

    <div class="btn-separator last-btn-separator mobile-separator">
        <a class="choices-btn btn third-choice">
            <p>End</p>
        </a>
        <a class="choices-btn btn">
            <p>Choice 4</p>
        </a>
    </div>

    <div class="choices-btn-container">
        <!-- desktop choices btn -->
        <div class="first-btn-separator btn-separator desktop-sparator">
            <a href="<?= $sceneData["choices"][0]["action"] ?>" class="choices-btn btn first-choice">
                <!-- Storing the coodrinates of the pin in the page to get them in javascript -->
                <span class="pin-coordiates pin-x"><?= $sceneData["choices"][0]["pinCoordinates"][0] ?></span>
                <span class="pin-coordiates pin-y"><?= $sceneData["choices"][0]["pinCoordinates"][1] ?></span>

                <p><?= $sceneData["choices"][0]["text"] ?></p>
            </a>
            <a href="<?= $sceneData["choices"][1]["action"] ?>" class="choices-btn btn second-choice">
                <!-- Storing the coodrinates of the pin in the page to get them in javascript -->
                <span class="pin-coordiates pin-x"><?= $sceneData["choices"][1]["pinCoordinates"][0] ?></span>
                <span class="pin-coordiates pin-y"><?= $sceneData["choices"][1]["pinCoordinates"][1] ?></span>
                <p><?= $sceneData["choices"][1]["text"] ?></p>
            </a>
        </div>

        <div class="last-btn-separator btn-separator desktop-sparator">
            <a href="<?= $sceneData["choices"][2]["action"] ?>" class="choices-btn btn third-choice">
                <!-- Storing the coodrinates of the pin in the page to get them in javascript -->
                <span class="pin-coordiates pin-x"><?= $sceneData["choices"][2]["pinCoordinates"][0] ?></span>
                <span class="pin-coordiates pin-y"><?= $sceneData["choices"][2]["pinCoordinates"][1] ?></span>
                <p><?= $sceneData["choices"][2]["text"] ?></p>
            </a>
            <a href="<?= $sceneData["choices"][3]["action"] ?>" class="choices-btn btn">
                <!-- Storing the coodrinates of the pin in the page to get them in javascript -->
                <span class="pin-coordiates pin-x"><?= $sceneData["choices"][3]["pinCoordinates"][0] ?></span>
                <span class="pin-coordiates pin-y"><?= $sceneData["choices"][3]["pinCoordinates"][1] ?></span>
                <p><?= $sceneData["choices"][3]["text"] ?></p>
            </a>
        </div>

    </div>

    <div class="fullscreen-btn">
        <span class="material-symbols-outlined fullscreen-enter">fullscreen</span>
        <span class="material-symbols-outlined fullscreen-exit hidden">fullscreen_exit</span>

    </div>

    <div class="inventory storage-interface hidden">
        <span class="close-inventory">X</span>
        <h2 class="storage-title inventory-title">Inventory</h2>
        <div class="inventory-grid">
        </div>
        <div class="craft-grid-container">
            <h2 class="storage-title craft-title">Crafting</h2>
        </div>
    </div>

    <div class="rotation-warning">
        <span class="material-symbols-outlined">screen_rotation</span>
        <p>Change your screen's rotation</p>
    </div>

    <div class="item-in-mouse">
        <div class="item">
            <img src="" alt="">
            <span></span>
        </div>
    </div>

    <iframe class="php-executer" src=""></iframe>
</body>

</html>