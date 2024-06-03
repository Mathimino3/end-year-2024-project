<?php
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);
$inventory = json_decode(file_get_contents("./inventory/inventory.json"), true);
$regionJson = null;
if (isset($_GET["region"])) {
    $regionJson = json_decode(file_get_contents("./regions/" . $_GET["region"] . ".json"), true);
}
require_once('./fn.php');

$action = $_GET['action'];

switch ($action) {
    case "changeScene":
        changeScene($playerInfos, $_GET["region"], $_GET["scene"]);
        break;
    case "breakBlocks":
        breakBlocks($playerInfos, $inventory, $regionJson, $_GET["region"], $_GET["scene"]);
        break;
    case "placeBlocks":
        placeBlocks($playerInfos, $inventory, $regionJson, $_GET["scene"]);
        break;
}

header('Location: /');
