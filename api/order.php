<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../functions/sendOrderEmails.php");

// ====== Preflight CORS ======
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ====== Chỉ cho POST ======
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

// ====== Nhận JSON từ React ======
$data = json_decode(file_get_contents("php://input"), true);

// ====== Lấy dữ liệu khách ======
$customer = $data["customer"];
$products = $data["products"];
$total    = $data["total"];

$name    = $customer["name"];
$phone   = $customer["phone"];
$email   = $customer["email"];
$address = $customer["address"];
$note    = $customer["note"];

$city     = $customer["province"];
$district = $customer["district"];
$wards    = $customer["ward"];
// ===== Tính tổng số lượng =====
$totalQty = 0;
foreach ($products as $item) {
    $totalQty += (int)$item['qty'];
}

// ===== Mặc định =====
$payment = isset($customer["payment"]) ? $customer["payment"] : "COD";
if ($payment === "bank") {
    $payment = "Chuyển khoản";
} else {
    $payment = "COD";
}
$shipped = 0;

// ====== Tạo mã đơn ======
$today = date('Y-m-d');

$countToday = $GLOBALS['sp']->getOne("
    SELECT COUNT(*) 
    FROM {$GLOBALS['db_sp']}.orders
    WHERE DATE(created_at) = ?
", [$today]);

$number = str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);
$order_code = 'DH' . date('Ymd') . $number;

// ====== Lưu đơn hàng ======
$sql = "INSERT INTO {$GLOBALS['db_sp']}.orders 
(order_code, name, phone, email, address, thanhpho, quanhuyen, phuongxa, content, qty, descs, phiship, totalend, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$GLOBALS['sp']->execute($sql, [
    $order_code,
    $name,
    $phone,
    $email,
    $address,
    $city,
    $district,
    $wards,
    $note,
    $totalQty,
    $payment,
    $shipped,
    $total
]);

$order_id = $GLOBALS['sp']->Insert_ID();

// ====== Lưu chi tiết đơn ======
foreach ($products as $item) {

    $productImageUrl = $config['BASE_URL'] . '/' . ltrim($item['image'], '/');

    $itemTotal = $item['price'] * $item['qty'];

    $productName = $item['title'];

    $GLOBALS['sp']->execute(
        "INSERT INTO {$GLOBALS['db_sp']}.orders_line 
        (order_id, product_name, product_id, product_image, qty, product_price, tamtinh)
        VALUES (?, ?, ?, ?, ?, ?, ?)",
        [
            $order_id,
            $productName,
            $item['id'],
            $productImageUrl,
            $item['qty'],
            $item['price'],
            $itemTotal
        ]
    );
}

// ====== Gửi email ======
$orderData = [
    'id' => $order_code,
    'customer_name' => $name,
    'phone' => $phone,
    'email' => $email,
    'address' => $address,
    'wards' => $wards,
    'district' => $district,
    'city' => $city,
    'content' => $note,
    'payment' => $payment,
    'shipped' => $shipped,
    'total' => $total,
    'cart' => $products
];

sendOrderEmails($orderData, $config['BASE_URL']);

// ====== Trả kết quả ======
ob_clean();
echo json_encode([
    "success" => true,
    "order_code" => $order_code,
]);
exit;