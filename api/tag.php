<?php

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../includes/config.php");

$langCode     = isset($_GET['lang']) ? $_GET['lang'] : '';
// Lấy từ DB → map code -> id
$langMap = [];
$languages = $GLOBALS['sp']->getAll("SELECT id, code FROM language");
foreach($languages as $row) {
    $langMap[$row['code']] = $row['id'];
}

// Nếu code không hợp lệ, default = 1 (vi)
$langid = isset($langMap[$langCode]) ? $langMap[$langCode] : 1;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        "success" => false,
        "message" => "Method not allowed"
    ]);
    exit;
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (!$slug) {
    echo json_encode([
        "success" => false,
        "message" => "Missing tag slug"
    ]);
    exit;
}
$sql = "
SELECT 
    a.id,
    a.img_thumb_vn,
    d.name,
    d.slug AS slug,
    d.short,
    d.keyword,
    p.price,p.priceold
FROM {$GLOBALS['db_sp']}.articlelist a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
    ON d.articlelist_id = a.id
   AND d.languageid = {$langid}
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
    ON p.articlelist_id = a.id
WHERE a.active = 1
  AND FIND_IN_SET(
        REPLACE('{$slug}', '-', ''),
        REPLACE(REPLACE(LOWER(d.keyword), ' ', ''), '-', '')
      )
ORDER BY a.num DESC
";

$articles = $GLOBALS['sp']->getAll($sql);
echo json_encode([
    "tag" => $slug,
    "total" => count($articles),
    "articles" => $articles
], JSON_UNESCAPED_UNICODE);
exit;
