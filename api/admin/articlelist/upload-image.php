<?php

header("Content-Type: application/json");

if(isset($_FILES['file']) && $_FILES['file']['error'] == 0) {

    $uploadFolder = 'hinh-anh/gallery/';
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];

    if(!in_array($ext, $allowed)) {
        echo json_encode(["status" => false,"message" => "File không hợp lệ"]);
        exit;
    }

    $filename = time().'_'.rand(1000, 9999).'.'.$ext;

    if(!move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$filename)) {
        echo json_encode(["status" => false,"message" => "Upload fail"]);
        exit;
    }

    echo json_encode([
        "status" => true,
        "path" => $uploadFolder.$filename
    ]);
}
