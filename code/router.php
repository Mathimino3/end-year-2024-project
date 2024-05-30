<?php
session_start();

$inventory = json_decode(file_get_contents("inventory.json"), true);
require_once('./fn.php');

$action = $_GET['action'];

switch ($action) {
    case "takeItem":
        takeItem($inventory);
        break;
    case "writeJson":
        writeJson($inventory, $_GET["data"]);
        break;
}
