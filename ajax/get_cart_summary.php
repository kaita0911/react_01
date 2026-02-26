<?php
session_start();

$response = [
    "success" => true,
    "total_price" => 0,
    "total_old" => 0,
    "total_discount" => 0,
    "total_items" => 0,
    "total_qty" => 0,
];

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode($response);
    exit;
}

foreach ($_SESSION['cart'] as $item) {
    // Bỏ qua sản phẩm chưa check
    if (!isset($item['checked']) || $item['checked'] !== true) continue;



    $price = isset($item['price']) ? (float)$item['price'] : 0;
    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
    //$priceOld = isset($item['priceold']) ? (float)$item['priceold'] : $price;
    // Nếu priceold không có hoặc <=0 thì lấy price
    $priceOld = isset($item['priceold']) && (float)$item['priceold'] > 0 ? (float)$item['priceold'] : $price;

    $response['total_price'] += $price * $qty;
    $response['total_old']   += $priceOld * $qty;
    if ($priceOld > $price) {
        $response['total_discount'] += ($priceOld - $price) * $qty;
    }
    $response['total_qty'] += $qty;
    $response['total_items']++;
}

echo json_encode($response);
