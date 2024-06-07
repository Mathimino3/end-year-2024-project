<?php

if ($sceneState === "varient") {
    //Changing the scene image according if the visible field has been harvested
    if ($requireLocation === "gameplayImgSrc") {
        if (in_array("villageCrops0", $playerInfos["sceneWhereBlocksBroken"])) {
            echo "Varient1";
        } else {
            echo "Varient0";
        }
    }
}

//Creating the iron golem btn
if ($requireLocation === "gameplayRoot" && isset($sceneData["mob"]) && $sceneData["mob"] === "iron_golem" && !in_array("iron_golem", $playerInfos["defetedMobs"]) && !$playerInfos["mobFight"]) {
    require_once('iron_golem_looking.php');
}
