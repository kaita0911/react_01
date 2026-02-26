<?php
// ajax/wishlist.php
session_start();
include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

header('Content-Type: application/json');

$session_id = session_id();
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action     = isset($_POST['action']) ? $_POST['action'] : '';

if ($product_id <= 0 || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['total' => 0]);
    exit;
}

// ➕ Thêm wishlist
if ($action === 'add') {
    $GLOBALS['sp']->execute("
        INSERT IGNORE INTO {$GLOBALS['db_sp']}.wishlist (session_id, product_id)
        VALUES ('{$session_id}', {$product_id})
    ");
}

// ❌ Xoá wishlist
if ($action === 'remove') {
    $GLOBALS['sp']->execute("
        DELETE FROM {$GLOBALS['db_sp']}.wishlist
        WHERE session_id = '{$session_id}'
          AND product_id = {$product_id}
    ");
}

// 🔢 Đếm tổng wishlist
$row = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.wishlist
    WHERE session_id = '{$session_id}'
");

echo json_encode([
    'total' => (int)$row['total']
]);
