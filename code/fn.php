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
        $playerInfos = addItemsToInv($inventory, $playerInfos, $regionJson[$scene]["breakItems"]);
        array_push($playerInfos["sceneWhereBlocksBroken"], $scene);
        file_put_contents('./player_infos.json', json_encode($playerInfos));
    }
}

function addItemsToInv($inventory, $playerInfos, $items)
{
    //Fct that add an array of items in the inventory in the first empty cell
    $freeCellFound = false;
    foreach ($items as $item) {
        foreach ($inventory["inventory"] as $cellIndex => $cell) {
            if ($inventory["inventory"][$cellIndex]["item"] === "" && $inventory["inventory"][$cellIndex]["count"] === 0) {
                $freeCellFound = true;
                $inventory["inventory"][$cellIndex]["item"] = $item["item"];
                $inventory["inventory"][$cellIndex]["count"] = $item["count"];
                file_put_contents('./inventory/inventory.json', json_encode($inventory));
                break;
            }
        }
        //If a free cell was found => add the item to the recently obtained items array
        if ($freeCellFound) {
            array_push($playerInfos["recentlyObtainedItems"], array("item" => $item["item"], "count" => $item["count"]));
        }
        //If the inv is full => add it to the dropped items array
        else {
            array_push($playerInfos["droppedItems"], array("item" => $item["item"], "count" => $item["count"]));
        }
    }
    return $playerInfos;
}
