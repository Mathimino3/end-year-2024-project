<?php
session_start();
$inventory = json_decode(file_get_contents("./inventory/inventory.json"), true);
$recipes = json_decode(file_get_contents("./inventory/recipes.json"), true);
//oui
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="height=device-height, 
        width=device-width, initial-scale=1.0, minimum-scale=1.0, 
        maximum-scale=1.0, user-scalable=no">
    <script src="./display.js" defer></script>
    <script src="./inventory/inventory.js" type="module" defer></script>
    <!-- <script src="./inventory/craft/craft.js" type="module" defer></script> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Game</title>
</head>

<body>

    <div class="btn-separator first-btn-separator mobile-separator">

        <div class="inventory-btn  btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>

        <a class="choices-btn btn first-choice">
            <p>Overworld</p>
        </a>
        <a class="choices-btn btn second-choice">
            <p>Nether</p>
        </a>
    </div>

    <div class="gameplay-container">
        <div class="inventory-btn inventory-btn btn">
            <img src="./assets/img/inventory_btn.png" alt="Inventaire">
        </div>

        <img class="gameplay" src="./assets/img/gameplay.png" alt="">

        <div class="chat">
            <p>This text is editable</p>
        </div>
    </div>

    <div class="btn-separator last-btn-separator mobile-separator">
        <a class="choices-btn btn third-choice">
            <p>End</p>
        </a>
        <a class="choices-btn btn">
            <p>Choice 4</p>
        </a>
    </div>

    <div class="choices-btn-container">

        <div class="first-btn-separator btn-separator desktop-sparator">
            <a class="choices-btn btn first-choice">
                <p>Overworld</p>
            </a>
            <a class="choices-btn btn second-choice">
                <p>Nether</p>
            </a>
        </div>

        <div class="last-btn-separator btn-separator desktop-sparator">
            <a class="choices-btn btn third-choice">
                <p>End</p>
            </a>
            <a class="choices-btn btn">
                <p>Choice 4</p>
            </a>
        </div>

    </div>

    <div class="fullscreen-btn">
        <span class="material-symbols-outlined fullscreen-enter">fullscreen</span>
        <span class="material-symbols-outlined fullscreen-exit hidden">fullscreen_exit</span>

    </div>

    <div class="inventory storage-interface">
        <span class="close-inventory">X</span>
        <h2 class="storage-title inventory-title">Inventory</h2>
        <div class="inventory-grid">
        </div>
        <div class="craft-grid-container">
            <h2 class="storage-title craft-title">Crafting</h2>
        </div>
    </div>

    <div class="rotation-warning">
        <span class="material-symbols-outlined">screen_rotation</span>
        <p>Change your screen's rotation</p>
    </div>

    <div class="item-in-mouse">
        <div class="item">
            <img src="" alt="">
            <span></span>
        </div>
    </div>

    <iframe class="php-executer" src=""></iframe>
</body>

</html>