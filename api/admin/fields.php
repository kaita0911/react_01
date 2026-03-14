<?php

/* ================= CORS ================= */

if(isset($_SERVER['HTTP_ORIGIN'])){
    header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD']==='OPTIONS'){
    http_response_code(200);
    exit();
}

require_once("../../includes/config.php");

/* ================= GET PARAM ================= */

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

/* =================================================
   SWITCH
================================================= */

switch($act){

/* =================================================
   LIST
================================================= */

case "list":

$rows = $GLOBALS['sp']->getAll("
SELECT *
FROM {$GLOBALS['db_sp']}.fields
ORDER BY id DESC
");

echo json_encode([
    "status"=>true,
    "data"=>$rows
],JSON_UNESCAPED_UNICODE);

exit;


/* =================================================
   DETAIL
================================================= */

case "detail":

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$row = $GLOBALS['sp']->getRow("
SELECT *
FROM {$GLOBALS['db_sp']}.fields
WHERE id=?
",[$id]);

echo json_encode([
    "status"=>true,
    "data"=>$row
],JSON_UNESCAPED_UNICODE);

exit;


/* =================================================
   ADD
================================================= */

case "add":

$name  = isset($_POST['name']) ? trim($_POST['name']) : '';
$label = isset($_POST['label']) ? trim($_POST['label']) : '';
$type  = isset($_POST['type']) ? trim($_POST['type']) : 'input';
$target  = isset($_POST['target']) ? trim($_POST['target']) : 'article';
if(!$name){
    echo json_encode([
        "status"=>false,
        "msg"=>"Name is required"
    ]);
    exit;
}

$GLOBALS['sp']->Execute("
INSERT INTO {$GLOBALS['db_sp']}.fields
(name,label,type,target)
VALUES(?,?,?,?)
",[$name,$label,$type,$target]);

$id = $GLOBALS['sp']->Insert_ID();

echo json_encode([
    "status"=>true,
    "id"=>$id
]);

exit;


/* =================================================
   UPDATE
================================================= */

case "update":

$id    = isset($_POST['id']) ? intval($_POST['id']) : 0;
$label = isset($_POST['label']) ? trim($_POST['label']) : '';
$type  = isset($_POST['type']) ? trim($_POST['type']) : 'input';
$target  = isset($_POST['target']) ? trim($_POST['target']) : 'article';
if(!$id){
    echo json_encode([
        "status"=>false,
        "msg"=>"Invalid ID"
    ]);
    exit;
}

$GLOBALS['sp']->Execute("
UPDATE {$GLOBALS['db_sp']}.fields
SET label=?, type=?, target=?
WHERE id=?
",[$label, $type, $target, $id]);

echo json_encode([
    "status"=>true
]);

exit;


/* =================================================
   DELETE
================================================= */

case "delete":

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if($id){

    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.fields
    WHERE id=?
    ",[$id]);

}

echo json_encode([
    "status"=>true
]);

exit;


/* =================================================
   DEFAULT
================================================= */

default:

echo json_encode([
    "status"=>false,
    "msg"=>"Invalid action"
]);

exit;

}