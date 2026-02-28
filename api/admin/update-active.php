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
// ===== Lấy dữ liệu POST =====
$id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
$table  = isset($_POST['table']) ? trim($_POST['table']) : '';
$column = isset($_POST['column']) ? trim($_POST['column']) : '';
$value  = isset($_POST['value']) ? intval($_POST['value']) : -1;


// ===== Validate =====
if ($id <= 0 || $table === '' || $column === '' || ($value !== 0 && $value !== 1)) {
    echo json_encode([
        'success' => false,
        'message' => 'Dữ liệu không hợp lệ'
    ]);
    exit;
}


// ===== Danh sách cho phép =====
$allowedTables = [
    'categories',
    'articlelist',
    'language',
    'component',
    'infos',
    'colors',
    'menu'
];

$allowedColumns = [
    'active',
    'home',
    'show',
    'hot',
    'new',
    'mostview',
    'open',
    'is_default'
];

if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
    echo json_encode([
        'success' => false,
        'message' => 'Không được phép thay đổi'
    ]);
    exit;
}


try {

    // ===== Câu SQL =====
    $sql = "UPDATE {$GLOBALS['db_sp']}.$table 
            SET $column = ? 
            WHERE id = ?";

    $GLOBALS['sp']->execute($sql, [$value, $id]);

    echo json_encode([
        'success' => true,
        'value'   => $value
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

exit;
?>