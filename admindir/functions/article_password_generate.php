<?php
include_once('../../includes/config.php');
header('Content-Type: application/json; charset=utf-8');

/**
 * Tạo mật khẩu ngẫu nhiên
 */
function generatePassword($length = 12)
{
    // Loại bỏ ký tự dễ nhầm: O, 0, I, l
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    return substr(str_shuffle($chars), 0, $length);
}

$article_id = intval(isset($_POST['article_id']) ? $_POST['article_id'] : 0);

if ($article_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Article ID không hợp lệ'
    ]);
    exit;
}

try {
    // 1. Sinh mật khẩu ngẫu nhiên
    $password = generatePassword(12);

    // 2. Lưu DB (PLAIN TEXT)
    $sql = "
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_password
        (articlelist_id, password_hash, created_at)
        VALUES (?, ?, NOW())
    ";

    $ok = $GLOBALS['sp']->execute($sql, [
        $article_id,
        $password
    ]);
    if (!$ok) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể lưu mật khẩu'
        ]);
        exit;
    }

    // 4. Trả mật khẩu gốc cho admin (chỉ 1 lần)
    echo json_encode([
        'success'  => true,
        'password' => $password
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
