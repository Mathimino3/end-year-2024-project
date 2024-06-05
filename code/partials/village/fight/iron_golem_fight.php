<?php
if ($requireLocation === "gameplayRoot") : ?>
    <script>
        // setting the variales for the game
        let speed = 3;
    </script>

    <?php $mobs = json_decode(file_get_contents("./mobs.json"), true);
    require_once("cursor_game.php"); ?>
<?php endif ?>