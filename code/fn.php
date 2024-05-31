<?php

function changeScene($playerInfos, $region, $scene)
{
    //Fct that change the scene to a specified one
    $playerInfos["currentRegion"] = $region;
    $playerInfos["currentScene"] = $scene;
    file_put_contents('./player_infos.json', json_encode($playerInfos));
}

function breakBlocks($playerInfos, $inventory, $regionJson, $region, $scene)
{
    //Fct the add the current scene in the array sceneWhereBlocksBroken 
    if (!in_array($scene, $playerInfos["sceneWhereBlocksBroken"])) {
        $playerInfos = addItemToInv($inventory, $playerInfos, $regionJson[$scene]["breakItems"]);
        array_push($playerInfos["sceneWhereBlocksBroken"], $scene);
        file_put_contents('./player_infos.json', json_encode($playerInfos));
    }
}

function addItemToInv($inventory, $playerInfos, $item)
{
    //Fct that add an item in the inventory in the first empty cell
    $freeCellFound = false;
    foreach ($inventory["inventory"] as $cellIndex => $cell) {
        if ($inventory["inventory"][$cellIndex]["item"] === "" && $inventory["inventory"][$cellIndex]["count"] === 0) {
            $freeCellFound = true;
            $inventory["inventory"][$cellIndex]["item"] = $item["item"];
            $inventory["inventory"][$cellIndex]["count"] = $item["count"];
            file_put_contents('./inventory/inventory.json', json_encode($inventory));
            break;
        }
    }
    if ($freeCellFound) {
        array_push($playerInfos["recentlyObtainedItems"], array("item" => $item["item"], "count" => $item["count"]));
        return $playerInfos;
    }
    array_push($playerInfos["droppedItems"], array("item" => $item["item"], "count" => $item["count"]));
    return $playerInfos;
}
