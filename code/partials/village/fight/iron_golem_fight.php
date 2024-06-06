<?php
if ($requireLocation === "gameplayRoot") : ?>

    <?php $mobs = json_decode(file_get_contents("./mobs.json"), true);
    require_once("cursor_game.php"); ?>
<?php endif ?>