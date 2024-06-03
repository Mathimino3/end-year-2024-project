<?php
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);

//Get where the player is in the game
$currentRegion = $playerInfos["currentRegion"] === "" ? "spawn" : $playerInfos["currentRegion"];
$currentScene = $playerInfos["currentScene"] === "" ? "spawn0" : $playerInfos["currentScene"];

$regionJson = json_decode(file_get_contents("./regions/" . $currentRegion . ".json"), true);
$sceneData = $regionJson[$currentScene];


//Knowing in wich "state" the current scene is in
$sceneState = "default";
if (in_array($currentScene, $playerInfos["sceneWhereBlocksBroken"]) && in_array($currentScene, $playerInfos["sceneWhereBlocksPlaced"])) {
    $sceneState = "blocksBeenBrokenAndPlaced";
} elseif (in_array($currentScene, $playerInfos["sceneWhereBlocksBroken"])) {
    $sceneState = "blocksBeenBroken";
} elseif (in_array($currentScene, $playerInfos["sceneWhereBlocksPlaced"])) {
    $sceneState = "blocksBeenPlaced";
} elseif (isset($sceneData["varients"]) && $sceneData["varients"]) {
    //If the scenes posses somes varients
    $sceneState = "varient";
}


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
    <title>MiniCraft</title>
</head>

<body>

    <!-- mobile choices btns 1 and 2-->
    <div class="btn-separator first-btn-separator mobile-separator">

        <div class="inventory-btn  btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>

        <a href="<?= $sceneData["choices"][0]["action"] ?>" class="choices-btn btn first-choice">
            <p><?= $sceneData["choices"][0]["text"] ?></p>
        </a>
        <a href="<?= $sceneData["choices"][1]["action"] ?>" class="choices-btn btn second-choice">
            <p><?= $sceneData["choices"][1]["text"] ?></p>
        </a>
    </div>

    <div class="gameplay-container">
        <?php require_once("./partials/gameplay.php") ?>
    </div>

    <!-- mobile choices btns 3 and 4-->
    <div class="btn-separator last-btn-separator mobile-separator">
        <a href="<?= $sceneData["choices"][2]["action"] ?>" class="choices-btn btn third-choice">
            <p><?= $sceneData["choices"][2]["text"] ?></p>
        </a>
        <a href="<?= $sceneData["choices"][3]["action"] ?>" class="choices-btn btn fourth-choice">
            <p><?= $sceneData["choices"][3]["text"] ?></p>
        </a>
    </div>

    <div class="choices-btn-container">
        <!-- desktop choices btns -->
        <div class="first-btn-separator btn-separator desktop-sparator">
            <a href="<?= $sceneData["choices"][0]["action"] ?>" class="choices-btn btn first-choice">
                <p><?= $sceneData["choices"][0]["text"] ?></p>
            </a>
            <a href="<?= $sceneData["choices"][1]["action"] ?>" class="choices-btn btn second-choice">
                <p><?= $sceneData["choices"][1]["text"] ?></p>
            </a>
        </div>

        <div class="last-btn-separator btn-separator desktop-sparator">
            <a href="<?= $sceneData["choices"][2]["action"] ?>" class="choices-btn btn third-choice">
                <p><?= $sceneData["choices"][2]["text"] ?></p>
            </a>
            <a href="<?= $sceneData["choices"][3]["action"] ?>" class="choices-btn btn fourth-choice">
                <p><?= $sceneData["choices"][3]["text"] ?></p>
            </a>
        </div>

    </div>

    <div class="fullscreen-btn">
        <span class="material-symbols-outlined fullscreen-enter">fullscreen</span>
        <span class="material-symbols-outlined fullscreen-exit hidden">fullscreen_exit</span>

    </div>

    <div class="inventory storage-interface <?= isset($_GET["invOpen"]) && $_GET["invOpen"] ? null : "hidden" ?>">
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

    <!-- the item that is in the mouse -->
    <div class="item-in-mouse">
        <div class="item">
            <img src="" alt="">
            <span></span>
        </div>
    </div>

    <!-- iframe that allow the js to run php code -->
    <iframe class="php-executer" src=""></iframe>
</body>

<!-- making thoses variables accesible by the js code -->
<div class="variables hidden">
    <span class="current-region"><?= $currentRegion ?></span>
    <span class="current-scene"><?= $currentScene ?></span>

    <span class="chat-text"><?= isset($sceneData["chatText"]) ? $sceneData["chatText"] : null ?></span>

    <span class="current-choices"><?= json_encode($sceneData["choices"]) ?></span>

    <!-- <span class="player_infos"><?= json_encode($playerInfos) ?></span> -->
    <span class="have-blocks-been-broken"><?= $haveBlocksBeenBroken ?></span>
    <span class="have-blocks-been-placed"><?= $haveBlocksBeenPlaced ?></span>
</div>
<!--  -->

</html>