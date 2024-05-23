<?php

function takeItem($inventory)
{
    $inventorySlot = $_GET["inventorySlot"];
    $inventory['mouse']["item"] = $inventory["inventory"][$inventorySlot]["item"];
    $inventory['mouse']["count"] = $inventory["inventory"][$inventorySlot]["count"];

    $inventory["inventory"][$inventorySlot]["item"] = "";
    $inventory["inventory"][$inventorySlot]["count"] = 0;
    file_put_contents('inventory.json', json_encode($inventory));
    header('Location: /');
}

function writeJson($inventory, $data)
{
    $data = json_decode($data, true);
    // print_r($data);
    file_put_contents('inventory.json', json_encode($data));
    header('Location: /');
}
