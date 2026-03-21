<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$langCode     = isset($_GET['lang']) ? $_GET['lang'] : '';
if (!$slug) {
    echo json_encode(["error" => "Missing slug"]);
    exit;
}
// Lấy từ DB → map code -> id
$langMap = [];
$languages = $GLOBALS['sp']->getAll("SELECT id, code FROM language");
foreach($languages as $row) {
    $langMap[$row['code']] = $row['id'];
}

// Nếu code không hợp lệ, default = 1 (vi)
$langid = isset($langMap[$langCode]) ? $langMap[$langCode] : 1;

// ==========================
// 1️⃣ Kiểm tra MENU (page)
// ==========================
$menu = $GLOBALS['sp']->getRow("
    SELECT m.id, m.comp
    FROM {$GLOBALS['db_sp']}.menu m
    LEFT JOIN {$GLOBALS['db_sp']}.menu_detail d
      ON d.menu_id = m.id
    WHERE d.slug = '{$slug}' -- bất kỳ lang
    LIMIT 1
");

if ($menu) {
    $menuDetail = $GLOBALS['sp']->getRow("
        SELECT name, slug
        FROM {$GLOBALS['db_sp']}.menu_detail
        WHERE menu_id = {$menu['id']}
          AND languageid = {$langid} -- lang mới
        LIMIT 1
    ");

    echo json_encode([
        "type" => "page",
        "comp" => (int)$menu['comp'],
        "name" => $menuDetail['name'],
        "slug" => $menuDetail['slug'],
    ]);
    exit;
}
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
    "slug" => $menu['slug'],
    "content" => isset($article['content']) ? $article['content'] : ''
    ]);
    exit;
}

// ==========================
// 2️⃣ Kiểm tra CATEGORY
// ==========================
// 🔥 tìm category theo slug (BẤT KỲ ngôn ngữ)
$category = $GLOBALS['sp']->getRow("
    SELECT c.id, c.comp
    FROM {$GLOBALS['db_sp']}.categories c
    LEFT JOIN {$GLOBALS['db_sp']}.categories_detail d
      ON d.categories_id = c.id
    WHERE d.slug = '{$slug}'
    LIMIT 1
");

if ($category) {

    // 🔥 lấy lại theo language mới
    $catDetail = $GLOBALS['sp']->getRow("
        SELECT name, slug
        FROM {$GLOBALS['db_sp']}.categories_detail
        WHERE categories_id = {$category['id']}
          AND languageid = {$langid}
        LIMIT 1
    ");

    echo json_encode([
        "type" => "category",
        "comp" => (int)$category['comp'],
        "id"   => (int)$category['id'],
        "name" => $catDetail['name'],
        "slug" => $catDetail['slug']
    ]);
    exit;
}

// ==========================
// 3️⃣ Kiểm tra ARTICLE
// ==========================
// 🔥 tìm article theo slug (mọi ngôn ngữ)
$article = $GLOBALS['sp']->getRow("
    SELECT a.id, a.comp
    FROM {$GLOBALS['db_sp']}.articlelist a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
      ON d.articlelist_id = a.id
    WHERE d.slug = '{$slug}' AND a.active = 1
    LIMIT 1
");

if ($article) {

    // 🔥 lấy lại theo ngôn ngữ mới
    $detail = $GLOBALS['sp']->getRow("
        SELECT name, content, slug
        FROM {$GLOBALS['db_sp']}.articlelist_detail
        WHERE articlelist_id = {$article['id']}
          AND languageid = {$langid}
        LIMIT 1
    ");

    echo json_encode([
        "type"   => "article",
        "comp"   => (int)$article['comp'],
        "id"     => (int)$article['id'],
        "name"  => $detail['name'],
        "content" => $detail['content'],
        "slug"   => $detail['slug'] // 🔥 QUAN TRỌNG
    ]);
    exit;
}
// ==========================
// 4️⃣ Không tìm thấy
// ==========================
echo json_encode([
    "type" => "404"
]);
