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

/* ================= GET PARAM ================= */

$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = isset($_REQUEST['comp']) ? intval($_REQUEST['comp']) : 0;
$lang = isset($_REQUEST['lang']) ? intval($_REQUEST['lang']) : 1;


/* ================= BUILD TREE ================= */

function buildTree($rows)
{

    $map  = array();
    $tree = array();

    foreach($rows as $r) {
        $r['children'] = array();
        $map[$r['id']] = $r;
    }

    foreach($map as $id => $node) {

        if($node['parent_id'] && isset($map[$node['parent_id']])) {
            $map[$node['parent_id']]['children'][] = &$map[$id];
        } else {
            $tree[] = &$map[$id];
        }

    }

    return $tree;

}


/* ================= DELETE CHILD ================= */

function deleteChild($id)
{

    $rows = $GLOBALS['sp']->getAll("
    SELECT id
    FROM {$GLOBALS['db_sp']}.categories
    WHERE parent_id=?
    ", array($id));

    foreach($rows as $r) {
        deleteChild($r['id']);
    }

    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.categories
    WHERE id=?
    ", array($id));

    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.categories_detail
    WHERE categories_id=?
    ", array($id));

}


$actionFile = __DIR__ . "/category/{$act}.php";

if(file_exists($actionFile)) {
    require $actionFile;
} else {
    echo json_encode([
        "status" => false,
        "message" => "Action không tồn tại"
    ]);
    exit;
}
