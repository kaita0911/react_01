<?php

$ids = isset($_POST['id']) ? $_POST['id'] : array();
$nums = isset($_POST['num']) ? $_POST['num'] : array();

if(empty($ids) || empty($nums)) {
    jsonResponse(array("status" => false));
}

for($i = 0;$i < count($ids);$i++) {

    $id = intval($ids[$i]);
    $num = intval($nums[$i]);

    $GLOBALS['sp']->Execute("
       UPDATE {$GLOBALS['db_sp']}.articlelist
       SET num=?
       WHERE id=?
   ", array($num,$id));
}

jsonResponse(array("status" => true));
