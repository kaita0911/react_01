<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php"); // kết nối DB

// Lấy tất cả ngôn ngữ active, default lên đầu
$sql = "
    SELECT id, code, name, is_default
    FROM {$GLOBALS['db_sp']}.language
    WHERE active = 1
    ORDER BY is_default DESC, id ASC
";

$languages = $sp->getAll($sql);

// Lấy luôn ngôn ngữ mặc định (dễ dùng phía frontend)
$defaultLanguage = null;
foreach ($languages as $lang) {
    if ($lang['is_default'] == 1) {
        $defaultLanguage = $lang;
        break;
    }
}

$response = [
    "default_language" => $defaultLanguage, // object mặc định
    "languages" => $languages               // mảng tất cả ngôn ngữ
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
