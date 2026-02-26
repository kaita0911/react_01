<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$langid = 1;

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

$keyword = $GLOBALS['sp']->qstr('%' . $q . '%');

$sql = "
SELECT 
    d.name,a.img_thumb_vn,
    d.unique_key AS slug,
    p.price, p.priceold
FROM {$GLOBALS['db_sp']}.articlelist a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
    ON d.articlelist_id = a.id
   AND d.languageid = {$langid}
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
    ON p.articlelist_id = a.id
WHERE a.active = 1
  AND a.comp = 2
  AND d.name LIKE $keyword
ORDER BY a.num DESC
LIMIT 10
";

$list = $GLOBALS['sp']->getAll($sql);

echo json_encode($list, JSON_UNESCAPED_UNICODE);
exit;