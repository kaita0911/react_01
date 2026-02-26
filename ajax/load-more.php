<?php
include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$sql = "
SELECT a.id, a.num, a.img_thumb_vn,
       d.name AS name_detail, d.unique_key,d.content, p.price, p.priceold
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d 
    ON a.id = d.articlelist_id AND d.languageid = {$langid}
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p 
    ON p.articlelist_id = a.id
{$joinCate}
WHERE a.active = 1 AND a.comp = 2 {$whereCate}
ORDER BY {$orderBy}
LIMIT {$start}, {$per_Page}
";

$data = $sp->getAll($sql);

foreach ($data as $item) {
    echo '<div class="item">' . $item['name'] . '</div>';
}
