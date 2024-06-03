<?php

//Changing the scene image according if the visibles fields have been harvested
if ($requireLocation === "gameplayImgSrc") {
    if (in_array("villageCrops0", $playerInfos["sceneWhereBlocksBroken"])) {
        echo "Varient1";
    } else {
        echo "Varient0";
    }
}
