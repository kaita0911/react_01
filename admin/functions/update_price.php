<?php
include_once('../../includes/config.php');
header('Content-Type: application/json; charset=utf-8');

$id    = intval(isset($_POST['id']) ? $_POST['id'] : 0);
$price = floatval(isset($_POST['price']) ? $_POST['price'] : 0);

if ($id <= 0 || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {

    // Cập nhật tên trong bảng articlelist_price
    $sql = "UPDATE {$GLOBALS['db_sp']}.articlelist_price
     SET price = ?
     WHERE articlelist_id = ?";
    $ok = $GLOBALS['sp']->execute($sql, [$price, $id]);
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật DB']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
