<?php
ob_start();   // ⭐ thêm dòng này
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../functions/sendOrderEmails.php");

// Nhận JSON từ fetch
$data = json_decode(file_get_contents("php://input"), true);

$name    = isset($data['name']) ? $data['name'] : '';
$phone   = isset($data['phone']) ? $data['phone'] : '';
$email   = isset($data['email']) ? $data['email'] : '';
$address = isset($data['address']) ? $data['address'] : '';
$message = isset($data['message']) ? $data['message'] : '';

if (!$name || !$phone || !$message) {
    echo json_encode(["success" => false, "msg" => "Thiếu thông tin"]);
    exit;
}

// Lưu database
$GLOBALS['sp']->execute(
    "INSERT INTO {$GLOBALS['db_sp']}.contact
     (name, phone, email, address, message, dated)
     VALUES (?, ?, ?, ?, ?, NOW())",
    [$name, $phone, $email, $address, $message]
);

// Gửi email
$contactData = [
    'name'    => $name,
    'phone'   => $phone,
    'email'   => $email,
    'address' => $address,
    'message' => $message
];

$emails = [
    'admin' => 'kaita0911@gmail.com'
];

$sent = sendContactEmail($contactData, $emails);

// Trả JSON cho React
echo json_encode([
    "success" => true,
    "email_sent" => $sent
]);