<?php
session_start();
include_once('../includes/config.php');

$key = isset($_POST['key']) ? $_POST['key'] : '';
$quantity = intval(isset($_POST['quantity']) ? $_POST['quantity'] : 1);
$checked = isset($_POST['checked']) ? (bool)$_POST['checked'] : true;


if (!isset($_SESSION['cart'][$key])) {
  echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.']);
  exit;
}

// Cập nhật số lượng
$_SESSION['cart'][$key]['quantity'] = $quantity;
$_SESSION['cart'][$key]['checked'] = $checked;

echo json_encode([
  'success' => true,
]);
exit;
