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
    //Fct the add the current scene in the array sceneWhereBlocksBroken and call another one to add items to the inv
    if (!in_array($scene, $playerInfos["sceneWhereBlocksBroken"])) {
        $playerInfos = addItemsToInv($inventory, $playerInfos, $regionJson[$scene]["breakItems"]);
        array_push($playerInfos["sceneWhereBlocksBroken"], $scene);
        file_put_contents('./player_infos.json', json_encode($playerInfos));
    }
}


function addItemsToInv($inventory, $playerInfos, $items, $logRecentsItems = true)
{
    //Fct that add an array of items in the inventory where it find a place
    $spaceFound = false;
    //Iterating trought every given items
    foreach ($items as $item) {
        //Iterating trought every inventory cells
        $rest = null;
        foreach ($inventory["inventory"] as $cellIndex => $cell) {
            //If the item in the cell is the same to the one we want to add and there is less than 64 item in the cell
            if ($inventory["inventory"][$cellIndex]["item"] === $item["item"] && $inventory["inventory"][$cellIndex]["count"] < 64) {
                $spaceFound = true;
                //calculating how many item will overlap from the stack
                $rest = $inventory["inventory"][$cellIndex]["count"] + $item["count"] - 64;
                //If there isn't any overflowing
                if ($rest <= 0) {
                    $inventory["inventory"][$cellIndex]["count"] = $inventory["inventory"][$cellIndex]["count"] + $item["count"];
                    file_put_contents('./inventory/inventory.json', json_encode($inventory));
                    break;
                    //If there is an overflowing
                } elseif ($rest > 0) {
                    $inventory["inventory"][$cellIndex]["count"] = 64;
                    file_put_contents('./inventory/inventory.json', json_encode($inventory));
                    //Run the function again with the rest of the items to add the overflowing ones to an other cell
                    $playerInfos = addItemsToInv($inventory, $playerInfos, [array("item" => $item["item"], "count" => $rest)], false);
                    break;
                }
            }
            //If the cell is empty
            if ($inventory["inventory"][$cellIndex]["item"] === "" && $inventory["inventory"][$cellIndex]["count"] === 0) {
                $spaceFound = true;
                $inventory["inventory"][$cellIndex]["item"] = $item["item"];
                $inventory["inventory"][$cellIndex]["count"] = $item["count"];
                file_put_contents('./inventory/inventory.json', json_encode($inventory));
                break;
            }
        }
        //If a free cell was found => add the item to the recently obtained items array
        if ($logRecentsItems) {
            if ($spaceFound) {
                array_push($playerInfos["recentlyObtainedItems"], array("item" => $item["item"], "count" => $item["count"]));
            }
            //If the inv is full => add it to the dropped items array
            else {
                array_push($playerInfos["droppedItems"], array("item" => $item["item"], "count" => $item["count"]));
            }
        }
    }
    return $playerInfos;
}


function placeBlocks($playerInfos, $inventory, $regionJson, $region, $scene)
{
    //Fct the add the current scene in the array sceneWhereBlocksPlaced and call another one to remove items from the inv

    if (!in_array($scene, $playerInfos["sceneWhereBlocksPlaced"])) {
        //Getting the response of the fonction that take items from the inv since it return an array
        $responseForRemoveItesFromInv = removeItemsFromInv($inventory, $playerInfos, $regionJson[$scene]["placeItems"]);
        $playerInfos = $responseForRemoveItesFromInv[0];
        //If the required items were found 
        if ($responseForRemoveItesFromInv[1]) {
            array_push($playerInfos["sceneWhereBlocksPlaced"], $scene);
        }
        file_put_contents('./player_infos.json', json_encode($playerInfos));
    }
}

function removeItemsFromInv($inventory, $playerInfos, $items)
{
    //Fct that remove an array of items in the inventory if they are found in it
    $done = 0;
    //Iterating trought every given items
    foreach ($items as $item) {
        $foundItems = 0;
        //The number of items we still need to find
        $NbrOfItemsToFindLeft = $item["count"];
        //Iterating trought every inventory cells
        foreach ($inventory["inventory"] as $cellIndex => $cell) {
            //If the item in the cell is = to the given one
            if ($cell["item"] === $item["item"]) {
                $foundItems = $foundItems + $cell["count"];
                //If we found the amount we needed or more for that item
                if ($foundItems >= $item["count"]) {
                    //Take that amount we need from the cell
                    $inventory["inventory"][$cellIndex]["count"] = $inventory["inventory"][$cellIndex]["count"] - $NbrOfItemsToFindLeft;
                    $done = $done + 1;
                    //Do the same with the next item
                    break;
                }
                //If we found a smaller amount than the one we needed
                //clear the cell
                $inventory["inventory"][$cellIndex]["count"] = 0;
                $inventory["inventory"][$cellIndex]["item"] = "";
                $NbrOfItemsToFindLeft = $NbrOfItemsToFindLeft - $cell["count"];
                //try with the next cell
            }
        }
    }
    //If we found every items
    if ($done === count($items)) {
        $playerInfos["recentlyLostItems"] = $items;
        file_put_contents('./inventory/inventory.json', json_encode($inventory));
    } else {
        $playerInfos["warning"] = "You can't place blocks you don't have! ";
    }
    return [$playerInfos, $done];
}
