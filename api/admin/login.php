<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '0'); // local HTTP
session_start();
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

$data = json_decode(file_get_contents("php://input"), true);

$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';

if ($username === '' || $password === '') {
    echo json_encode(["status" => false]);
    exit;
}

$sql = "SELECT * FROM {$GLOBALS['db_sp']}.admin WHERE username = ?";
$user = $GLOBALS['sp']->getRow($sql, [$username]);

if (!$user) {
    echo json_encode(["status" => false]);
    exit;
}

/* ==== CHECK PASSWORD ==== */

$isValid = false;

/* ----- MD5 cũ ----- */
if (md5($password) === $user['password']) {

    $isValid = true;

    // Nâng cấp lên hash mới
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $GLOBALS['sp']->execute(
        "UPDATE {$GLOBALS['db_sp']}.admin SET password=? WHERE id=?",
        [$newHash, $user['id']]
    );
}
/* ----- Hash mới ----- */
elseif (password_verify($password, $user['password'])) {
    $isValid = true;
}

if (!$isValid) {
    echo json_encode(["status" => false, "message" => "Sai mật khẩu"]);
    exit;
}

/* ==== SUCCESS ==== */
session_regenerate_id(true);
$_SESSION['admin_login'] = true;
$_SESSION['admin_id'] = $user['id'];
$_SESSION['admin_username'] = $user['username'];

echo json_encode([
    "status" => true,
    "data" => [
        "id" => $user['id'],
        "username" => $user['username']
    ]
]);
exit;