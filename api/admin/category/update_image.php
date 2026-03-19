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
            unlink($realPath); // xóa file cũ
        }
    }

    /* LẤY TÊN FILE GỐC + CHUẨN SEO */
    $rawName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    // ✅ chuẩn SEO: chỉ chữ thường, số, dấu "-", bỏ ký tự lạ
    $slugName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($rawName));
    $filename = $slugName . '.' . $ext;

    $filePath = $uploadDir . $filename;

    // Nếu trùng tên, thêm số ngẫu nhiên để tránh ghi đè file khác
    if(file_exists($filePath)) {
        $filename = $slugName . '-' . rand(1000, 9999) . '.' . $ext;
        $filePath = $uploadDir . $filename;
    }

    if(move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        $image = $uploadFolder . $filename; // lưu DB

        // Cập nhật DB
        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.categories
            SET img_vn=?
            WHERE id=?
        ", [$image, $id]);

        // Trả về frontend
        echo json_encode([
            "status" => true,
            "image" => $image . '?t=' . time(), // cache-busting
            "filename" => $filename
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false
]);
