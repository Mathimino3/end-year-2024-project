<?php
$scenes = json_decode(file_get_contents("./scenes.json"), true);
$playerInfos = json_decode(file_get_contents("./player_infos.json"), true);

require_once('./fn.php');

$action = $_GET['action'];

switch ($action) {
    case "changeScene":
        changeScene($playerInfos, $_GET["region"], $_GET["scene"]);
        break;
}

header('Location: /');
