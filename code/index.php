<?php
$scenes = json_decode(file_get_contents("./scenes.json"), true);
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);

//Get where the player is in the game
$currentRegion = $playerInfos["currentRegion"] === "" ? "spawn" : $playerInfos["currentRegion"];
$currentScene = $playerInfos["currentScene"] === "" ? "spawn0" : $playerInfos["currentScene"];

$regionJson = json_decode(file_get_contents("./regions/" . $currentRegion . ".json"), true);
$sceneData = $regionJson[$currentScene];


//Have the blocks that could be broken broken?
$haveBlocksBeenBroken = in_array($currentScene, $playerInfos["sceneWhereBlocksBroken"]) ? true : false;
//Check if the current scene is in the list of the ones where the blocks have been broken

//Have the blocks that could be placed placed?
$haveBlocksBeenPlaced = in_array($currentScene, $playerInfos["sceneWhereBlocksPlaced"]) ? true : false;
//Check if the current scene is in the list of the ones where the blocks have been placed

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
        <span class="gameplay-warning"><?= $playerInfos["warning"] ?></span>
        <?php $playerInfos["warning"] = "";
        ?>
        <div class="inventory-btn inventory-btn btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>
        <!-- Display a list of the items you need to place blocks in this scene. Shown when hovering a placeable part-->
        <!-- &#160; is a blank space -->
        <div class="needed-items-to-place hidden">
            You need
            <?php foreach ($sceneData["placeItems"] as $i => $item) : ?>
                <?= $item["count"] ?>&#160
                <span class="item-name-to-format"><?= $item["item"] ?></span><?= $item["count"] > 1 ? "s" : "" ?>
                <img src="./assets/textures/<?= str_replace("minecraft:", "", $item["item"]) ?>.png" alt="">
                <?php if ($i < count($sceneData["placeItems"]) - 2) {
                    echo ",";
                } elseif ($i === count($sceneData["placeItems"]) - 2) {
                    echo "&#160and";
                } ?>
            <?php endforeach; ?>
            &#160to place blocks
        </div>

        <img class="gameplay-img" src="./assets/gameplay_img/<?php
                                                                //Check wich img to show. if the blocks have been broken and place show the right img
                                                                //if only broken show only broken   if only placed show only placed
                                                                //else show default background
                                                                if ($haveBlocksBeenBroken && $haveBlocksBeenPlaced) echo $currentRegion . "/" . $currentScene . "BrokenAndPlaced";
                                                                elseif ($haveBlocksBeenBroken) echo $currentRegion . "/" . $currentScene . "Broken";
                                                                elseif ($haveBlocksBeenPlaced) echo $currentRegion . "/" . $currentScene . "Placed";
                                                                else echo $currentRegion . "/" . $currentScene ?>.png">
        <!-- The layer is where the interactable parts of the image are set -->
        <canvas class="layer-canvas"></canvas>
        <img class="img-layer" src="<?php if (file_exists('./assets/gameplay_img/' . $currentRegion . "/" . $currentScene . 'Layer.png')) echo './assets/gameplay_img/' . $currentRegion . "/" . $currentScene . 'Layer.png' ?>">
        <!-- The outlines of the interactable parts hovering -->
        <img class="layer-outline-break layer-outline" src="<?php if (!$haveBlocksBeenBroken) echo './assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlineBreak.png' ?>" alt="">
        <img class="layer-outline-place layer-outline" src="<?php if (!$haveBlocksBeenPlaced) echo './assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlinePlace.png' ?>" alt="">

        <!-- the "chat" -->

        <div class="chat">
            <p><?php
                //Check wich text to show. if the blocks have been broken and place show the right text
                //if only broken show only broken   if only placed show only placed
                //else show default text
                if ($haveBlocksBeenBroken && $haveBlocksBeenPlaced && isset($sceneData["chatTextBrokenAndPlaced"])) echo $sceneData["chatTextBrokenAndPlaced"];
                elseif ($haveBlocksBeenBroken && isset($sceneData["chatTextBroken"])) echo $sceneData["chatTextBroken"];
                elseif ($haveBlocksBeenPlaced && isset($sceneData["chatTextPlaced"])) echo $sceneData["chatTextPlaced"];
                elseif (isset($sceneData["chatText"])) echo $sceneData["chatText"]
                ?></p>
        </div>

        <!-- the location pins that can appear while hovering choices -->
        <div class="pin hidden">
            <img src="./assets/img/pin.png" alt="">
        </div>
        <div class="recent-items">
            <!-- Creating a display for the item we recently lost -->
            <?php foreach ($playerInfos["recentlyLostItems"] as $index => $i) : ?>
                <div class="recent-items-content">
                    <!-- &#160; is a blank space -->
                    - <?= $i["count"] ?>&#160;
                    <span class="item-name-to-format"><?= $i["item"] ?></span>
                    <?= $i["count"] > 1 ? "s" : "" ?>&#160;
                    <img src="./assets/textures/<?= str_replace("minecraft:", "", $i["item"]) ?>.png" alt="">
                </div>
            <?php endforeach;
            //Creating a display for the item we recently got
            foreach ($playerInfos["recentlyObtainedItems"] as $index => $i) : ?>
                <div class="recent-items-content">
                    <!-- &#160; is a blank space -->
                    + <?= $i["count"] ?>&#160;
                    <span class="item-name-to-format"><?= $i["item"] ?></span>
                    <?= $i["count"] > 1 ? "s" : "" ?>&#160;
                    <img src="./assets/textures/<?= str_replace("minecraft:", "", $i["item"]) ?>.png" alt="">
                </div>
            <?php
            endforeach;
            //Clearing the recently obtained items after displaying them
            $playerInfos["recentlyLostItems"] = [];
            $playerInfos["recentlyObtainedItems"] = [];
            file_put_contents('./player_infos.json', json_encode($playerInfos));
            ?>
        </div>

        <div class="destroy-animation hidden">
            <img src="" alt="">
        </div>

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

    <span class="current-choices"><?= json_encode($sceneData["choices"]) ?></span>

    <!-- <span class="player_infos"><?= json_encode($playerInfos) ?></span> -->
    <span class="have-blocks-been-broken"><?= $haveBlocksBeenBroken ?></span>
    <span class="have-blocks-been-placed"><?= $haveBlocksBeenPlaced ?></span>
</div>
<!--  -->

</html>