import inventory from "./inventory.json" with { type: "json" };

//Creating the inventory
const inventoryGrid = document.querySelector(".inventory-grid"); //parrent of the inventory cells
const phpExecuter = document.querySelector(".php-executer");//iframe that run php code
const itemInMouse = document.querySelector(".item-in-mouse");

const maxStackSize = 64;

//Disabling context menu
document.querySelectorAll('*').forEach(a => {
  a.addEventListener("contextmenu", (e)=>{e.preventDefault()});
});

function updateInv() {
  //(re)create the inventory

  inventoryGrid.innerHTML = null;
  for (let i = 0; i < 36; i++) {
    const cell = document.createElement("div");
    cell.classList.add("cell");
    inventoryGrid.appendChild(cell);
    if (inventory["inventory"][i]["item"] !== "") {
      //If there is an item at that location in the inventory's json =>
      //show that item
      cell.appendChild(createItem("inventory", inventory["inventory"][i]["item"],inventory["inventory"][i]["count"],i));
    }
    cell.id = `inventory-${i}`;
  }
  
  const cells = document.querySelectorAll(".cell");
    //Handeling clicks on cells
  cells.forEach((cell, cellIndex) =>{    
    cell.addEventListener("mouseup", (e) => {
      console.log('clicked cell: ' + cellIndex);
      if (typeof cell.children[0] !== "undefined" && inventory["mouse"]["item"] === "" && inventory["mouse"]["count"] === 0) {
        // If there is an item in the cell and there isn't already an item in the mouse =>
          takeItem(cellIndex, e.button);
      } else if ((typeof cell.children[0] === "undefined" || cell.children[0].classList.contains(inventory["mouse"]["item"])) && inventory["mouse"]["item"] !== "" && inventory["mouse"]["count"] !== 0) {
        //If the cell is empty or has the same item as the mouse in it and the mouse isn't empty =>
        putItem(cellIndex, e.button);
      }else if(inventory['mouse']["item"] !== "" && inventory['mouse']["count"] !== 0 && typeof cell.children[0] !== "undefined" && !cell.children[0].classList.contains(inventory["mouse"]["item"])){
        //If there is different items in the mouse and the cell =>
        switchItems(cellIndex )
      }
    })
  }
  );

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

}
updateInv();

//Making the item in the mouse follow the mouse
window.addEventListener("mousemove", (e) => {
  itemInMouse.style.left = `${e.clientX - itemInMouse.offsetWidth/2}px`;
  itemInMouse.style.top = `${e.clientY - itemInMouse.offsetHeight}px`;
});

function createItem(location, itemName, count, index=null) {
  //Function the return the html object of an item
  const a = document.createElement("a");
  a.classList.add("item", itemName);
  //Handeling items taht are at a number of 0 or less (removing them from existance)
  if(count <= 0){
    inventory[location][index] = {"item": "","count": 0}
    writeJson("./inventory/inventory.json", inventory);
    return a
  }
  //Handeling items whose count is higher than the max
  if(count > maxStackSize){
    count = maxStackSize
    inventory[location][index]["count"] = count;
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

function takeItem(cellIndex, mouseBtn = null, amount = null) {
  //Fct to take item from a cell to the mouse
  console.log("action: take");
  //The amount of item in the cell before we take them from it
  const initialAmount = inventory["inventory"][cellIndex]["count"]
  //The amount of item we are taking
  let amountToTake = initialAmount
  if(mouseBtn === 2){
    //Only take the half of the item if right click
    amountToTake = Math.round(initialAmount/2)
  }
  inventory["mouse"]["item"] = inventory["inventory"][cellIndex]["item"];
  inventory["mouse"]["count"] = amountToTake;
  //The amount of items left in the cell at the end
  const finalAmount = initialAmount-amountToTake
  if(finalAmount === 0){
    inventory["inventory"][cellIndex]["item"] = "";
  }
  inventory["inventory"][cellIndex]["count"] = finalAmount;
  writeJson("./inventory/inventory.json", inventory);
  updateInv();
}

//Fct to put item from the mouse to a cell
function putItem(cellIndex, mouseBtn = null, amount = null) {
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
  updateInv();  
}

function switchItems(cellIndex){
  //Fct that switch the item in the mouse and the one in the cell
  console.log("action: switch");
  const initialCellItem = inventory["inventory"][cellIndex]["item"]
  const initialCellCount = inventory["inventory"][cellIndex]["count"]
  
  inventory["inventory"][cellIndex]["item"] = inventory["mouse"]["item"]
  inventory["inventory"][cellIndex]["count"] = inventory["mouse"]["count"] 
  
  inventory["mouse"]["item"] = initialCellItem;
  inventory["mouse"]["count"] = initialCellCount;
  writeJson("./inventory/inventory.json", inventory);
  updateInv();
}

function writeJson(file, data) { //(async)
  //This code writes data in a json file but, vanilla js can't write on local files for security reasons.
  //I also don't want the page to refresh.
  //So, the workaround is to create an iframe and to put the url of a php script that write on the json in the iframe's src tag
  //(the file argument need to be relative to writeJson.php location)
  phpExecuter.src = `../writeJson.php?file=${file}&data=${JSON.stringify(data)}`;
}

//Crafting
function createCraftGrid(){
  const craftGrid = document.querySelector(".craft-grid");
  // for(let c = 0; c<8; c++){
  //   const cell = document.createElement("div");
  //     cell.classList.add("cell");
  //     craftGrid.appendChild(cell);
  // }

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
                cell.classList.add("cell");
                cell.id = `craft-${gridIndex}`;

              if(inventory["craft"]["table"][rowNbr][rowItemIndex]["item"] !== "" && inventory["craft"]["table"][rowNbr][rowItemIndex]["count"] !== 0){
                createItem(inventory["craft"]["table"][rowNbr][rowItemIndex]["item"], inventory["craft"]["table"][rowNbr][rowItemIndex]["count"], gridIndex)
              }

                //Setting cell id
                
                craftGrid.appendChild(cell);
              })
                  // <div class="slot"><img src="./assets/textures/<?php echo str_replace("minecraft:", "", $craft["table"][$rowNbr][$i]) ?>.png" alt="">
          
  }
  
  //Creating the fat arrow
  const fatArrow = document.createElement("img");
  fatArrow.src ="../assets/img/fat_arrow.png";
  fatArrow.classList.add("fat-arrow")
  document.querySelector('.craft-grid-container').appendChild(fatArrow)

  const resultCell = document.createElement("div")
  resultCell.classList.add("result-cell", "cell")
  resultCell.style.height = `${document.querySelector("#inventory-0").offsetHeight}px`
  document.querySelector('.craft-grid-container').appendChild(resultCell)

}
createCraftGrid()