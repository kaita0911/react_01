<?php
include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
$lang_prefix  = isset($_GET['lang']) ? $_GET['lang'] : '';
if ($lang_prefix == 'en/') { // lam tuong tu cho cac ngon ngu khac
    $langid = 2;
} else
    $langid =  1;
$comp_id  = isset($_GET['comp']) ? $_GET['comp'] : '';
$module   = isset($_GET['module']) ? $_GET['module'] : '';
$sub      = isset($_GET['sub']) ? $_GET['sub'] : '';
$cate_id  = isset($_GET['cate_id']) ? $_GET['cate_id'] : '';
$page     = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$sort     = isset($_GET['sort']) ? $_GET['sort'] : '';
$per_Page = 12;

$whereDetail = '';
$joinSql     = '';
$wherecomp   = "AND a.comp = {$comp_id}";

// === Nếu có danh mục hoặc thương hiệu ===
if (!empty($cate_id)) {
    $wherecomp = '';
    if ($comp_id == 76) {
        $joinSql     .= " INNER JOIN {$GLOBALS['db_sp']}.articlelist_brands AS ac ON ac.articlelist_id = a.id";
        $whereDetail = " AND ac.brands_id = {$cate_id}";
    } else {
        $joinSql     .= " INNER JOIN {$GLOBALS['db_sp']}.articlelist_categories AS ac ON ac.articlelist_id = a.id";
        $whereDetail = " AND ac.categories_id = {$cate_id}";
    }
}



// === Template mapping theo comp ===
$templateMap = [
    '31' => 'video/list.tpl',
    '3' => 'about/list.tpl',
    '2'  => 'products/list.tpl',
    '27' => 'service/list.tpl',
    '1'  => 'articles/list.tpl',
    '10' => 'project/list.tpl',
    '25' => 'recruiment/list.tpl',
];
$template = isset($templateMap[$comp_id]) ? $templateMap[$comp_id] : null;

if (!$template) {
    echo json_encode(["success" => false, "message" => "comp không tồn tại"]);
    exit;
}

// === Cấu hình riêng cho từng nhóm comp ===
// --- per page logic: set BEFORE computing $start ---
$isProduct = in_array((int)$comp_id, [2, 76], true);

$sql = "SELECT phantrang
FROM {$GLOBALS['db_sp']}.component
WHERE id = $comp_id";
$rs_page = $GLOBALS["sp"]->getRow($sql);
$per_Page = !empty($rs_page['phantrang']) ? (int)$rs_page['phantrang'] : 20;
$start     = ($page - 1) * $per_Page;

// === ORDER BY cho sản phẩm ===
if ($isProduct) {
    $map = [
        'price_asc'  => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'name_asc'   => 'd.name ASC',
        'name_desc'  => 'd.name DESC'
    ];
    $orderBy = isset($map[$sort]) ? $map[$sort] : 'a.num DESC';
} else {
    $orderBy = 'a.num DESC';
}

// === SELECT ===
// --- SELECT & JOIN (dùng $joins cho cả query và count) ---
$select = "
    a.id, a.comp, a.num, a.img_thumb_vn, a.dated,
    d.name AS name_detail, d.unique_key AS unique_key, d.short AS short_detail, d.content AS content_detail
";
$select .= $isProduct ? ", p.price, p.priceold" : "";
// === JOIN ===

$joins = "
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
      ON a.id = d.articlelist_id AND d.languageid = {$langid}
    {$joinSql}
";
if ($isProduct) {
    $joins .= " LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p ON p.articlelist_id = a.id ";
}

// === MAIN QUERY ===
$sql = "
    SELECT {$select}
    FROM {$GLOBALS['db_sp']}.articlelist AS a
    {$joins}
    WHERE a.active = 1 {$wherecomp} {$whereDetail}
    ORDER BY {$orderBy}
    LIMIT {$start}, {$per_Page}
";
$articles = $GLOBALS['sp']->getAll($sql);
if (!empty($articles)) {
    foreach ($articles as &$item) {
        // Giá sản phẩm
        if ($isProduct) {
            $item['price_formatted']    = isset($item['price']) ? number_format($item['price'], 0, ',', '.') : '';
            $item['priceold_formatted'] = isset($item['priceold']) ? number_format($item['priceold'], 0, ',', '.') : '';
        }
    }
}
$smarty->assign("view", $articles);


// === Đếm tổng bài ===
$countSql = "
    SELECT COUNT(DISTINCT a.id) AS cnt
    FROM {$GLOBALS['db_sp']}.articlelist AS a
    {$joins}
    WHERE a.active = 1 {$wherecomp} {$whereDetail}
";
$total = (int)$GLOBALS['sp']->getOne($countSql);

// --- PAGINATION ---
$totalPages = $total > 0 ? ceil($total / $per_Page) : 1;
$smarty->assign([
    "lang_prefix"   => $lang_prefix,
    "totalPages"   => $totalPages,
    "Checkpg"      => $totalPages > 1 ? 1 : 0,
    "currentPage"  => $page,
    "module"       => $module,
    "comp"         => $comp_id,
    "sort"         => $sort
]);

// === Render HTML & Pagination ===
$html        = $smarty->fetch($template);
$pagination  = $smarty->fetch("pagination.tpl");

// === Trả về JSON ===
echo json_encode([
    "success"     => true,
    "html"        => $html,
    "pagination"  => $pagination
]);
exit;
