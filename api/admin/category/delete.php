<?php

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if($id) {
    deleteChild($id);
}

echo json_encode(array(
    "status" => true
));
