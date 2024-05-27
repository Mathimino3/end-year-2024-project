import inventory from "../inventory.json" assert { type: "json" };
import recipesBook from "./recipes.json" assert { type: "json" };
import tags from "./tags.json" assert { type: "json" };

const recipeKeys = Object.keys(recipesBook);
// const craftCells = document.querySelectorAll(".craft-cell");
// craftCells.forEach((cell, cellIndex) => {
//   cell.addEventListener("mouseup", () => {
//     console.log("caca");
//     checkCraft();
//   });
// });

function itemsInTable() {
  //To get the list of the differents items that are currently in the table
  let itemsInTheTable = [];
  inventory["craftTable"].foreach((row) => {
    row.foreach((rowItem) => {
      if (rowItem !== "" && !itemsInTheTable.includes(rowItem)) {
        array_push(itemsInTheTable, rowItem);
      }
    });
  });
  return itemsInTheTable;
}

function nbrOfItemsInTable() {
  //To get the number of items in the table (occupied slots)
  let nbrOfItems = 0;
  inventory["craftTable"].foreach((row) => {
    row.foreach((rowItem) => {
      if (rowItem !== "") {
        nbrOfItems++;
      }
    });
  });
  return nbrOfItems;
}
console.log(nbrOfItemsInTable());

function checkCraft() {
  // const itemsInTheTable = itemsInTable(craft);
  recipeLoop: for (let i = 0; i < recipeKeys.length; i++) {
    const itemKey = recipeKeys[i];
    const mcItem = recipesBook[itemKey];
    const nbrOfItemsInTable = nbrOfItemsInTable();

    let nbrOfIgredients = null; //The number of ingredients in the shapeless recipe
    if (typeof mcItem["ingredients"] !== "undefined") {
      nbrOfIgredients = mcItem["ingredients"].length;
    }

    let isMatch = true;
    //If the recipe use a pattern (shaped)
    if (mcItem["type"] === "minecraft:crafting_shaped") {
      const recipePattern = mcItem["pattern"];
      //Iterating trough every items of the recipe pattern
      rowLoop: for (let rowNbr = 0; rowNbr < recipePattern.length; rowNbr++) {
        const row = recipePattern[rowNbr];
        itemLoop: for (let j = 0; j < row.length; j++) {
          const rowItem = row[j];
          if (rowItem === inventory["craftTable"][rowNbr][j]["item"]) {
            //If the item from the recipe is === to the one in the craft =>
            continue itemLoop; //check the next one
          } else if (
            typeof rowItem === "array" &&
            typeof rowItem["tag"] !== "undefined"
          ) {
            //If the item is a tag  =>
            if (
              tags[rowItem["tag"]]["values"].includes(
                inventory["craftTable"][rowNbr][j]["item"]
              )
            ) {
              //check if the item in the crafting table is in the array of that tag
              continue itemLoop; //check the next one
            } else {
              isMatch = false; //if not =>
              break rowLoop; //try with the next recipe
            }
          } else {
            isMatch = false; //if not =>
            break rowLoop; //try with the next recipe
          }
        }
      }
      //break recipeLoop lead to here
      console.log("ismatch: " + isMatch);
      if (isMatch === true) {
        //when we've check every items and they are all ===
        return mcItem["result"]["item"].replace("minecraft:", "");
      }
    }
    // // If the recipe doesn't use a pattern (shapeless) and if there is as much items in the table as ingredients in the recipe
    // else if (mcItem["type"] === "minecraft:crafting_shapeless" && nbrOfIgredients === nbrOfItemsInTable) {
    //     foreach ($mcItem["ingredients"] as $j => $ingredient) { //Iterating trough the ingredients
    //         if (isset($ingredient["item"]) && in_array($ingredient["item"], $itemsInTheTable)) { //If the recipe doesnt'use tags and is in the table =>
    //             continue; //check the next one (there is only one but we never know)
    //         } elseif (isset($ingredient["tag"])) { // If the recipe use tags =>
    //             foreach ($itemsInTheTable as $g => $itemInTable) {
    //                 if (in_array($itemInTable, $tags[$ingredient["tag"]]["values"])) { //check if the item in the crafting table is in the array of that tag =>
    //                     continue; //check the next one
    //                 } else {
    //                     $isMatch = false; // if not =>
    //                     break 2; //try with the next recipe
    //                 }
    //             }
    //         } else {
    //             $isMatch = false; // if not =>
    //             break; //try with the next recipe
    //         }
    //     }
    //     if ($isMatch !== false) {
    //         return str_replace("minecraft:", "", $mcItem["result"]["item"]);
    //     }
    // }
  }
  return "null";
}

// checkCraft();
// export default checkCraft();
console.log("result: " + checkCraft());
