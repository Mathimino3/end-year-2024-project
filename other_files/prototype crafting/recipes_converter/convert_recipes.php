<?php
//This code was only used onece to generate the .json file with all the datas regarding crafting. 
//It's not part of the game.

$recipesBook = json_decode(file_get_contents("./recipes_converter/1_20_4recipes.json"), true);
$output = json_decode(file_get_contents("./recipes_converter/output.json"), true);

function formatPlayerPattern($mcItem, $recipesBook)
{
    $pattern = $mcItem["pattern"];
    $newPattern = [[], [], []];
    for ($rowNbr = 0; $rowNbr < 3; $rowNbr++) { //iterating through the rows
        if (isset($pattern[$rowNbr])) {
            $row = $pattern[$rowNbr];
            for ($j = 0; $j < 3; $j++) { //iterating through the items of the row 
                if (!isset($row[$j])) { //if the item doesn't exist => create one with just a white space (because it doesn't work with "")
                    $row[$j] = " ";
                }
                array_push($newPattern[$rowNbr],  $row[$j]);
            }
            foreach ($newPattern[$rowNbr] as $n => $newRow) { // removing the whitesspaces
                if ($newPattern[$rowNbr][$n] === " ") {
                    $newPattern[$rowNbr][$n] = "";
                }
            }
        } else { // if the row doesn't exist => creating an empty one
            $newPattern[$rowNbr] = ["", "", ""];
        }
    }
    //Now the  pattern is formated but we still need to replace each keys with its values
    $keys = $mcItem["key"];
    $keyNames = array_keys($mcItem["key"]);
    foreach ($newPattern as $rowNbr => $row) {
        //checking for each charcter if they are === to a key key ($keyNames). 
        // If yes => replace it by the value of that key. If it's a white space => replace itt with " ;"
        foreach ($row as $i => $rowItem) {
            foreach ($keyNames as $keyName) {
                if ($rowItem === $keyName && isset($rowItem)) {
                    if (isset($keys[$keyName]["item"])) {
                        $newPattern[$rowNbr][$i] = $keys[$keyName]["item"];
                    } elseif (isset($keys[$keyName]["tag"])) {
                        $newPattern[$rowNbr][$i] = array("tag" => $keys[$keyName]["tag"]);
                    }
                }
            }
        }
    }
    return $newPattern;
}

$dataToExport = array();
foreach ($recipesBook as $i => $mcItem) {
    // print_r($mcItem["result"]["item"]);
    $object = array();
    $objectData = array();
    $type = array("type" => $recipesBook[$i]["type"]);
    $result = (isset($recipesBook[$i]["result"])) ? array("result" => $recipesBook[$i]["result"]) : null;
    if ($recipesBook[$i]["type"] === "minecraft:crafting_shaped") {
        $newPattern = array("pattern" => formatPlayerPattern($mcItem, $recipesBook));
        $objectData = array_merge($type, $newPattern, $result);
        // print_r("pass_shaped ");
    } elseif ($recipesBook[$i]["type"] === "minecraft:crafting_shapeless") {
        $ingredients = array("ingredients" => $recipesBook[$i]["ingredients"]);
        $objectData = array_merge($type, $ingredients, $result);
        // print_r("pass_shapeless ");
    } else {
        continue; //If not a minecraft:crafting_shaped or a minecraft:crafting_shapeless => don't add it
    }
    $object = array($i => $objectData);
    $dataToExport =  array_merge($dataToExport, $object);
}
file_put_contents('./recipes_converter/output.json', json_encode($dataToExport));





// function formatPlayerPattern($playerPattern, $craft)
// {
//     //Formating the player's pattern to a different structure to match with the formated minecraft's pattern
//     $newPlayerPattern = [];
//     foreach ($playerPattern as $row) {
//         if ($row !== ["", "", ""]) { //if the row is empty => don't do anything
//             $rowString = "";
//             foreach ($row as $i => $rowItem) { // itrerating trough the items in the row
//                 if ($rowItem !== "") { //if it's not an empty string
//                     $rowString .= $rowItem . ";";
//                     //if there isn't any value at the end of the row ("") => stop the row
//                     if (isset($row[$i + 1]) && $row[$i + 1] == "") {
//                         if ($i === 1) {
//                             break;
//                         } elseif ($i === 0 && $row[$i + 2] === "") {
//                             break;
//                         }
//                     }
//                 } else { //if it is an empty string
//                     $rowString .= " ;";
//                 }
//             }
//             array_push($newPlayerPattern, $rowString);
//         }
//     }
//     $craft["new-table"][0] = $newPlayerPattern;
//     file_put_contents("craft.json", json_encode($craft));
//     return $newPlayerPattern;
// }
// formatPlayerPattern($craft["table"], $craft);


// function formatRecipePattern($mcItem)
// {
//     $keys = $mcItem["key"];
//     // print_r($keys);
//     $keyNames = array_keys($mcItem["key"]);
//     // print_r($keyNames);
//     $recipePattern = $mcItem["pattern"];
//     $newRecipePattern = [];
//     foreach ($recipePattern as $row) { //($row is a string)
//         //checking for each charcter if they are === to a key key ($keyNames). 
//         // If yes => replace it by the value of that key. If it's a white space => replace itt with " ;"
//         for ($i = 0; $i < strlen($row); $i++) {
//             foreach ($keyNames as $keyName) {
//                 if ($row[$i] === $keyName) {
//                     $row = str_replace($row[$i], $keys[$keyName]["item"] . ";", $row);
//                 } elseif ($row[$i] === " ") {
//                     $row = str_replace($row[$i], " ;", $row);
//                 }
//             }
//         }
//         array_push($newRecipePattern, $row);
//     }
//     return $newRecipePattern;
// }
// formatRecipePattern($recipesBook["minecraft:acacia_boat"]);
