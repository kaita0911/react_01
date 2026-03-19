<?php

$ids = isset($_POST['ids']) ? $_POST['ids'] : array();

if(!empty($ids)) {

    foreach($ids as $id) {

        $id = intval($id);

        if($id) {
            deleteChild($id);
        }

    }

}

echo json_encode(array(
    "status" => true
));
