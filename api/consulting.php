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
$sql = "
SELECT 
    a.id,
    d.name,
    d.unique_key as slug
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d 
    ON a.id = d.articlelist_id 
   AND d.languageid = {$langid}
WHERE a.active = 1 
  AND a.comp = 72
ORDER BY a.num DESC
";

$articles = $GLOBALS['sp']->getAll($sql);

echo json_encode(array(
    "success" => true,
    "articles" => $articles
));
exit;