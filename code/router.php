<?php
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);
$mobs = json_decode(file_get_contents("./mobs.json"), true);

$inventory = json_decode(file_get_contents("./inventory/inventory.json"), true);
$regionJson = isset($_GET["region"]) ? json_decode(file_get_contents("./regions/" . $_GET["region"] . ".json"), true) : null;

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
    case "fighting":
        //entering and leaving afight
        $playerInfos["mobFight"] = $playerInfos["mobFight"] ? $playerInfos["mobFight"] = false : $playerInfos["mobFight"] = true;
        $playerInfos["mobPv"] = null;
        $playerInfos["mob"] = "";
        file_put_contents('./player_infos.json', json_encode($playerInfos));
        break;
    case "fight":
        if ($_GET["fightAction"] === "attack") {
            attack($inventory, $playerInfos, $mobs, $_GET["damage"], $_GET["scene"]);
        }
        if ($_GET["fightAction"] === "getAttacked") {
            getAttacked($playerInfos, $mobs, $_GET["damage"], $_GET["scene"]);
        }
        break;
    case "resetAll":
        resetAll($playerInfos, $inventory);
        break;
}

header('Location: /');
