<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
include_once(__DIR__ . "/../includes/config.php");
$lang     = isset($_GET['lang']) ? $_GET['lang'] : '1';
$sql = "
SELECT *
FROM {$GLOBALS['db_sp']}.language
WHERE active = 1;
";
$languages = $sp->getAll($sql);
echo json_encode($languages, JSON_UNESCAPED_UNICODE);
