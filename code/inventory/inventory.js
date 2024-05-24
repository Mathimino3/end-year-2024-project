import inventory from "./inventory.json" with { type: "json" };

//Creating the inventory
const inventoryGrid = document.querySelector(".inventory-grid"); //parrent of the inventory cells
const craftContainer = document.querySelector(".craft-grid-container");
const phpExecuter = document.querySelector(".php-executer");//iframe that run php code
const itemInMouse = document.querySelector(".item-in-mouse");
const maxStackSize = 64;

//Disabling context menu
document.querySelectorAll('*').forEach(a => {
  a.addEventListener("contextmenu", (e)=>{e.preventDefault()});
});

function createInv(){
//(re)create the inventory

  inventoryGrid.innerHTML = null;
  for (let i = 0; i < 36; i++) {
    const cell = document.createElement("div");
    cell.classList.add("cell", "inventory-cell");
    inventoryGrid.appendChild(cell);
    if (inventory["inventory"][i]["item"] !== "") {
      //If there is an item at that location in the inventory's json =>
      //show that item
      cell.appendChild(createItem("inventory", inventory["inventory"][i]["item"],inventory["inventory"][i]["count"],i));
    }
    cell.id = `inventory-${i}`;
  }
  
  const inventoryCells = document.querySelectorAll(".inventory-cell");
    //Handeling clicks on cells
  inventoryCells.forEach((cell, cellIndex) =>{    
    cell.addEventListener("mouseup", (e) => {
      cellClick("inventory", e, cell, cellIndex)
    })
  });
}

function createCraftContainer(){
  craftContainer.innerHTML = null; 
  const craftGrid = document.createElement('div')  
  craftGrid.classList.add("craft-grid");
  for (let rowNbr = 0; rowNbr < 3; rowNbr++){
              // iterating through the 3 row of the crafting table
              const row = inventory["craft"]["table"][rowNbr];

              //creating the slots
              inventory["craft"]["table"][rowNbr].forEach((mcItem, rowItemIndex)=>{
                let gridIndex = 0;
                if(rowNbr === 0){
                  gridIndex =rowItemIndex;
                }else if(rowNbr === 1){
                  gridIndex =rowItemIndex+3;
                }else if(rowNbr === 2){
                  gridIndex =rowItemIndex+6
                }
                const cell = document.createElement("div");
                cell.classList.add("cell", "craft-cell");
                //Setting cell id
                cell.id = `craft-${gridIndex}`;
                
                if(inventory["craft"]["table"][rowNbr][rowItemIndex]["item"] !== "" && inventory["craft"]["table"][rowNbr][rowItemIndex]["count"] !== 0){
                  cell.appendChild(createItem("craftTable", inventory["craft"]["table"][rowNbr][rowItemIndex]["item"], inventory["craft"]["table"][rowNbr][rowItemIndex]["count"], gridIndex))
                }
                craftGrid.appendChild(cell);
              })
                  // <div class="slot"><img src="./assets/textures/<?php echo str_replace("minecraft:", "", $craft["table"][$rowNbr][$i]) ?>.png" alt="">
  }
  craftContainer.appendChild(craftGrid)

  const craftCells = document.querySelectorAll(".craft-cell");
      //Handeling clicks on cells
    craftCells.forEach((cell, cellIndex) =>{    
      cell.addEventListener("mouseup", (e) => {
        // if(cell.classList.contains("inventory-cell")){
        //   location = "inventory"
        // } else if(cell.classList.contains("craft-cell")){
        //   location = "craftTable"
        // }
        cellClick("craftTable", e, cell, cellIndex)
      })
    });

  //Creating the fat arrow
  const fatArrow = document.createElement("img");
  fatArrow.src ="../assets/img/fat_arrow.png";
  fatArrow.classList.add("fat-arrow")
  craftContainer.appendChild(fatArrow)

  const resultCell = document.createElement("div")
  resultCell.classList.add("result-cell", "cell")
  resultCell.style.height = `${document.querySelector("#inventory-0").offsetHeight}px`
  craftContainer.appendChild(resultCell)

}

function cellClick(location, e, cell, cellIndex){
  console.log('clicked cell: ' + cellIndex + " location: " + location);
  if (typeof cell.children[0] !== "undefined" && inventory["mouse"]["item"] === "" && inventory["mouse"]["count"] === 0) {
    // If there is an item in the cell and there isn't already an item in the mouse =>
      takeItem(location, cellIndex, e.button);
  } else if ((typeof cell.children[0] === "undefined" || cell.children[0].classList.contains(inventory["mouse"]["item"])) && inventory["mouse"]["item"] !== "" && inventory["mouse"]["count"] !== 0) {
    //If the cell is e  mpty or has the same item as the mouse in it and the mouse isn't empty =>
    putItem(location, cellIndex, e.button);
  }else if(inventory['mouse']["item"] !== "" && inventory['mouse']["count"] !== 0 && typeof cell.children[0] !== "undefined" && !cell.children[0].classList.contains(inventory["mouse"]["item"])){
    //If there is different items in the mouse and the cell =>
    switchItems(location, cellIndex )
  }
}

function createItem(location, itemName, count, index=null) {
  const a = document.createElement("a");
  a.classList.add("item", itemName);
  //Function the return the html object of an item
  
  //Handeling items taht are at a number of 0 or less (removing them from existance)
  if(count <=0){
    switch(location){
      case "inventory":
        inventory["inventory"][index] = {"item": "","count": 0}
        break;
      case "craftTable":
        inventory["craft"]["table"][indexToCraftIndexTable(index)[0]][indexToCraftIndexTable(index)[1]] = {"item": "","count": 0}
        break;
      }
    writeJson("./inventory/inventory.json", inventory);
    return a
  }

  //Handeling items whose count is higher than the max
  if(count > maxStackSize){
    count = maxStackSize
    switch(location){
      case "inventory":
        inventory["inventory"][index]["count"] = count;
        break;
      case "craftTable":
        
        inventory["craft"]["table"][indexToCraftIndexTable(index)[0]][indexToCraftIndexTable(index)[1]]["count"] = count
        break;
      }
    writeJson("./inventory/inventory.json", inventory); 
  }
  //Creating the img
  const img = document.createElement("img");
  img.classList.add('item-img')
  a.appendChild(img);
  setItemImage(img, itemName)
  
  //Creating the count txt
  const span = document.createElement("span");
  span.classList.add("item-count");
  span.innerText = count;
  a.appendChild(span);

  //Creating the tooltip
  const itemTooltip = document.createElement('div')
  itemTooltip.classList.add("item-tooltip", "hidden")
  a.appendChild(itemTooltip)
  //Creating the tooltip's text
  const itemTooltipName = document.createElement('span')
  itemTooltipName.classList.add('item-tooltip-name')
  itemTooltipName.innerText = formatItemName(itemName)
  itemTooltip.appendChild(itemTooltipName)

  //Handeling the hover
  const itemHoverHandeler = document.createElement('div')
  itemHoverHandeler.classList.add('item-hover-handeler')
  a.appendChild(itemHoverHandeler)
  
  //Handeling the display of tooltip
  itemHoverHandeler.addEventListener('mouseover', ()=>{
    itemTooltip.classList.remove("hidden")
  })
  itemHoverHandeler.addEventListener('mouseout', ()=>{
    itemTooltip.classList.add("hidden")
  })
  return a;
}

function setItemImage(img, itemName){
  img.src = `../assets/textures/${itemName.replace("minecraft:", "")}.png`;
  //Setting src if img not found
  img.onerror = () => {
    img.src = `../assets/textures/${itemName.replace("minecraft:", "")}_front.png`;
    img.onerror = () => {
      img.src = '../assets/textures/missing_texture.png';
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
      newItemName = newItemName.substring(0, l + 1) + newItemName[l + 1].toUpperCase() + newItemName.substring(l + 2);
    }
  } //Oak Planks
  return newItemName;
}

function takeItem(location, cellIndex, mouseBtn = null, amount = null) {
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
  switch(location){
    case "inventory":
      initialAmount = inventory["inventory"][cellIndex]["count"]
      itemInCell = inventory["inventory"][cellIndex]["item"];
      break;
    case "craftTable":
      craftTableX = indexToCraftIndexTable(cellIndex)[0];
      craftTableY = indexToCraftIndexTable(cellIndex)[1];

      initialAmount = inventory["craft"]["table"][craftTableX][craftTableY]["count"];
      itemInCell = inventory["craft"]["table"][craftTableX][craftTableY]["item"];
      break;
  }

  //The amount of item we are taking
  let amountToTake = initialAmount
  if(mouseBtn === 2){
    //Only take the half of the item if right click
    amountToTake = Math.round(initialAmount/2)
  }
  inventory["mouse"]["item"] = itemInCell;
  inventory["mouse"]["count"] = amountToTake;
  //The amount of items left in the cell at the end
  const finalAmount = initialAmount-amountToTake

  //Removing the item from his cell according to his location
  switch(location){
    case "inventory":
      if(finalAmount === 0){
        inventory["inventory"][cellIndex]["item"] = "";
      }
      inventory["inventory"][cellIndex]["count"] = finalAmount;
      break;
    case "craftTable":
      if(finalAmount === 0){
        inventory["craft"]["table"][craftTableX][craftTableY]["item"] = "";
      }
      inventory["craft"]["table"][craftTableX][craftTableY]["count"] = finalAmount;
      break;
  }
  
  writeJson("./inventory/inventory.json", inventory);
  updateAll();
}

//Fct to put item from the mouse to a cell
function putItem(location, cellIndex, mouseBtn = null, amount = null) {
  console.log("action: put");
  const initialCellAmount = inventory["inventory"][cellIndex]["count"]
  //The  amount of items in the mouse before with put them in a cell
  const initialMouseAmount = inventory["mouse"]["count"]
  //The amount of items to put in the cell
  let amountToPut = initialMouseAmount;
  if(mouseBtn === 2){
    //Only put one item if right click
    amountToPut = 1;
  }
  let finalCellAmount = initialCellAmount + amountToPut
  let mouseRest = 0
  if(finalCellAmount > maxStackSize){
    mouseRest = finalCellAmount-maxStackSize;
    finalCellAmount = maxStackSize
  }
  inventory["inventory"][cellIndex]["item"] = inventory["mouse"]["item"];
  inventory["inventory"][cellIndex]["count"] = finalCellAmount;
  //The amount of items left in the mouse at the end
  const finalMouseAmount = initialMouseAmount-amountToPut + mouseRest;
  if(finalMouseAmount === 0){
    inventory["mouse"]["item"] = "";
  }
  inventory["mouse"]["count"] = finalMouseAmount;
  writeJson("./inventory/inventory.json", inventory);
  updateAll();  
}

function switchItems(location, cellIndex){
  //Fct that switch the item in the mouse and the one in the cell
  console.log("action: switch");
  const initialCellItem = inventory["inventory"][cellIndex]["item"]
  const initialCellCount = inventory["inventory"][cellIndex]["count"]
  
  inventory["inventory"][cellIndex]["item"] = inventory["mouse"]["item"]
  inventory["inventory"][cellIndex]["count"] = inventory["mouse"]["count"] 
  
  inventory["mouse"]["item"] = initialCellItem;
  inventory["mouse"]["count"] = initialCellCount;
  writeJson("./inventory/inventory.json", inventory);
  updateAll();
}

function writeJson(file, data) { //(async)
  //This code writes data in a json file but, vanilla js can't write on local files for security reasons.
  //I also don't want the page to refresh.
  //So, the workaround is to create an iframe and to put the url of a php script that write on the json in the iframe's src tag
  //(the file argument need to be relative to writeJson.php location)
  phpExecuter.src = `../writeJson.php?file=${file}&data=${JSON.stringify(data)}`;
}

function indexToCraftIndexTable(index){
  //Function to convert a regular index to access an item in a grid to ones that work for the crafting table
  //since the craft grid is a 2 dimentional array while the regulars grid are simple ones.
  let rowNbr = Math.trunc(index/3);
  let rowItemIndex = null;
  if(index === 0 || index === 3 || index === 6){
    rowItemIndex = 0;
  }else if(index === 1 || index === 4 || index === 7){
    rowItemIndex = 1;
  }else if(index === 2 || index === 5 || index === 8){
    rowItemIndex = 2;
  }
  //[x, y]
  return [rowNbr, rowItemIndex];
}

function updateAll() {
  createInv()
  createCraftContainer()  

  //Creating the item in the mouse
  if(inventory["mouse"]["item"] === "" && inventory["mouse"]["count"]=== 0){
    //If the mouse is empty => hide it
    itemInMouse.style.display = 'none';
    return;
  }
  itemInMouse.style.display = 'block';
  //Setting the image of the item in the mouse
  setItemImage(itemInMouse.children[0].children[0], inventory["mouse"]["item"])
  itemInMouse.children[0].children[1].innerText = inventory["mouse"]["count"];
  
  //Setting a hight that relate to the other items height;
  const item = document.querySelectorAll(".item")[0];
  itemInMouse.style.height = `${item.offsetHeight}px`

  //Making the item in the mouse follow the mouse
  window.addEventListener("mousemove", (e) => {
    itemInMouse.style.left = `${e.clientX - itemInMouse.offsetWidth/2}px`;
    itemInMouse.style.top = `${e.clientY - itemInMouse.offsetHeight}px`;
  });
}
updateAll();
