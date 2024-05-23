<?php

$craft = json_decode(file_get_contents("craft.json"), true);
$recipesBook = json_decode(file_get_contents("recipes.json"), true);
$tags = json_decode(file_get_contents("tags.json"), true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Craft</title>
</head>

<body>

    <div class="craft-table">
        <?php for ($rowNbr = 0; $rowNbr < 3; $rowNbr++) :
            // iterating through the 3 row of the crafting table
            $row = $craft["table"][$rowNbr];
            // checking if the row exist. If not => creating an empty one so that 9 slots are shown
            if (!isset($craft["table"][$rowNbr])) {
                array_push($craft["table"], ["", "", ""]);
                file_put_contents('craft.json', json_encode($craft));
            }
            //creating the slots
            foreach ($craft["table"][$rowNbr] as $i => $mcItem) : ?>
                <div class="slot"><img src="./assets/textures/<?php echo str_replace("minecraft:", "", $craft["table"][$rowNbr][$i]) ?>.png" alt=""></div>
        <?php endforeach;
        endfor ?>
    </div>

    <div class="result slot"><img src="
    <?php
    if (checkCraft($craft, $recipesBook, $tags) !== "null") :
        $result = checkCraft($craft, $recipesBook, $tags);
        if (file_exists('./assets/textures/' . $result . '.png')) {
            echo './assets/textures/' . $result . '.png';
        } elseif (file_exists('./assets/textures/' . $result . '_front.png')) {
            echo './assets/textures/' . $result . '_front.png';
        } else {
            echo './assets/textures/missing_texture.png';
        }

    ?>" alt=""><span><?php echo $result;
                    endif; ?></span></div>
</body>

</html>

<?php
function checkCraft($craft, $recipesBook, $tags)
{
    $itemsInTheTable = itemsInTable($craft);
    foreach ($recipesBook as $i => $mcItem) {
        $nbrOfItemsInTable = nbrOfItemsInTable($craft);

        $nbrOfIgredients = null; //The number of ingredients in the shapeless recipe
        if (isset($mcItem["ingredients"])) {
            $nbrOfIgredients = count($mcItem["ingredients"]);
        }

        $isMatch = "null";
        //If the recipe use a pattern (shaped)
        if ($mcItem["type"] === "minecraft:crafting_shaped") {
            $recipePattern = $mcItem["pattern"];
            //Iterating trough every items of the recipe pattern
            foreach ($recipePattern as $rowNbr => $row) {
                foreach ($row as $j => $rowItem) {
                    if ($rowItem === $craft["table"][$rowNbr][$j]) { //If the item from the recipe is === to the one in the craft => 
                        continue; //check the next one
                    } elseif (gettype($rowItem) === "array" && isset($rowItem["tag"])) { //If the item is a tag  => 
                        if (in_array($craft["table"][$rowNbr][$j], $tags[$rowItem["tag"]]["values"])) { //check if the item in the crafting table is in the array of that tag
                            continue; //check the next one
                        } else {
                            $isMatch = false; //if not =>
                            break 2; //try with the next recipe
                        }
                    } else {
                        $isMatch = false; //if not =>
                        break 2; //try with the next recipe
                    }
                }
            }
            //break 2; lead to here 
            if ($isMatch !== false) { //when we've check every items and they are all === 
                return str_replace("minecraft:", "", $mcItem["result"]["item"]);
            }
        }
        //If the recipe doesn't use a pattern (shapeless) and if there is as much items in the table as ingredients in the recipe
        elseif ($mcItem["type"] === "minecraft:crafting_shapeless" && $nbrOfIgredients === $nbrOfItemsInTable) {
            foreach ($mcItem["ingredients"] as $j => $ingredient) { //Iterating trough the ingredients
                if (isset($ingredient["item"]) && in_array($ingredient["item"], $itemsInTheTable)) { //If the recipe doesnt'use tags and is in the table =>
                    continue; //check the next one (there is only one but we never know)
                } elseif (isset($ingredient["tag"])) { // If the recipe use tags =>
                    foreach ($itemsInTheTable as $g => $itemInTable) {
                        if (in_array($itemInTable, $tags[$ingredient["tag"]]["values"])) { //check if the item in the crafting table is in the array of that tag =>
                            continue; //check the next one
                        } else {
                            $isMatch = false; // if not =>
                            break 2; //try with the next recipe
                        }
                    }
                } else {
                    $isMatch = false; // if not =>
                    break; //try with the next recipe
                }
            }
            if ($isMatch !== false) {
                return str_replace("minecraft:", "", $mcItem["result"]["item"]);
            }
        }
    }
    return "null";
}

function itemsInTable($craft)
{
    //To get the list of the differents items that are currently in the table
    $itemsInTheTable = [];
    foreach ($craft["table"] as $row) {
        foreach ($row as $rowItem) {
            if ($rowItem !== "" && !in_array($rowItem, $itemsInTheTable)) {
                array_push($itemsInTheTable, $rowItem);
            }
        }
    }
    return $itemsInTheTable;
}

function nbrOfItemsInTable($craft)
{
    //T o get the number of items in the table (occupied slots)
    $nbrOfItems = 0;
    foreach ($craft["table"] as $row) {
        foreach ($row as $rowItem) {
            if ($rowItem !== "") {
                $nbrOfItems++;
            }
        }
    }
    return $nbrOfItems;
}
