<?php

function changeScene($playerInfos, $region, $scene)
{
    $playerInfos["currentRegion"] = $region;
    $playerInfos["currentScene"] = $scene;
    file_put_contents('./player_infos.json', json_encode($playerInfos));
}

function breakBlocks($playerInfos, $region, $scene)
{
    if (!in_array($scene, $playerInfos["sceneWhereBlocksBroken"])) {
        array_push($playerInfos["sceneWhereBlocksBroken"], $scene);
    }
    file_put_contents('./player_infos.json', json_encode($playerInfos));
}
