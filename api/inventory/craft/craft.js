import recipesBook from "./recipes.json" with { type: "json" };
import tags from "./tags.json" with { type: "json" };

const recipeKeys = Object.keys(recipesBook);
// const craftCells = document.querySelectorAll(".craft-cell");
// craftCells.forEach((cell, cellIndex) => {
//   cell.addEventListener("mouseup", () => {
//     checkCraft();
//   });
// });

function getItemsInTable() {
  //To get the list of the differents items that are currently in the table
  let itemsInTable = [];
  inventory["craftTable"].forEach((row) => {
    row.forEach((rowItem) => {
      if (rowItem["item"] !== "" && !itemsInTable.includes(rowItem["item"])) {
        itemsInTable.push(rowItem["item"]);
      }
    });
  });
  return itemsInTable;
}

function getNbrOfItemsInTable() {
  //To get the number of items in the table (occupied slots)
  let nbrOfItems = 0;
  inventory["craftTable"].forEach((row) => {
    row.forEach((rowItem) => {
      if (rowItem["item"] !== "") {
        nbrOfItems++;
      }
    });
  });
  return nbrOfItems;
}

export default function checkCraft() {
  const itemsInTable = getItemsInTable();
  recipeLoop: for (let i = 0; i < recipeKeys.length; i++) {
    const itemKey = recipeKeys[i];
    const mcItem = recipesBook[itemKey];
    const nbrOfItemsInTable = getNbrOfItemsInTable();

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
      if (isMatch === true) {
        //when we've check every items and they are all ===
        return mcItem["result"]["item"].replace("minecraft:", "");
      }
    }
    // If the recipe doesn't use a pattern (shapeless) and if there is as much items in the table as ingredients in the recipe
    else if (
      mcItem["type"] === "minecraft:crafting_shapeless" &&
      nbrOfIgredients === nbrOfItemsInTable
    ) {
      ingredientsLoop: for (let j = 0; j < mcItem["ingredients"].length; j++) {
        const ingredient = mcItem["ingredients"][j]; //Iterating trough the ingredients;
        if (
          typeof ingredient["item"] !== "undefined" &&
          itemsInTable.includes(ingredient["item"])
        ) {
          //If the recipe doesnt'use tags and is in the table =>
          continue ingredientsLoop; //check the next one
        } else if (typeof ingredient["tag"] !== "undefined") {
          // If the recipe use tags =>
          tagsLoop: for (let g = 0; g < itemsInTable.length; g++) {
            const itemInTable = itemsInTable[g];
            if (tags[ingredient["tag"]]["values"].includes(itemInTable)) {
              //check if the item in the crafting table is in the array of that tag =>
              continue tagsLoop; //check the next one
            } else {
              isMatch = false; // if not =>
              break ingredientsLoop; //try with the next recipe
            }
          }
        } else {
          isMatch = false; // if not =>
          break ingredientsLoop; //try with the next recipe
        }
      }
      if (isMatch === true) {
        return mcItem["result"]["item"].replace("minecraft:", "");
      }
    }
  }
  return "null";
}

// checkCraft();
// export default checkCraft();
// console.log("result: " + checkCraft());
// export default checkCraft()