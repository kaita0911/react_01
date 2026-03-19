<?php

$module = isset($_POST['module']) ? trim($_POST['module']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$comp_id = getCompId($module);
if($id && isset($_FILES['image']) && $_FILES['image']['name'] != '') {

    $old = $GLOBALS['sp']->getOne("
SELECT img_thumb_vn
FROM {$GLOBALS['db_sp']}.articlelist
WHERE id=?
", [$id]);

    if ($old) {

        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;

        $realPath = realpath($oldPath);
        $baseDir  = realpath($_SERVER['DOCUMENT_ROOT'].'/hinh-anh/');

        if (
            $realPath &&
            $baseDir &&
            strpos($realPath, $baseDir) === 0 &&   // nằm trong hinh-anh
            is_file($realPath) &&                  // là file thật
            file_exists($realPath)                // tồn tại
        ) {
            unlink($realPath);
        }
    }
    $uploadFolder = 'hinh-anh/';
    switch($comp_id) {
        case 1:
            $uploadFolder .= 'tin-tuc/';
            break;

        case 2:
            $uploadFolder .= 'thumbs/';
            break;
        case 77:
            $uploadFolder .= 'banner/';
            break;

        default:
            $uploadFolder .= 'thong-tin-chung/';
    }
    /* TẠO FILE NAME */

    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = time().'_'.rand(1000, 9999).'.'.$ext;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$filename)) {

        $image = $uploadFolder.$filename;

        $GLOBALS['sp']->Execute("
    UPDATE {$GLOBALS['db_sp']}.articlelist
    SET img_thumb_vn=?
    WHERE id=?
    ", [$image,$id]);

        echo json_encode([
            "status" => true,
            "image" => $image
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false
]);
