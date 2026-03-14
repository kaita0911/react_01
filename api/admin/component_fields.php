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
   LIST FIELD CỦA COMPONENT
================================================= */

case "list":

    $component = isset($_GET['component']) ? intval($_GET['component']) : 0;
    $target = isset($_GET['target']) ? $_GET['target'] : '';

    $sql = "
    SELECT 
        f.id, cf.field_id,
        f.name,
        f.label,
        f.type,
        f.target
    FROM {$GLOBALS['db_sp']}.component_fields cf
    LEFT JOIN {$GLOBALS['db_sp']}.fields f 
        ON f.id = cf.field_id
    WHERE cf.component_id = ?
    ";

    $params = [$component];

    if($target != ''){
        $sql .= " AND f.target = ?";
        $params[] = $target;
    }

    $sql .= " ORDER BY cf.position ASC";

    $rows = $GLOBALS['sp']->GetAll($sql,$params);

    echo json_encode([
        "status"=>true,
        "data"=>$rows
    ]);

break;


/* =================================================
   LIST FIELD CHƯA GÁN
================================================= */

case "available":

$component = isset($_GET['component']) ? intval($_GET['component']) : 0;

$rows = $GLOBALS['sp']->getAll("
SELECT *
FROM {$GLOBALS['db_sp']}.fields
WHERE id NOT IN (
    SELECT field_id
    FROM {$GLOBALS['db_sp']}.component_fields
    WHERE component_id=?
)
ORDER BY id DESC
",[$component]);

echo json_encode([
"status"=>true,
"data"=>$rows
],JSON_UNESCAPED_UNICODE);

exit;



/* =================================================
   ADD FIELD TO COMPONENT
================================================= */

case "add":

$component = isset($_POST['component']) ? intval($_POST['component']) : 0;
$field     = isset($_POST['field']) ? intval($_POST['field']) : 0;

if(!$component || !$field){

echo json_encode([
"status"=>false,
"msg"=>"Missing component or field"
]);

exit;

}


/* position */

$max = $GLOBALS['sp']->getOne("
SELECT MAX(position)
FROM {$GLOBALS['db_sp']}.component_fields
WHERE component_id=?
",[$component]);

$pos = $max ? $max+1 : 1;


/* insert */

$GLOBALS['sp']->Execute("
INSERT INTO {$GLOBALS['db_sp']}.component_fields
(component_id,field_id,position)
VALUES(?,?,?)
",[$component,$field,$pos]);

echo json_encode([
"status"=>true
]);

exit;



/* =================================================
   DELETE FIELD
================================================= */

case "delete":

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if($id){

$GLOBALS['sp']->Execute("
DELETE FROM {$GLOBALS['db_sp']}.component_fields
WHERE id=?
",[$id]);

}

echo json_encode([
"status"=>true
]);

exit;



/* =================================================
   REORDER
================================================= */

case "reorder":

$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

foreach($ids as $num=>$id){

$GLOBALS['sp']->Execute("
UPDATE {$GLOBALS['db_sp']}.component_fields
SET position=?
WHERE id=?
",[$num+1,$id]);

}

echo json_encode([
"status"=>true
]);

exit;

/* =================================================
   SAVE
================================================= */

case "save":

    $component = isset($_POST['component']) ? intval($_POST['component']) : 0;
    
    $fields = isset($_POST['fields']) ? $_POST['fields'] : [];
    
    if(!$component){
    
    echo json_encode([
    "status"=>false
    ]);
    
    exit;
    
    }
    
    
    /* xoá field cũ */
    
    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.component_fields
    WHERE component_id=?
    ",[$component]);
    
    
    /* insert field mới */
    
    $pos = 1;
    
    foreach($fields as $field){
    
    $GLOBALS['sp']->Execute("
    INSERT INTO {$GLOBALS['db_sp']}.component_fields
    (component_id,field_id,position)
    VALUES(?,?,?)
    ",[$component,$field,$pos]);
    
    $pos++;
    
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