<?php
include_once('../../includes/config.php');
header('Content-Type: application/json; charset=utf-8');

$id = intval(isset($_POST['id']) ? $_POST['id'] : 0);

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID không hợp lệ'
    ]);
    exit;
}

try {
    $sql = "
        DELETE FROM {$GLOBALS['db_sp']}.articlelist_password
        WHERE id = ?
        LIMIT 1
    ";

    $ok = $GLOBALS['sp']->execute($sql, [$id]);

    if (!$ok) {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể xóa mật khẩu'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
