<?php

function changeScene($playerInfos, $regionJson, $region, $scene, $die = null, $condition = null, $choice = null)
{
    $continue = true;
    if ($condition !== null && $choice !== null) {
        print_r($regionJson[$playerInfos["currentScene"]]["choices"][$choice]["condition"]);
        eval($regionJson[$playerInfos["currentScene"]]["choices"][$choice]["condition"]);
        if ($continue === false) {
            $playerInfos["warning"] = "You can't go there yet!";
            file_put_contents('./player_infos.json', json_encode($playerInfos));
            return;
        }
    }
    //if we need to kill the player
    if ($die) {
        $playerInfos = playerDie($playerInfos);
    }
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


function placeBlocks($playerInfos, $inventory, $regionJson, $scene)
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


function attack($inventory, $playerInfos, $mobs, $damage, $scene)
{
    //Fct that remove pvs from the mob we are currently fighting
    $playerInfos["mobPv"] = $playerInfos["mobPv"] - $damage;
    if ($playerInfos["mobPv"] <= 0) {
        //If the mob is dead
        array_push($playerInfos["sceneFighted"], $scene);
        array_push($playerInfos["defetedMobs"], $playerInfos["mob"]);
        $playerInfos["mobFight"] = false;
        $playerInfos["mobPv"] = null;
        $playerInfos = addItemsToInv($inventory, $playerInfos, $mobs[$playerInfos["mob"]]["killItems"], true);
        $playerInfos["mob"] = "";
    }
    file_put_contents('./player_infos.json', json_encode($playerInfos));
}

function getAttacked($playerInfos, $mobs, $damage, $scene)
{
    //Fct that remove pvs from the player
    $playerInfos["playerPv"] = $playerInfos["playerPv"] - $damage;
    if ($playerInfos["playerPv"] <= 0) {
        //If we are dead
        $playerInfos["playerPv"] = 0;
        $playerInfos["mob"] = "";
        $playerInfos["mobPv"] = null;
        $playerInfos["mobFight"] = false;
        $playerInfos = playerDie($playerInfos);
    }
    file_put_contents('./player_infos.json', json_encode($playerInfos));
}

function playerDie($playerInfos)
{
    //Fct taht kill the player
    $playerInfos["playerDead"] = true;
    return $playerInfos;
}

function resetAll($playerInfos, $inventory)
{
    //fct that put the game back to his original state

    //reset player_infos.json
    $playerInfos = array(
        "currentRegion" => "",
        "currentScene" => "",
        "sceneWhereBlocksBroken" => [],
        "sceneWhereBlocksPlaced" => [],
        "recentlyObtainedItems" => [],
        "recentlyLostItems" => [],
        "droppedItems" => [],
        "playerPv" => 20,
        "playerDead" => false,
        "warning" => "",
        "mobFight" => false,
        "mob" => "",
        "mobPv" => null,
        "sceneFighted" => [],
        "defetedMobs" => []
    );

    //reseting inventory.json
    $inventory = array(
        "inventory" => [
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0),
            array("item" => "", "count" => 0)
        ],
        "mouse" => array("item" => "", "count" => 0),
        "craftTable" => [
            [
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0)
            ],
            [
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0)
            ],
            [
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0),
                array("item" => "", "count" => 0)
            ]
        ],
        "resultCell" => array("0" => array("item" => "", "count" => 0), "item" => "", "count" => 0)
    );
    //Clearing the inventory
    // foreach ($inventory["inventory"] as $cellIndex => $cell) {
    //     if ($cell["item"] !== null || $cell["count"] !== 0) {
    //         $inventory["inventory"][$cellIndex]["item"] = "";
    //         $inventory["inventory"][$cellIndex]["count"] = 0;
    //     }
    // }

    // //Clearing the crafting table
    // foreach ($inventory["craftTable"] as $rowNbr => $row) {
    //     foreach ($row as $i => $rowItem)
    //         if ($rowItem["item"] !== null || $rowItem["count"] !== 0) {
    //             $inventory["craftTable"][$rowNbr][$i]["item"] = "";
    //             $inventory["craftTable"][$rowNbr][$i]["count"] = 0;
    //         }
    // }

    // //Clearing the mouse
    // $inventory["mouse"]["item"] = "";
    // $inventory["mouse"]["count"] = 0;

    // //Clearing the result cell
    // $inventory["resultCell"]["item"] = "";
    // $inventory["resultCell"]["count"] = 0;

    file_put_contents('./player_infos.json', json_encode($playerInfos));
    file_put_contents('./inventory/inventory.json', json_encode($inventory));
}
