<?php
session_start();
require_once(__DIR__ . "/../includes/config.php");
header('Content-Type: application/json; charset=utf-8');

$article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
$password   = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($article_id <= 0 || $password === '') {
  echo json_encode(['success' => false]);
  exit;
}

// kiểm tra mật khẩu
$sql = "
    SELECT id
    FROM {$GLOBALS['db_sp']}.articlelist_password
    WHERE articlelist_id = ?
      AND password_hash = ?
    LIMIT 1
";

$row = $GLOBALS['sp']->getRow($sql, [$article_id, $password]);

if (!$row) {
  echo json_encode(['success' => false]);
  exit;
}
/* ✅ LƯU SESSION */
$_SESSION['article_password_ok'][$article_id] = true;


// ✅ MẬT KHẨU ĐÚNG
echo json_encode([
  'success' => true
]);
exit;
