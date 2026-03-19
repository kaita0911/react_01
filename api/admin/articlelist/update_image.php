<?php

$module = isset($_POST['module']) ? trim($_POST['module']) : '';
$slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$comp_id = getCompId($module);

// Chuẩn hóa module + folder
$module = preg_replace('/[^a-zA-Z0-9_-]/', '', $module);
$uploadFolder = 'hinh-anh/' . $module . '/';
$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

if ($id && isset($_FILES['image']) && $_FILES['image']['name'] != '') {

    // Tạo folder nếu chưa tồn tại
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Kiểm tra file hợp lệ
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(["status" => false, "message" => "File không hợp lệ"]);
        exit;
    }

    // Chuẩn SEO tên file
    // $rawName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    // $slugName = preg_replace('/[^a-z0-9]+/i', '-', strtolower($rawName));

    // Tạo tên file mới (luôn mới)
    $filename = $slug . '-' . time() . '.' . $ext;
    $filePath = $uploadDir . $filename;

    // Xóa file cũ nếu có
    $old = $GLOBALS['sp']->getOne("
        SELECT img_thumb_vn
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE id=?
    ", [$id]);

    if ($old) {
        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;
        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    // Upload file mới
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {

        // Cập nhật DB luôn với file mới
        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.articlelist
            SET img_thumb_vn=?
            WHERE id=?
        ", [$uploadFolder . $filename, $id]);

        // Trả về frontend
        echo json_encode([
            "status" => true,
            "image" => $uploadFolder . $filename, // hiển thị UI
            "filename" => $filename // lưu tên file
        ]);
        exit;
    }
}

echo json_encode([
    "status" => false
]);
