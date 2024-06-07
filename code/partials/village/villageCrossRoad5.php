<?php

if ($sceneState === "varient") {
    //Changing the scene image according if the visible field has been harvested
    if ($requireLocation === "gameplayImgSrc") {
        if (in_array("villageHayBale1", $playerInfos["sceneWhereBlocksBroken"])) {
            echo "Varient1";
        } else {
            echo "Varient0";
        }
    }
}
