<?php
include_once('../../includes/config.php');
header('Content-Type: application/json; charset=utf-8');

$id     = intval(isset($_POST['id']) ? $_POST['id'] : 0);
$table  = trim(isset($_POST['table']) ? $_POST['table'] : '');
$column = trim(isset($_POST['column']) ? $_POST['column'] : '');
$value  = intval(isset($_POST['value']) ? $_POST['value'] : -1);

if ($id <= 0 || $table === '' || $column === '' || ($value !== 0 && $value !== 1)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

$allowedTables = ['categories', 'articlelist', 'language', 'component', 'infos', 'colors', 'menu'];
$allowedColumns = ['active', 'home', 'show', 'hot', 'new', 'mostview', 'open', 'is_default'];

if (!in_array($table, $allowedTables) || !in_array($column, $allowedColumns)) {
    echo json_encode(['success' => false, 'message' => 'Không được phép thay đổi']);
    exit;
}

try {
    $sql = "UPDATE {$GLOBALS['db_sp']}.$table SET $column = $value WHERE id = $id";
    // Debug xem câu SQL
    error_log("TOGGLE SQL: $sql");

    $GLOBALS['sp']->query($sql);

    echo json_encode(['success' => true, 'value' => $value]);
} catch (Exception $e) {
    error_log("TOGGLE ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
