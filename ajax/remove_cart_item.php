<?php
session_start();

$key = isset($_POST['key']) ? $_POST['key'] : '';
if ($key && isset($_SESSION['cart'][$key])) {
  unset($_SESSION['cart'][$key]);
}
// Khởi tạo biến tổng
$total_items = count(isset($_SESSION['cart']) ? $_SESSION['cart'] : []);
// $total_price = 0;
// $total_discount = 0;
// $total_qty = 0;

// // Nếu còn sản phẩm trong giỏ
// if (!empty($_SESSION['cart'])) {
//   foreach ($_SESSION['cart'] as $item) {
//     $price = floatval($item['price']);
//     $priceold = isset($item['priceold']) ? floatval($item['priceold']) : 0;
//     $qty = intval($item['quantity']);

//     $total_price += $price * $qty;
//     $total_qty += $qty;

//     if ($priceold > $price) {
//       $total_discount += ($priceold - $price) * $qty;
//     }
//   }
// }

echo json_encode([
  'success' => true,
  'total_items' => $total_items,
  // 'total_qty' => $total_qty,
  // 'total_price' => number_format($total_price, 0, ',', '.') . '₫',
  // 'total_discount' => number_format($total_discount, 0, ',', '.') . '₫'
]);
exit;
