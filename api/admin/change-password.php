<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '0'); // local http
session_start();
// var_dump($_SESSION['admin_id']);
// exit;
require_once("../../includes/config.php");

$data = json_decode(file_get_contents("php://input"), true);

$old_password = trim(isset($data['old_password']) ? $data['old_password'] : '');
$new_password = trim(isset($data['new_password']) ? $data['new_password'] : '');

if ($old_password === '' || $new_password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Thiếu dữ liệu"
    ]);
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    echo json_encode([
        "status" => false,
        "message" => "Chưa đăng nhập"
    ]);
    exit;
}

$admin_id = $_SESSION['admin_id'];

$user = $GLOBALS['sp']->getRow(
    "SELECT password FROM {$GLOBALS['db_sp']}.admin WHERE id=?",
    [$admin_id]
);

if (!$user) {
    echo json_encode([
        "status" => false,
        "message" => "Không tìm thấy tài khoản"
    ]);
    exit;
}

$current_password = $user['password'];

// ⭐ KIỂM TRA ĐÚNG
if (!password_verify($old_password, $current_password)) {
    echo json_encode([
        "status" => false,
        "message" => "Mật khẩu cũ không đúng"
    ]);
    exit;
}


// ⭐ HASH MẬT KHẨU MỚI
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);


// ⭐ UPDATE
$GLOBALS['sp']->execute(
    "UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE id=?",
    [$new_hash, $admin_id]
);

echo json_encode([
    "status" => true,
    "message" => "Đổi mật khẩu thành công"
]);
exit;