<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$langid     = 1; // tùy hệ thống bạn
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(array(
        "success" => false,
        "message" => "Method not allowed"
    ));
    exit;
}
$contact = "Liên hệ";

//////////////////////////////////////////////////
// 🔥 Lấy category home
//////////////////////////////////////////////////

$sqlCat = "
SELECT 
    c.id,
    c.num,
    d.name,
    d.unique_key as slug
FROM {$GLOBALS['db_sp']}.categories AS c
INNER JOIN {$GLOBALS['db_sp']}.categories_detail AS d
    ON d.categories_id = c.id
   AND d.languageid = {$langid}
WHERE c.active = 1
  AND c.home = 1
ORDER BY c.num ASC
";

$categories = $GLOBALS['sp']->getAll($sqlCat);

//////////////////////////////////////////////////
// 🔥 Lấy product theo từng category
//////////////////////////////////////////////////

foreach ($categories as &$cat) {

    $catid = (int)$cat['id'];

    $sqlProduct = "
    SELECT DISTINCT
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

    INNER JOIN {$GLOBALS['db_sp']}.articlelist_categories AS ac
        ON ac.articlelist_id = a.id
       AND ac.categories_id = {$catid}

    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
        ON d.articlelist_id = a.id
       AND d.languageid = {$langid}

    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p
        ON p.articlelist_id = a.id

    WHERE a.active = 1
      AND a.comp = 2

    ORDER BY a.num DESC
    LIMIT 10
    ";

    $products = $GLOBALS['sp']->getAll($sqlProduct);
    $cat['products'] = $products;
}

unset($cat);

echo json_encode(array(
    "success" => true,
    "data" => $categories
));
exit;