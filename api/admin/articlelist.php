<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once("../../includes/config.php");
require_once("../../functions/function-api.php");

// CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$act = isset($_GET['act']) ? $_GET['act'] : 'list';

function jsonResponse($data)
{
    echo json_encode($data);
    exit;
}

function getCompId($module)
{

    $comp = $GLOBALS['sp']->getRow("
        SELECT id
        FROM {$GLOBALS['db_sp']}.component
        WHERE `do`=?
        LIMIT 1
    ", array($module));

    if(!$comp) {
        return 0;
    }

    return intval($comp['id']);
}

$actionFile = __DIR__ . "/articlelist/{$act}.php";

if(file_exists($actionFile)) {
    require $actionFile;
} else {
    echo json_encode([
        "status" => false,
        "message" => "Action không tồn tại"
    ]);
    exit;
}
