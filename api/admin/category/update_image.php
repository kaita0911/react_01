<?php

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if($id && isset($_FILES['image']) && $_FILES['image']['name'] != '') {

    $uploadFolder = 'hinh-anh/cate/';
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /* LẤY HÌNH CŨ */
    $old = $GLOBALS['sp']->getOne("
 SELECT img_vn
 FROM {$GLOBALS['db_sp']}.categories
 WHERE id=?
 ", [$id]);

    if($old) {
        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;
        $realPath = realpath($oldPath);
        $baseDir  = realpath($_SERVER['DOCUMENT_ROOT'].'/hinh-anh/');

        if (
            $realPath &&
            $baseDir &&
            strpos($realPath, $baseDir) === 0 &&
            is_file($realPath)
        ) {
            unlink($realPath);
        }
    }

    /* TẠO FILE NAME */
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = time().'_'.rand(1000, 9999).'.'.$ext;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$filename)) {
        $image = $uploadFolder.$filename.'?t='.time(); // tránh cache

        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.categories
            SET img_vn=?
            WHERE id=?
        ", [$uploadFolder.$filename, $id]); // Lưu trong DB vẫn là path gốc

        echo json_encode([
            "status" => true,
            "image" => $image // trả về kèm ?t để hiển thị ngay
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false
]);
