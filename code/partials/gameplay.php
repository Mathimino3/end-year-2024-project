<?php $playerInfos = json_decode(file_get_contents("./player_infos.json"), true);
?>


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
    <?php if (isset($sceneData["placeItems"])) {
        foreach ($sceneData["placeItems"] as $i => $item) : ?>
            <?= $item["count"] ?>&#160
            <span class="item-name-to-format"><?= $item["item"] ?></span>
            <img src="./assets/textures/<?= str_replace("minecraft:", "", $item["item"]) ?>.png" alt="">
            <?php if ($i < count($sceneData["placeItems"]) - 2) {
                echo ",";
            } elseif ($i === count($sceneData["placeItems"]) - 2) {
                echo "&#160and";
            } ?>
    <?php endforeach;
    } ?>
    &#160to place blocks
</div>

<img class="gameplay-img" src="./assets/gameplay_img/<?php
                                                        echo $currentRegion . "/" . $currentScene;
                                                        //Check wich img to show according to the scene state
                                                        switch ($sceneState) {
                                                            case "blocksBeenBrokenAndPlaced":
                                                                echo "BrokenAndPlaced";
                                                                break;
                                                            case "blocksBeenBroken":
                                                                echo "Broken";
                                                                break;
                                                            case "blocksBeenPlaced":
                                                                echo "Placed";
                                                                break;
                                                            case "varient":
                                                                //If the scenes posses somes varients
                                                                $requireLocation = "gameplayImgSrc";
                                                                require($currentRegion . "/" . $currentScene . ".php");
                                                                break;
                                                        }
                                                        ?>.png">
<!-- The layer is where the interactable parts of the image are set -->
<canvas class="layer-canvas"></canvas>
<img class="img-layer" src="<?php if (file_exists('./assets/gameplay_img/' . $currentRegion . "/" . $currentScene . 'Layer.png')) echo './assets/gameplay_img/' . $currentRegion . "/" . $currentScene . 'Layer.png' ?>">
<!-- The outlines of the interactable parts hovering -->
<img class="layer-outline-break layer-outline" src="<?php if ($sceneState !== "blocksBeenBroken" && file_exists('./assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlineBreak.png')) echo './assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlineBreak.png' ?>">
<img class="layer-outline-place layer-outline" src="<?php if ($sceneState !== "blocksBeenPlaced" && file_exists('./assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlinePlace.png')) echo './assets/gameplay_img/' .  $currentRegion . "/" . $currentScene . 'OutlinePlace.png' ?>">

<?php if ($sceneState === "varient") {
    $requireLocation = "gameplayRoot";
    require($currentRegion . "/" . $currentScene . ".php");
} ?>

<!-- the "chat" -->
<div class="chat">
    <p><?php
        //Check wich text to show. if the blocks have been broken and place show the right text
        //if only broken show only broken   if only placed show only placed
        //else show default text ...
        if (isset($_GET["talk"]) && $_GET["talk"]) {
            echo $sceneData["chatTextInteraction"];
        } elseif (isset($sceneData["chatTextBroken"]) || isset($sceneData["chatTextPlaced"]) || isset($sceneData["chatTextBrokenAndPlaced"])) {
            switch ($sceneState) {
                case "blocksBeenBrokenAndPlaced":
                    echo $sceneData["chatTextBrokenAndPlaced"];
                    break;
                case "blocksBeenBroken":
                    echo $sceneData["chatTextBroken"];
                    break;
                case "blocksBeenPlaced":
                    echo $sceneData["chatTextPlaced"];
                    break;
            }
        } elseif (isset($sceneData["chatText"])) {
            echo $sceneData["chatText"];
        }
        ?></p>
</div>
<?php // endif; 
?>

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
            <img src="./assets/textures/<?= str_replace("minecraft:", "", $i["item"]) ?>.png" alt="">
        </div>
    <?php endforeach;
    //Creating a display for the item we recently got
    foreach ($playerInfos["recentlyObtainedItems"] as $index => $i) : ?>
        <div class="recent-items-content">
            <!-- &#160; is a blank space -->
            + <?= $i["count"] ?>&#160;
            <span class="item-name-to-format"><?= $i["item"] ?></span>
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