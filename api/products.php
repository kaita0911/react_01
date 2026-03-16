<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");
include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
include_once(__DIR__ . "/../functions/function-api.php");

$act     = isset($_GET['act']) ? $_GET['act'] : 'list';
$slug    = isset($_GET['slug']) ? $_GET['slug'] : '';
$cate_id = isset($_GET['cate_id']) ? (int)$_GET['cate_id'] : 0;
$langid     = 1; // tùy hệ thống bạn

if ($act === "detail") {

    $sql = "
        SELECT a.*, d.*, p.price, p.priceold, a.comp,
        c.id AS cate_id,
        cd.name AS category_name,
        cd.slug AS category_slug,
        d.keyword as tag
        FROM {$GLOBALS['db_sp']}.articlelist a
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
            ON d.articlelist_id = a.id AND d.languageid = {$langid}
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
            ON p.articlelist_id = a.id

        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_categories ac
            ON ac.articlelist_id = a.id

        LEFT JOIN {$GLOBALS['db_sp']}.categories c
            ON c.id = ac.categories_id

        LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
            ON cd.categories_id = c.id
       AND cd.languageid = {$langid}
        WHERE d.slug = '{$slug}'
        LIMIT 1
    ";

    $rs = $GLOBALS["sp"]->getRow($sql);

    if (!$rs) {
        echo json_encode(["error" => "Not found"]);
        exit;
    }

    $category_path = [];
    // 🔥 Lấy tất cả category của bài
    $allCate = $GLOBALS['sp']->getAll("
    SELECT categories_id
    FROM {$GLOBALS['db_sp']}.articlelist_categories
    WHERE articlelist_id = {$rs['articlelist_id']}
    ");
    $leafCate = 0;
    foreach ($allCate as $c) {

        $cid = (int)$c['categories_id'];

        // Nếu KHÔNG phải cha của ai → là leaf
        $isParent = (int)$GLOBALS['sp']->getOne("
            SELECT COUNT(*)
            FROM {$GLOBALS['db_sp']}.categories_related
            WHERE related_id = {$cid}
        ");

        if ($isParent == 0) {
            $leafCate = $cid;
            break;
        }
    }

    // Nếu không tìm được → fallback
    if ($leafCate == 0) {
        $leafCate = (int)$rs['cate_id'];
    }

    $currentId = $leafCate;
    while ($currentId > 0) {

        // Lấy thông tin category hiện tại
        $cat = $GLOBALS['sp']->getRow("
            SELECT c.id,
                   cd.name,
                   cd.slug
            FROM {$GLOBALS['db_sp']}.categories c
            LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
                ON cd.categories_id = c.id
               AND cd.languageid = {$langid}
            WHERE c.id = {$currentId}
            LIMIT 1
        ");

        if (!$cat) {
            break;
        }

        // ⭐ CHÈN VÀO ĐẦU → ROOT → LEAF
        array_unshift($category_path, [
            "id"   => $cat['id'],
            "name" => $cat['name'],
            "slug" => $cat['slug']
        ]);

        // Tìm category CHA trong bảng categories_related
        $parent = $GLOBALS['sp']->getRow("
            SELECT related_id
            FROM {$GLOBALS['db_sp']}.categories_related
            WHERE category_id = {$currentId}
            LIMIT 1
        ");
        if (!$parent) {
            break;
        }

        $currentId = (int)$parent['related_id'];

    }


    //////////////////////////////////////////////////
    // TAGS
    //////////////////////////////////////////////////

    $tags = [];

    if (!empty($rs['keyword'])) {
        foreach (explode(',', $rs['keyword']) as $t) {
            $t = trim($t);
            if ($t !== '') {
                $tags[] = [
                    "name" => $t,
                    "slug" => removeVietnameseTones($t)
                ];
            }
        }
    }

    //////////////////////////////////////////////////
    // IMAGES
    //////////////////////////////////////////////////

    $article_id = (int)$rs['articlelist_id'];

    $images = $GLOBALS['sp']->getAll("
        SELECT *
        FROM {$GLOBALS['db_sp']}.gallery_sp
        WHERE articlelist_id = {$article_id}
        ORDER BY num ASC
    ");

    //////////////////////////////////////////////////
    // RELATED PRODUCTS
    //////////////////////////////////////////////////

    $related = $GLOBALS['sp']->getAll("
        SELECT a.id, a.img_thumb_vn,
               d.name ,
               d.slug,d.keyword,d.des,
               p.price
        FROM {$GLOBALS['db_sp']}.articlelist a
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
            ON d.articlelist_id = a.id AND d.languageid = {$langid}
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
            ON p.articlelist_id = a.id
        WHERE a.comp = 2
          AND a.active = 1
          AND a.id != {$article_id}
        ORDER BY a.num DESC
        LIMIT 8
    ");

    echo json_encode([
        "id"        => $rs['articlelist_id'],
        "des" => $rs['des'],
        "keyword" => $rs['keyword'],
        "title"     => $rs['name'],
        "slug" => $rs['slug'],
        "short"   => $rs['short'],
        "content"   => $rs['content'],
        "thumb"     => $rs['img_thumb_vn'],
        "price"     => $rs['price'],
        "priceold"  => $rs['priceold'],
        "comp"      => $rs['comp'],          // ⭐ cực quan trọng
        "category_name" => $rs['category_name'],
        "category_slug" => $rs['category_slug'],
        "category_path" => $category_path,
        "tags"      => $tags,
        "images"    => $images,
        "related"   => $related
    ], JSON_UNESCAPED_UNICODE);

    exit;
}

//////////////////////////////////////////////////
// 🟢 LIST + SUB
//////////////////////////////////////////////////

$page = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage  = 20;
$start    = ($page - 1) * $perPage;

$joinCate  = "";
$whereCate = "";
$cat = "";
$category_path = [];
if ($act === "sub" && $cate_id > 0) {
    $joinCate  = "INNER JOIN {$GLOBALS['db_sp']}.articlelist_categories ac ON ac.articlelist_id = a.id";
    $whereCate = "AND ac.categories_id = {$cate_id}";

    $currentId = (int)$cate_id;

    while ($currentId > 0) {

        // Lấy info category hiện tại
        $cat = $GLOBALS['sp']->getRow("
            SELECT c.id,c.img_vn,
                   cd.name,
                   cd.slug,cd.keyword, cd.des
            FROM {$GLOBALS['db_sp']}.categories c
            LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
                ON cd.categories_id = c.id
               AND cd.languageid = {$langid}
            WHERE c.id = {$currentId}
            LIMIT 1
        ");

        if (!$cat) {
            break;
        }

        // ⭐ CHÈN VÀO ĐẦU → ROOT → LEAF
        array_unshift($category_path, [
            "id"   => $cat['id'],
            "name" => $cat['name'],
            "slug" => $cat['slug'],
            "img_vn"   => $cat['img_vn'],
            "des" => $cat['des'],
            "keyword" => $cat['keyword']
        ]);

        //////////////////////////////////////////////////
        // 🔥 TÌM CHA
        //////////////////////////////////////////////////

        $parentId = (int)$GLOBALS['sp']->getOne("
            SELECT related_id
            FROM {$GLOBALS['db_sp']}.categories_related
            WHERE category_id = {$currentId}
            LIMIT 1
        ");

        if ($parentId <= 0) {
            break;
        }

        $currentId = $parentId;
    }
}

$sql = "
    SELECT a.id, a.img_thumb_vn,
           d.name ,
           d.slug as slug,
           p.price,p.priceold
    FROM {$GLOBALS['db_sp']}.articlelist a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d
        ON d.articlelist_id = a.id AND d.languageid = {$langid}
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
        ON p.articlelist_id = a.id
    {$joinCate}
    WHERE a.active = 1
      AND a.comp = 2
      {$whereCate}
    ORDER BY a.num DESC
    LIMIT {$start}, {$perPage}
";

$items = $GLOBALS['sp']->getAll($sql);

$total = (int)$GLOBALS['sp']->getOne("
    SELECT COUNT(*)
    FROM {$GLOBALS['db_sp']}.articlelist a
    {$joinCate}
    WHERE a.active = 1
      AND a.comp = 2
      {$whereCate}
");

echo json_encode([
    "items" => $items,
    "category" => $cat,
    "category_path" => $category_path,   // ⭐ THÊM DÒNG NÀY
    "pagination" => [
        "page" => $page,
        "perPage" => $perPage,
        "total" => $total,
        "totalPages" => ceil($total / $perPage)
    ]
], JSON_UNESCAPED_UNICODE);
