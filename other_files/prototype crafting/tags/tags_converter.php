<?php
$blocks = json_decode(file_get_contents("./tags/1_20_4_block_tags.json"), true);
$items = json_decode(file_get_contents("./tags/1_20_4_item_tags.json"), true);
// $newArray = array();
foreach ($items as $i => $mcItem) {
    if (!isset($blocks[$i])) {
        $item = array($i => $mcItem);
        // print_r($item);
        $blocks = array_merge($blocks, $item);
    }
    // $keysName = array_keys($blocks);
    // print_r($blocks[$keysName[count($blocks) - 1]]);
}
// print_r($blocks);
file_put_contents('./tags/new_tags.json', json_encode($blocks));
