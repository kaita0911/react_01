<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!$slug) {
    echo json_encode(["error" => "Missing slug"]);
    exit;
}

$langid = (int)$langid;

// ==========================
// 1️⃣ Kiểm tra MENU (page)
// ==========================
$menu = $GLOBALS['sp']->getRow("
    SELECT m.id, m.comp, d.name, d.unique_key
    FROM {$GLOBALS['db_sp']}.menu m
    LEFT JOIN {$GLOBALS['db_sp']}.menu_detail d
      ON d.menu_id = m.id AND d.languageid = {$langid}
    WHERE d.unique_key = '{$slug}'
    LIMIT 1
");

if ($menu) {

    // lấy nội dung article theo component
    $article = $GLOBALS['sp']->getRow("
    SELECT a.id, d.name, d.content
    FROM {$GLOBALS['db_sp']}.articlelist a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
        ON d.articlelist_id = a.id AND d.languageid = {$langid}
    WHERE a.comp = {$menu['comp']}
    LIMIT 1
    ");

    echo json_encode([
    "type"   => "page",
    "comp"   => (int)$menu['comp'],
    "title"  => $menu['name'],
    "content"=> isset($article['content']) ? $article['content'] : ''
    ]);
    exit;
}

// ==========================
// 2️⃣ Kiểm tra CATEGORY
// ==========================
$category = $GLOBALS['sp']->getRow("
    SELECT c.id, c.comp, d.name
    FROM {$GLOBALS['db_sp']}.categories c
    LEFT JOIN {$GLOBALS['db_sp']}.categories_detail d
      ON d.categories_id = c.id AND d.languageid = {$langid}
    WHERE d.unique_key = '{$slug}'
    LIMIT 1
");

if ($category) {

    echo json_encode([
        "type" => "category",
        "comp" => (int)$category['comp'],
        "id"   => (int)$category['id'],
        "title"=> $category['name']
    ]);
    exit;
}

// ==========================
// 3️⃣ Kiểm tra ARTICLE
// ==========================
$article = $GLOBALS['sp']->getRow("
    SELECT a.id, a.comp, d.name, d.content
    FROM {$GLOBALS['db_sp']}.articlelist a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
      ON d.articlelist_id = a.id AND d.languageid = {$langid}
    WHERE d.unique_key = '{$slug}' AND a.active = 1
    LIMIT 1
");

if ($article) {

    echo json_encode([
        "type"   => "article",
        "comp"   => (int)$article['comp'],
        "id"     => (int)$article['id'],
        "title"  => $article['name'],
        "content"=> $article['content']
    ]);
    exit;
}

// ==========================
// 4️⃣ Không tìm thấy
// ==========================
echo json_encode([
    "type" => "404"
]);
