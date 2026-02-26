<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");


$langid     = 1; // tùy hệ thống bạn
// ===== Preflight =====
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}
$sql = "
SELECT 
    a.id,
    a.img_thumb_vn,
    d.name,
    d.unique_key as slug,

    COALESCE(
        NULLIF(p.price, 0),
        (
            SELECT att.price
            FROM {$GLOBALS['db_sp']}.articlelist_attributes att
            INNER JOIN {$GLOBALS['db_sp']}.articlelist_codes c
                ON c.id = att.code_id
            WHERE c.articlelist_id = a.id
              AND att.price > 0
            ORDER BY att.sort_order ASC
            LIMIT 1
        )
    ) AS price,

    p.priceold

FROM {$GLOBALS['db_sp']}.articlelist AS a

LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
    ON a.id = d.articlelist_id 
   AND d.languageid = {$langid}

LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p
    ON p.articlelist_id = a.id

WHERE a.active = 1 
  AND a.hot = 1
  AND a.comp = 2

ORDER BY a.num DESC
";

$articles = $GLOBALS['sp']->getAll($sql);

// // ===== Format dữ liệu cho React =====
// if (!empty($articles)) {
//     foreach ($articles as &$item) {

//         // Format giá
//         if (!empty($item['price']) && $item['price'] > 0) {
//             $item['price_formatted'] = number_format($item['price'], 0, ',', '.') . '₫';
//         } else {
//             $item['price_formatted'] = "Liên hệ";
//         }

//         $item['priceold_formatted'] = !empty($item['priceold'])
//             ? number_format($item['priceold'], 0, ',', '.') . '₫'
//             : '';

//         // Thêm link và image full path (khuyên dùng)
//         $item['image'] = $config['BASE_URL'] . '/' . ltrim($item['img_thumb_vn'], '/');
//         $item['slug']  = $item['unique_key'];
//     }
// }

echo json_encode(array(
    "success" => true,
    "data" => $articles
));

exit;