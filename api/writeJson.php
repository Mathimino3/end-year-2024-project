<?php
//This file is just a script that write on a json file when called in js
$file = $_GET["file"];
$data = json_decode($_GET["data"], true);

file_put_contents($file, json_encode($data));
