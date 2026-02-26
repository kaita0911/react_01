<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
include_once(__DIR__ . "/../functions/function-api.php");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
$langid = 1;

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = 12;
$start = ($page - 1) * $perPage;

if (!$q) {
    echo json_encode([
        "success" => false,
        "message" => "Missing keyword"
    ]);
    exit;
}

$where  = '';
$params = array();
$keyword     = vn_to_slug($q);
if ($keyword !== '') {
  $where    = " AND d.name LIKE ? ";
  $params[] = '%' . $keyword . '%';
}
$sql = "
SELECT 
    a.id,
    a.img_thumb_vn,
    d.name,
    d.unique_key AS slug,
    d.short,
    p.price,
    p.priceold
FROM {$GLOBALS['db_sp']}.articlelist a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
    ON d.articlelist_id = a.id
   AND d.languageid = {$langid}
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
    ON p.articlelist_id = a.id
WHERE a.active = 1
  AND a.comp = 2
   $where
ORDER BY a.num DESC
LIMIT {$start}, {$perPage}";

$items = $GLOBALS['sp']->getAll($sql, $params);

$total = (int)$GLOBALS['sp']->getOne("
SELECT COUNT(*)
FROM {$GLOBALS['db_sp']}.articlelist a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
    ON d.articlelist_id = a.id
   AND d.languageid = {$langid}
WHERE a.active = 1
  AND a.comp = 2
  $where
",$params);
echo json_encode([
    "success" => true,
    "keyword" => $q,
    "items" => $items,
    "pagination" => [
        "page" => $page,
        "perPage" => $perPage,
        "total" => $total,
        "totalPages" => ceil($total / $perPage)
    ]
], JSON_UNESCAPED_UNICODE);

exit;