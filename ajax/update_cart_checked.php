<?php
session_start();
// --- Kiểm tra dữ liệu ---
if (!isset($_POST['key']) || !isset($_POST['checked'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu.']);
    exit;
}

$key = $_POST['key'];
$checked = $_POST['checked'] ? true : false;
// --- Kiểm tra session giỏ hàng ---
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống.']);
    exit;
}

// --- Cập nhật trạng thái check ---
if ($key === 'all') {
    foreach ($_SESSION['cart'] as &$item) {
        $item['checked'] = $checked;
    }
    unset($item);
} else {
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['checked'] = $checked;
    }
}

echo json_encode([
    'success' => true,
]);
exit;
