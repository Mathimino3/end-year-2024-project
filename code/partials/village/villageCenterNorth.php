<?php

if ($sceneState === "varient") {
    //Changing the scene image according if the visible field has been harvested
    if ($requireLocation === "gameplayImgSrc") {
        if (in_array("villageCrops1", $playerInfos["sceneWhereBlocksBroken"])) {
            echo "Varient1";
        } else {
            echo "Varient0";
        }
    }
}

//Adding an image to the left that will remove the hay bales
if ($requireLocation === "gameplayRoot") {
    if (in_array("villageStairs0", $playerInfos["sceneWhereBlocksPlaced"])) : ?>
        <img src="../assets/gameplay_img/village/villageCenterNorthLeft.png" style="position: absolute; top: 0; left: 0; height: 100%; opacity: 1">
<?php endif;
}
