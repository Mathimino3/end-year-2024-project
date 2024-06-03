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
