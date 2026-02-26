<?php
// if (!isset($cat1)) {
//     $cat1 = isset($_GET['cat1']) ? $_GET['cat1'] : '';
// }
$smarty->assign('randcss', rand(1, 10000000));
// $breadcrumbs = buildBreadcrumb($langid, $path_url, $cat1);
// $smarty->assign('breadcrumbs', $breadcrumbs);
// =================== Mặc định các biến ===================
$now = (new DateTime())->format('YmdHis');
$smarty->assign('currentTime', $now);
/////////////////////////// Load Menu Top ///////////////////////////
// 2️⃣ Lặp qua từng menu để lấy danh mục con (category)
$sql_cat = "SELECT c.id, c.comp, c.active,
           d.name AS name_detail, d.unique_key, c.img_vn
            FROM {$GLOBALS['db_sp']}.categories AS c
            LEFT JOIN {$GLOBALS['db_sp']}.categories_detail AS d
                ON d.categories_id = c.id AND d.languageid = {$langid}
            WHERE c.active = 1 and c.comp= 2
            ORDER BY c.num ASC";
$categories = $GLOBALS['sp']->getAll($sql_cat);
$smarty->assign('categories', $categories);
// Gom lại thành mảng theo id
$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c;
}
// 2️⃣ Lấy quan hệ cha - con từ bảng articlecat_related
$sql_rel = "SELECT category_id, related_id FROM {$GLOBALS['db_sp']}.categories_related order by category_id asc";
$relations = $GLOBALS['sp']->getAll($sql_rel);

// Gom nhóm con theo cha
$childrenMap = [];
$childIds = [];

foreach ($relations as $r) {
    $parentIds[] = $r['related_id'];
    $childrenMap[$r['related_id']][] = $r['category_id'];
}
$childIds = array_unique($childIds);

// 3️⃣ Tìm danh mục cha cấp 1 (những cái không nằm trong category_id)
$childIds = array_column($relations, 'category_id');
$rootCats = [];
foreach ($categories as $c) {
    if (!in_array($c['id'], $childIds)) {
        $rootCats[] = $c;
    }
}
// 4️⃣ Hàm đệ quy dựng cây danh mục
function buildCategoryTree($cat, $childrenMap, $catMap)
{
    $catId = $cat['id'];
    $cat['children'] = [];

    if (!empty($childrenMap[$catId])) {
        foreach ($childrenMap[$catId] as $childId) {
            if (isset($catMap[$childId])) {
                $cat['children'][] = buildCategoryTree($catMap[$childId], $childrenMap, $catMap);
            }
        }
    }

    return $cat;
}
// 5️⃣ Dựng cây danh mục hoàn chỉnh
$categoryTree = [];
foreach ($rootCats as $root) {
    $categoryTree[] = buildCategoryTree($root, $childrenMap, $catMap);
}

$smarty->assign("categories_tree", $categoryTree);

$sql = "SELECT m.id,m.comp,
               d.name AS name_detail, d.unique_key AS unique_key_detail, m.has_sub
        FROM {$GLOBALS['db_sp']}.menu AS m
        LEFT JOIN {$GLOBALS['db_sp']}.menu_detail AS d
            ON d.menu_id = m.id AND d.languageid = {$langid}
        WHERE m.active = 1
        ORDER BY m.num ASC";

$menus = $GLOBALS['sp']->getAll($sql);

// Gắn categoryTree theo comp
foreach ($menus as &$m) {
    if ($m['has_sub'] == 1) {
        // Lấy các category theo comp
        $m['categories'] = array_filter($categoryTree, function ($c) use ($m) {
            return $c['comp'] == $m['comp'];
        });
    } else {
        $m['categories'] = []; // Không có menu con
    }
}
unset($m);
$smarty->assign("menus", $menus);

$infos_ids = [
    1 => 'logoHome',
    2 => 'domain',
    3 => 'map',
    4 => 'banknd',
    5 => 'hotline',
    6 => 'email',
    7 => 'faceShare',
    11 => 'introfooter',
    12 => 'showcart',
    14 => 'formdangky',
    15 => 'seo',
    17 => 'headerscript',
    18 => 'bodyscript',
    19 => 'filter_product',
    20 => 'searchengine',
    21 => 'quickview',
    22 => 'makm'
];

foreach ($infos_ids as $id => $varname) {
    $rs = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id=$id");
    $rs = isset($rs) ? $rs : [];
    $smarty->assign($varname, $rs);
}
////get language
$allLangRows = $GLOBALS['sp']->GetAll("
    SELECT *
    FROM {$GLOBALS['db_sp']}.language 
    WHERE active = 1
");

$smarty->assign('alllanguages', $allLangRows);


// /////////////////////////// Footer ///////////////////////////
$rs_footer = $GLOBALS['sp']->getRow("
    SELECT f.*, fd.name, fd.content, fd.languageid, fd.address
    FROM {$GLOBALS['db_sp']}.footer AS f
    LEFT JOIN {$GLOBALS['db_sp']}.footer_detail AS fd 
        ON f.id = fd.footer_id
    WHERE fd.languageid = {$langid}
    ORDER BY f.id ASC
    LIMIT 1
");
$smarty->assign('footer', $rs_footer);
///* ========== Tin NỔI BẬT ========== */
$articles_hot = $sp->getAll("
SELECT a.id, a.img_thumb_vn, a.view,
       ad.name as name_detail, ad.unique_key
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad
    ON a.id = ad.articlelist_id AND ad.languageid = $langid
WHERE a.active = 1 AND a.comp = 1 AND a.hot = 1
ORDER BY a.view DESC
LIMIT 7
");
$smarty->assign('articles_hot', $articles_hot);
$articles_new = $sp->getAll("
SELECT a.id, a.img_thumb_vn, a.view,
       ad.name as name_detail, ad.unique_key
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad
    ON a.id = ad.articlelist_id AND ad.languageid = $langid
WHERE a.active = 1 AND a.comp = 1 AND a.new = 1
ORDER BY a.id DESC
LIMIT 7
");
$smarty->assign('articles_new', $articles_new);


//////Thong tin huu ich/////
$sql = "SELECT a.*, ad.name AS name_detail, ad.unique_key FROM 
        {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
        ON a.id = ad.articlelist_id
    WHERE 
        ad.languageid = {$langid}
        AND a.active = 1
        AND a.comp = 72
    ORDER BY 
        a.num DESC
";

$rs_consulting = $GLOBALS['sp']->getAll($sql);
$smarty->assign('consulting', $rs_consulting);
// /////////////////////////// Load Vouchers ///////////////////////////
// $img_popup = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.articlelist WHERE id =2121");
// $smarty->assign("img_popup", $img_popup);

// $img_contact = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.articlelist WHERE id =105");
// $smarty->assign("img_contact", $img_contact);

// $sql = "SELECT a.img_thumb_vn, ad.short, ad.content FROM 
//         {$GLOBALS['db_sp']}.articlelist AS a
//     LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
//         ON a.id = ad.articlelist_id
//     WHERE 
//         ad.languageid = {$langid}
//         AND a.active = 1
//         AND a.id = 101
// ";

// $rs_product = $GLOBALS['sp']->getRow($sql);
// // Lấy nội dung bài viết
// $content = $rs_product['content'];
// // Gọi hàm tạo mục lục
// list($newContent, $toc) = generate_toc($content);
// //
// $newContent = preg_replace(
//     '/(?<!<div class="img">)\s*(<img[^>]+>)/i',
//     '<div class="img">$1</div>',
//     $newContent
// );
// $smarty->assign('content', $newContent);
// $smarty->assign('toc', $toc);
// $smarty->assign('product', $rs_product);

////
$sql = "SELECT a.img_thumb_vn, ad.short, ad.content FROM 
        {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
        ON a.id = ad.articlelist_id
    WHERE 
        ad.languageid = {$langid}
        AND a.active = 1
        AND a.id = 102
";

$rs_news = $GLOBALS['sp']->getRow($sql);
$smarty->assign('articles', $rs_news);
///
$sql = "SELECT a.img_thumb_vn,ad.name, ad.short, ad.content FROM 
        {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
        ON a.id = ad.articlelist_id
    WHERE 
        ad.languageid = {$langid}
        AND a.active = 1
        AND a.id = 103
";

$rs_service = $GLOBALS['sp']->getRow($sql);
$smarty->assign('services', $rs_service);
//

$sql = "SELECT a.img_thumb_vn,ad.name, ad.short, ad.content FROM 
        {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
        ON a.id = ad.articlelist_id
    WHERE 
        ad.languageid = {$langid}
        AND a.active = 1
        AND a.id = 104
";

$rs_recruiment = $GLOBALS['sp']->getRow($sql);
$smarty->assign('recruiment', $rs_recruiment);

//

$sql = "SELECT a.img_thumb_vn,ad.name, ad.short, ad.content FROM 
        {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS ad 
        ON a.id = ad.articlelist_id
    WHERE 
        ad.languageid = {$langid}
        AND a.active = 1
        AND a.id = 105
";

$rs_contact = $GLOBALS['sp']->getRow($sql);
$smarty->assign('contact', $rs_contact);


////
$sql = "
SELECT a.img_thumb_vn,
       d.name AS name_detail, d.unique_key, d.short
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d 
    ON a.id = d.articlelist_id AND d.languageid = {$langid}
WHERE a.active = 1 AND a.comp = 32
ORDER BY a.num DESC
";
$feedback = $GLOBALS['sp']->getAll($sql);
$smarty->assign("feedback", $feedback);


// $sql = "
// SELECT a.img_thumb_vn,
//        d.name AS name_detail, d.unique_key, d.short
// FROM {$GLOBALS['db_sp']}.articlelist AS a
// LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d 
//     ON a.id = d.articlelist_id AND d.languageid = {$langid}
// WHERE a.comp = 32
// ORDER BY a.num DESC
// ";
// $feedback_home = $GLOBALS['sp']->getAll($sql);
// $smarty->assign("feedback_home", $feedback_home);

////
$sql = "
SELECT a.img_thumb_vn,
       d.name AS name_detail, d.unique_key, d.short
FROM {$GLOBALS['db_sp']}.articlelist AS a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d 
    ON a.id = d.articlelist_id AND d.languageid = {$langid}
WHERE a.active = 1 AND a.comp = 19
ORDER BY a.num ASC
";
$commit = $GLOBALS['sp']->getAll($sql);
$smarty->assign("commit", $commit);
///wishlist
$session_id = session_id();
$wishlist_ids = $GLOBALS['sp']->getCol("
    SELECT product_id
    FROM {$GLOBALS['db_sp']}.wishlist
    WHERE session_id = '{$session_id}'
");
$smarty->assign("wishlist_ids", $wishlist_ids);

$row = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.wishlist
    WHERE session_id = '{$session_id}'
");

$wishlist_total = (int)$row['total'];
$smarty->assign("wishlist_total", $wishlist_total);