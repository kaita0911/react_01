<?php
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', 0);

// ===== INCLUDE CHUẨN =====
require_once '../../includes/config.php';
require_once __DIR__ . '/function.php'; // ⭐ BẮT BUỘC (StripUnicode)

// ===== CHECK DB =====
if (!isset($GLOBALS['sp'])) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Không kết nối được DB'
    ]);
    exit;
}

$id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$comp = isset($_POST['comp']) ? (int)$_POST['comp'] : 0;

if ($id <= 0 || empty($_FILES['img_thumb_vn'])) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu'
    ]);
    exit;
}

$file = $_FILES['img_thumb_vn'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Upload lỗi'
    ]);
    exit;
}

// ===== XÁC ĐỊNH THƯ MỤC =====
$predix = '../../';

switch ($comp) {
    case 7:
        $uploadDir = $predix . 'hinh-anh/banner/';
        $uploadDir_pre = 'hinh-anh/banner/';
        break;
    case 73:
    case 74:
    case 75:
        $uploadDir = $predix . 'hinh-anh/quang-cao/';
        $uploadDir_pre = 'hinh-anh/quang-cao/';
        break;
    case 2:
        $uploadDir = $predix . 'hinh-anh/thumbs/';
        $uploadDir_pre = 'hinh-anh/thumbs/';
        break;
    case 27:
        $uploadDir = $predix . 'hinh-anh/dich-vu/';
        $uploadDir_pre = 'hinh-anh/dich-vu/';
        break;
    case 10:
        $uploadDir = $predix . 'hinh-anh/du-an/';
        $uploadDir_pre = 'hinh-anh/du-an/';
        break;
    case 1:
        $uploadDir = $predix . 'hinh-anh/tin-tuc/';
        $uploadDir_pre = 'hinh-anh/tin-tuc/';
        break;
    case 29:
        $uploadDir = $predix . 'hinh-anh/doi-tac/';
        $uploadDir_pre = 'hinh-anh/doi-tac/';
        break;
    default:
        $uploadDir = $predix . 'hinh-anh/thong-tin-chung/';
        $uploadDir_pre = 'hinh-anh/thong-tin-chung/';
}

// ===== TẠO TÊN FILE =====
$nameOnly = pathinfo($file['name'], PATHINFO_FILENAME);
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$slug = StripUnicode($nameOnly);
$filename = $slug . '-' . time() . '.' . $ext;

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$uploadPath = $uploadDir . $filename;

// ===== XÓA ẢNH CŨ =====
$oldImg = $GLOBALS['sp']->getOne("
    SELECT img_thumb_vn
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE id = $id
");

if (!empty($oldImg) && file_exists($predix . $oldImg)) {
    @unlink($predix . $oldImg);
}

// ===== UPLOAD =====
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Không lưu được file'
    ]);
    exit;
}

// ===== UPDATE DB =====
$newImg = $uploadDir_pre . $filename;

$GLOBALS['sp']->query("
    UPDATE {$GLOBALS['db_sp']}.articlelist
    SET img_thumb_vn = '$newImg'
    WHERE id = $id
");

// ===== DONE =====
ob_clean();
echo json_encode([
    'success' => true,
    'img' => $newImg
]);
exit;
