<?php
//require_once "../includes/config.php"; // file kết nối DB và biến $db_sp, v.v.
include_once('../../includes/config.php');
header('Content-Type: application/json; charset=utf-8');

$id   = intval(isset($_POST['id']) ? $_POST['id'] : 0);
$lang = intval(isset($_POST['lang']) ? $_POST['lang'] : 0);
$name = trim(isset($_POST['name']) ? $_POST['name'] : '');

if ($id <= 0 || $lang <= 0 || $name == '') {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {
    // Cập nhật tên trong bảng articlelist_detail
    $sql = "
        UPDATE {$GLOBALS['db_sp']}.articlelist_detail
        SET name = ?
        WHERE articlelist_id = ? AND languageid = ?
    ";
    $ok = $GLOBALS['sp']->execute($sql, [$name, $id, $lang]);

    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật DB']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
