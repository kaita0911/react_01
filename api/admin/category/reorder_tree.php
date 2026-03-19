<?php

$ids = isset($_POST['id']) ? $_POST['id'] : array();
$parents = isset($_POST['parent_id']) ? $_POST['parent_id'] : array();
$nums = isset($_POST['num']) ? $_POST['num'] : array();

if(!empty($ids)) {

    foreach($ids as $i => $id) {

        $id = intval($id);

        $parent = isset($parents[$i]) ? intval($parents[$i]) : 0;
        $num = isset($nums[$i]) ? intval($nums[$i]) : 0;

        $GLOBALS['sp']->Execute("
             UPDATE {$GLOBALS['db_sp']}.categories
             SET parent_id = ?, num = ?
             WHERE id = ?
         ", array($parent, $num, $id));

    }

}

echo json_encode(array(
    "status" => true
));
