<?php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../includes/config.php");

// ===== Preflight =====
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ===== Chỉ cho GET =====
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array(
        "success" => false,
        "message" => "Method not allowed"
    ));
    exit;
}

//
// 🔥 LẤY comp từ URL
//
$comp = isset($_GET['comp']) ? intval($_GET['comp']) : "";
// nếu không truyền → mặc định comp = 7

// ===== Lấy banner =====
$rs_banner = $GLOBALS['sp']->getAll("
    SELECT 
        a.id,
        a.img_thumb_vn,
        d.slug AS slug,
        d.name
    FROM {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
        ON d.articlelist_id = a.id
        AND d.languageid = 1
    WHERE a.active = 1 
      AND a.comp = {$comp}
    ORDER BY a.id DESC
");

// ===== Trả JSON =====
echo json_encode(array(
    "success" => true,
    "comp" => $comp,
    "data" => $rs_banner
));

exit;
