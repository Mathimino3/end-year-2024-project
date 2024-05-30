import inventory from "./inventory.json" with { type: "json" };
import recipesBook from "./recipes.json" with { type: "json" };
import tags from "./tags.json" with { type: "json" };

//Creating the inventory
const inventoryGrid = document.querySelector(".inventory-grid"); //parrent of the inventory cells
const craftContainer = document.querySelector(".craft-grid-container");
const phpExecuter = document.querySelector(".php-executer"); //iframe that run php code
const itemInMouse = document.querySelector(".item-in-mouse");
const maxStackSize = 64;
const recipeKeys = Object.keys(recipesBook);

//Disabling context menu
document.querySelectorAll("*").forEach((a) => {
  a.addEventListener("contextmenu", (e) => {
    e.preventDefault();
  });
});

function createInv() {
  //fct that (re)create the inventory

  inventoryGrid.innerHTML = null;
  for (let i = 0; i < 36; i++) {
    const cell = document.createElement("div");
    cell.classList.add("cell", "inventory-cell");
    inventoryGrid.appendChild(cell);
    if (inventory["inventory"][i]["item"] !== "") {
      //If there is an item at that location in the inventory's json =>
      //show that item
      cell.appendChild(
        createItem(
          "inventory",
          inventory["inventory"][i]["item"],
          inventory["inventory"][i]["count"],
          i
        )
      );
    }
    cell.id = `inventory-${i}`;
  }

  const inventoryCells = document.querySelectorAll(".inventory-cell");
  //Handeling clicks on cells
  inventoryCells.forEach((cell, cellIndex) => {
    cell.addEventListener("mouseup", (e) => {
      cellClick("inventory", e, cell, cellIndex);
    });
  });
}

function createCraftContainer() {
  //fct that (re)create the craft grid and the result-cell
  craftContainer.innerHTML = null;
  const craftGrid = document.createElement("div");
  craftGrid.classList.add("craft-grid");

  //Creating the craft title
  const craftTitle = document.createElement("h2");
  craftTitle.classList.add("storage-title", "craft-title");
  craftTitle.innerText = "Crafting";
  craftGrid.appendChild(craftTitle);
  //Crating the craft grid
  for (let rowNbr = 0; rowNbr < 3; rowNbr++) {
    // iterating through the 3 row of the crafting table
    const row = inventory["craftTable"][rowNbr];

    //creating the slots
    inventory["craftTable"][rowNbr].forEach((mcItem, rowItemIndex) => {
      //converting the coordinates of the item to an index
      let gridIndex = 0;
      if (rowNbr === 0) {
        gridIndex = rowItemIndex;
      } else if (rowNbr === 1) {
        gridIndex = rowItemIndex + 3;
      } else if (rowNbr === 2) {
        gridIndex = rowItemIndex + 6;
      }

      const cell = document.createElement("div");
      cell.classList.add("cell", "craft-cell");
      //Setting cell id
      cell.id = `craft-${gridIndex}`;
      //If there is an item at that location in the craft grid json =>
      //show that item
      if (
        inventory["craftTable"][rowNbr][rowItemIndex]["item"] !== "" &&
        inventory["craftTable"][rowNbr][rowItemIndex]["count"] !== 0
      ) {
        cell.appendChild(
          createItem(
            "craftTable",
            inventory["craftTable"][rowNbr][rowItemIndex]["item"],
            inventory["craftTable"][rowNbr][rowItemIndex]["count"],
            gridIndex
          )
        );
      }
      craftGrid.appendChild(cell);
    });
  }
  craftContainer.appendChild(craftGrid);

  //Getting the result of the craft and saving it to the json
  const craftResult = checkCraft()[0];
  const craftResultCount = checkCraft()[1];
  console.log("craftResult: " + craftResult);
  console.log("craftResultCount: " + craftResultCount);
  if (craftResult !== "" && craftResultCount !== 0) {
    inventory["resultCell"][0]["item"] = craftResult;
    inventory["resultCell"][0]["count"] = craftResultCount;
  } else {
    inventory["resultCell"][0]["item"] = "";
    inventory["resultCell"][0]["count"] = 0;
  }
  writeJson("./inventory/inventory.json", inventory);

  const craftCells = document.querySelectorAll(".craft-cell");
  //Handeling clicks on cells
  craftCells.forEach((cell, cellIndex) => {
    cell.addEventListener("mouseup", (e) => {
      cellClick("craftTable", e, cell, cellIndex);
    });
  });

  //Creating the fat arrow
  const fatArrow = document.createElement("img");
  fatArrow.src = "../assets/img/fat_arrow.png";
  fatArrow.classList.add("fat-arrow");
  craftContainer.appendChild(fatArrow);

  const resultCell = document.createElement("div");
  resultCell.classList.add("result-cell", "cell");
  resultCell.style.height = `${
    document.querySelector("#craft-0").offsetHeight
  }px`;
  //Show the item if there is a result to the craft
  if (craftResult !== "" && craftResultCount !== 0) {
    resultCell.appendChild(
      createItem("resultCell", craftResult, craftResultCount, 0)
    );
  }
  craftContainer.appendChild(resultCell);

  //If there is an item at that location in the resultCell json =>
  //show that item
  //Handeling click on the result cell
  resultCell.addEventListener("mouseup", (e) => {
    cellClick("resultCell", e, resultCell, 0);
  });
}

function cellClick(location, e, cell, cellIndex) {
  console.log("clicked cell: " + cellIndex + " location: " + location);
  if (
    typeof cell.children[0] !== "undefined" &&
    inventory["mouse"]["item"] === "" &&
    inventory["mouse"]["count"] === 0
  ) {
    // If there is an item in the cell and there isn't already an item in the mouse =>
    takeItem(location, cellIndex, e.button);
  } else if (
    (typeof cell.children[0] === "undefined" ||
      cell.children[0].classList.contains(inventory["mouse"]["item"])) &&
    inventory["mouse"]["item"] !== "" &&
    inventory["mouse"]["count"] !== 0 &&
    !cell.classList.contains("result-cell")
  ) {
    //If the cell is empty or has the same item as the mouse in it and the mouse isn't empty and isn't the result-cell=>
    putItem(location, cellIndex, e.button);
  } else if (
    inventory["mouse"]["item"] !== "" &&
    inventory["mouse"]["count"] !== 0 &&
    typeof cell.children[0] !== "undefined" &&
    !cell.children[0].classList.contains(inventory["mouse"]["item"]) &&
    !cell.classList.contains("result-cell")
  ) {
    //If there is different items in the mouse and the cell and isn't the result-cellS=>
    switchItems(location, cellIndex);
  }
}

function createItem(location, itemName, count, index) {
  const a = document.createElement("a");
  a.classList.add("item", itemName);
  //Function the return the html object of an item

  //Handeling items taht are at a number of 0 or less (removing them from existance)
  if (count <= 0) {
    if (location === "craftTable") {
      inventory["craftTable"][indexToCraftIndexTable(index)[0]][
        indexToCraftIndexTable(index)[1]
      ] = { item: "", count: 0 };
    } else {
      inventory[location][index] = { item: "", count: 0 };
    }
    writeJson("./inventory/inventory.json", inventory);
    return a;
  }

  //Handeling items whose count is higher than the max
  if (count > maxStackSize) {
    count = maxStackSize;
    if (location === "craftTable") {
      inventory["craftTable"][indexToCraftIndexTable(index)[0]][
        indexToCraftIndexTable(index)[1]
      ]["count"] = count;
    } else {
      inventory[location][index]["count"] = count;
    }
    writeJson("./inventory/inventory.json", inventory);
  }
  //Creating the img
  const img = document.createElement("img");
  img.classList.add("item-img");
  a.appendChild(img);
  setItemImage(img, itemName);

  //Creating the count txt
  const span = document.createElement("span");
  span.classList.add("item-count");
  span.innerText = count;
  a.appendChild(span);

  //Creating the tooltip
  const itemTooltip = document.createElement("div");
  itemTooltip.classList.add("item-tooltip", "hidden");
  a.appendChild(itemTooltip);
  //Creating the tooltip's text
  const itemTooltipName = document.createElement("span");
  itemTooltipName.classList.add("item-tooltip-name");
  itemTooltipName.innerText = formatItemName(itemName);
  itemTooltip.appendChild(itemTooltipName);

  //Handeling the hover
  const itemHoverHandeler = document.createElement("div");
  itemHoverHandeler.classList.add("item-hover-handeler");
  a.appendChild(itemHoverHandeler);

  //Handeling the display of tooltip
  itemHoverHandeler.addEventListener("mouseover", () => {
    itemTooltip.classList.remove("hidden");
  });
  itemHoverHandeler.addEventListener("mouseout", () => {
    itemTooltip.classList.add("hidden");
  });
  return a;
}

function setItemImage(img, itemName) {
  img.src = `../assets/textures/${itemName.replace("minecraft:", "")}.png`;
  //Setting src if img not found
  img.onerror = () => {
    img.src = `../assets/textures/${itemName.replace(
      "minecraft:",
      ""
    )}_front.png`;
    img.onerror = () => {
      img.src = "../assets/textures/missing_texture.png";
    };
  };
}

function formatItemName(itemName) {
  //Formating string from, eg: "minecraft:oak_planks" to "Oak Planks"
  let newItemName = itemName.replace("minecraft:", ""); //oak_planks
  newItemName = newItemName.replaceAll("_", " "); //oak planks
  newItemName = newItemName[0].toUpperCase() + newItemName.substring(1); //Oak planks
  for (let l = 0; l < newItemName.length; l++) {
    if (newItemName[l] === " " && typeof newItemName[l + 1] !== "undefined") {
      newItemName =
        newItemName.substring(0, l + 1) +
        newItemName[l + 1].toUpperCase() +
        newItemName.substring(l + 2);
    }
  } //Oak Planks
  return newItemName;
}

function takeItem(location, cellIndex, mouseBtn = null) {
  //Fct to take item from a cell to the mouse
  console.log("action: take");

  //The amount of item in the cell before we take them from it
  let initialAmount = null;
  //The item in the cell nefore we take it from it
  let itemInCell = null;
  //The "coordinates" to get to the item if in crafting table (2D array)
  let craftTableX = null;
  let craftTableY = null;

  //Setting the variables according to the location of the item
  if (location === "craftTable") {
    craftTableX = indexToCraftIndexTable(cellIndex)[0];
    craftTableY = indexToCraftIndexTable(cellIndex)[1];

    initialAmount = inventory["craftTable"][craftTableX][craftTableY]["count"];
    itemInCell = inventory["craftTable"][craftTableX][craftTableY]["item"];
  } else {
    initialAmount = inventory[location][cellIndex]["count"];
    itemInCell = inventory[location][cellIndex]["item"];
  }

  //The amount of item we are taking
  let amountToTake = initialAmount;
  if (location === "resultCell" && mouseBtn === 0) {
    //Only take one item if left click and in resultCell
    amountToTake = 1;
  }else if (mouseBtn === 2 && location !== "resultCell") {
    //Only take the half of the item if right click anywhere e
    amountToTake = Math.round(initialAmount / 2);
  }
  inventory["mouse"]["item"] = itemInCell;
  inventory["mouse"]["count"] = amountToTake;
  //The amount of items left in the cell at the end
  const finalAmount = initialAmount - amountToTake;

  //Removing the item from his cell according to his location
  if (location === "craftTable") {
    if (finalAmount === 0) {
      inventory["craftTable"][craftTableX][craftTableY]["item"] = "";
    }
    inventory["craftTable"][craftTableX][craftTableY]["count"] = finalAmount;
  } else if (location === "resultCell") {
    removeItemsFromTable(amountToTake);
  } else {
    if (finalAmount === 0) {
      inventory[location][cellIndex]["item"] = "";
    }
    inventory[location][cellIndex]["count"] = finalAmount;
  }

  writeJson("./inventory/inventory.json", inventory);
  updateAll();
}

function removeItemsFromTable(amountToTake) {
  //Fcr the remove the items that have been used when crafting
  inventory["craftTable"].forEach((row, rowIndex) => {
    row.forEach((rowItem, rowItemIndex) => {
      const newAmount = rowItem["count"] - amountToTake;
      if (newAmount > 0) {
        inventory["craftTable"][rowIndex][rowItemIndex]["count"] = newAmount;
      } else {
        inventory["craftTable"][rowIndex][rowItemIndex]["count"] = 0;
        inventory["craftTable"][rowIndex][rowItemIndex]["item"] = "";
      }
    });
  });
  writeJson("./inventory/inventory.json", inventory);
}
//Fct to put item from the mouse to a cell
function putItem(location, cellIndex, mouseBtn = null) {
  console.log("action: put");
  //The  amount of items in the cell before with put them in
  let initialCellAmount = null;
  //The  amount of items in the mouse before with put them in a cell
  const initialMouseAmount = inventory["mouse"]["count"];
  //The "coordinates" to get to the item if in crafting table (2D array)
  let craftTableX = null;
  let craftTableY = null;

  //Setting the variables according to the location of the item
  if (location === "craftTable") {
    craftTableX = indexToCraftIndexTable(cellIndex)[0];
    craftTableY = indexToCraftIndexTable(cellIndex)[1];

    initialCellAmount =
      inventory["craftTable"][craftTableX][craftTableY]["count"];
  } else {
    initialCellAmount = inventory[location][cellIndex]["count"];
  }

  //The amount of items to put in the cell
  let amountToPut = initialMouseAmount;
  if (mouseBtn === 2) {
    //Only put one item if right click
    amountToPut = 1;
  }
  let finalCellAmount = initialCellAmount + amountToPut;
  let mouseRest = 0;
  if (finalCellAmount > maxStackSize) {
    mouseRest = finalCellAmount - maxStackSize;
    finalCellAmount = maxStackSize;
  }
  //Putting the item in the cell according to his location
  if (location === "craftTable") {
    inventory["craftTable"][craftTableX][craftTableY]["item"] =
      inventory["mouse"]["item"];
    inventory["craftTable"][craftTableX][craftTableY]["count"] =
      finalCellAmount;
  } else {
    inventory[location][cellIndex]["item"] = inventory["mouse"]["item"];
    inventory[location][cellIndex]["count"] = finalCellAmount;
  }
  //The amount of items left in the mouse at the end
  const finalMouseAmount = initialMouseAmount - amountToPut + mouseRest;
  if (finalMouseAmount === 0) {
    inventory["mouse"]["item"] = "";
  }
  inventory["mouse"]["count"] = finalMouseAmount;
  writeJson("./inventory/inventory.json", inventory);
  updateAll();
}

function switchItems(location, cellIndex) {
  //Fct that switch the item in the mouse and the one in the cell
  console.log("action: switch");

  let initialCellItem = null;
  let initialCellCount = null;
  //The "coordinates" to get to the item if in crafting table (2D array)
  let craftTableX = null;
  let craftTableY = null;

  //Setting the variables and moving items according to the location of it
  if (location === "craftTable") {
    craftTableX = indexToCraftIndexTable(cellIndex)[0];
    craftTableY = indexToCraftIndexTable(cellIndex)[1];

    initialCellItem = inventory["craftTable"][craftTableX][craftTableY]["item"];
    initialCellCount =
      inventory["craftTable"][craftTableX][craftTableY]["count"];

    inventory["craftTable"][craftTableX][craftTableY]["item"] =
      inventory["mouse"]["item"];
    inventory["craftTable"][craftTableX][craftTableY]["count"] =
      inventory["mouse"]["count"];
  } else {
    initialCellItem = inventory[location][cellIndex]["item"];
    initialCellCount = inventory[location][cellIndex]["count"];

    inventory[location][cellIndex]["item"] = inventory["mouse"]["item"];
    inventory[location][cellIndex]["count"] = inventory["mouse"]["count"];
  }

  inventory["mouse"]["item"] = initialCellItem;
  inventory["mouse"]["count"] = initialCellCount;
  writeJson("./inventory/inventory.json", inventory);
  updateAll();
}

function writeJson(file, data) {
  //(async)
  //This code writes data in a json file but, vanilla js can't write on local files for security reasons.
  //I also don't want the page to refresh.
  //So, the workaround is to create an iframe and to put the url of a php script that write on the json in the iframe's src tag
  //(the file argument need to be relative to writeJson.php location)
  phpExecuter.src = `../writeJson.php?file=${file}&data=${JSON.stringify(
    data
  )}`;
}

function indexToCraftIndexTable(index) {
  //Function to convert a regular index to access an item in a grid to ones that work for the crafting table
  //since the craft grid is a 2 dimentional array while the regulars grid are simple ones.
  let rowNbr = Math.trunc(index / 3);
  let rowItemIndex = null;
  if (index === 0 || index === 3 || index === 6) {
    rowItemIndex = 0;
  } else if (index === 1 || index === 4 || index === 7) {
    rowItemIndex = 1;
  } else if (index === 2 || index === 5 || index === 8) {
    rowItemIndex = 2;
  }
  //[x, y]
  return [rowNbr, rowItemIndex];
}

function updateAll() {
  createInv();
  createCraftContainer();
  //Creating the item in the mouse
  if (inventory["mouse"]["item"] === "" && inventory["mouse"]["count"] === 0) {
    //If the mouse is empty => hide it
    itemInMouse.style.display = "none";
    return;
  }
  itemInMouse.style.display = "block";
  //Setting the image of the item in the mouse
  setItemImage(itemInMouse.children[0].children[0], inventory["mouse"]["item"]);
  itemInMouse.children[0].children[1].innerText = inventory["mouse"]["count"];

  //Setting a hight that relate to the other items height;
  const item = document.querySelectorAll(".item")[0];
  itemInMouse.style.height = `${item.offsetHeight}px`;

  //Making the item in the mouse follow the mouse
  window.addEventListener("mousemove", (e) => {
    itemInMouse.style.left = `${e.clientX - itemInMouse.offsetWidth / 2}px`;
    itemInMouse.style.top = `${e.clientY - itemInMouse.offsetHeight}px`;
  });
}
updateAll();

//Creating  tips for the user
const inventoryTips = document.createElement("img");
inventoryTips.src="../assets/img/inventory_tips.png";
inventoryTips.classList.add("inventory-tips", "hidden")
document.querySelector(".inventory").appendChild(inventoryTips);

const inventoryTipsBtn = document.createElement("span")
inventoryTipsBtn.classList.add("inventory-tips-btn")
inventoryTipsBtn.innerText = "Tips"
document.querySelector(".inventory").appendChild(inventoryTipsBtn);

//Handle inventoryTips display
inventoryTipsBtn.addEventListener("mouseover", ()=>{
  inventoryTips.classList.remove("hidden");
})
inventoryTipsBtn.addEventListener("mouseout", ()=>{
  inventoryTips.classList.add("hidden");
})



//Crating///////////////////////////////////////////////////////////////

function checkCraft() {
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
        return [mcItem["result"]["item"], getMinCount()];
        //This line return [name of the item, count of the item]
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
        //when we've check every items and they are all ===
        return [mcItem["result"]["item"], getMinCount()];
        //This line return [name of the item, count of the item]
      }
    }
  }
  //No matches
  return ["", 0];
}

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

function getMinCount() {
  //Fct to get the smallest count that is in the crafting table
  let countList = [];
  inventory["craftTable"].forEach((row) => {
    row.forEach((rowItem) => {
      if (rowItem["count"] !== 0) {
        countList.push(rowItem["count"]);
      }
    });
  });
  return Math.min(...countList);
}
