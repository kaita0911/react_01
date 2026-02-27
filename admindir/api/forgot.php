<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once("../../includes/config.php");
require_once("../../functions/sendOrderEmails.php");

$data = json_decode(file_get_contents("php://input"), true);
$email = isset($data['email']) ? trim($data['email']) : '';

if ($email === '') {
    echo json_encode(["status" => false]);
    exit;
}
$info = $GLOBALS['sp']->getRow(
    "SELECT plain_text_vn FROM {$GLOBALS['db_sp']}.infos WHERE id=6"
);

if (!$info) {
    echo json_encode(["status" => false]);
    exit;
}

$adminEmail = trim($info['plain_text_vn']);


if ($email !== $adminEmail) {
    echo json_encode(["status" => false]);
    exit;
}
/* =====================================
   LẤY USER ADMIN (giả sử id = 1)
===================================== */
$user = $GLOBALS['sp']->getRow(
    "SELECT * FROM {$GLOBALS['db_sp']}.admin LIMIT 1"
);

if (!$user) {
    echo json_encode(["status" => false]);
    exit;
}
// Tạo mật khẩu mới
$newPass = substr(md5(time() . rand()), 0, 8);
$hashed = password_hash($newPass, PASSWORD_DEFAULT);

$GLOBALS['sp']->execute(
    "UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE email=?",
    [$hashed, $email]
);
// GỬI MAIL
$subject = "Khôi phục mật khẩu admin";
$body = "
  <p>Mật khẩu mới của bạn là:</p>
  <h2>{$newPass}</h2>
  <p>Vui lòng đăng nhập và đổi mật khẩu ngay.</p>
";
sendEmail($subject, $body, $email, null);

echo json_encode([
    "status" => true,
    "new_password" => $newPass
]);