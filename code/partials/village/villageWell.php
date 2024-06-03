<?php

//Changing the scene image according to wich fields have been harvested
if ($requireLocation === "gameplayImgSrc") {
    if (in_array("villageCrops0", $playerInfos["sceneWhereBlocksBroken"]) && in_array("villageCrops1", $playerInfos["sceneWhereBlocksBroken"])) {
        echo "Varient3";
    } elseif (in_array("villageCrops1", $playerInfos["sceneWhereBlocksBroken"])) {
        echo "Varient2";
    } elseif (in_array("villageCrops0", $playerInfos["sceneWhereBlocksBroken"])) {
        echo "Varient1";
    } else {
        echo "Varient0";
    }
}
if ($requireLocation === "gameplayRoot") {
    if (in_array("villageHayBayle2", $playerInfos["sceneWhereBlocksBroken"])) : ?>
        <img src="../assets/gameplay_img/village/villageWellLeftVarient0.png" style="position: absolute; top: 0; left: 0; height: 100%; opacity: 1">
<?php endif;
} ?>