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


// =================================================
// 📖 CHI TIẾT BÀI VIẾT
// =================================================
if ($act === "detail") {

    $sql = "
        SELECT a.*, d.*
        FROM {$GLOBALS['db_sp']}.articlelist AS a
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
            ON d.articlelist_id = a.id AND d.languageid = {$langid}
        WHERE d.unique_key = '{$slug}'
        LIMIT 1
    ";

    $rs = $GLOBALS["sp"]->getRow($sql);

    if (!$rs) {
        echo json_encode(["error" => "Not found"]);
        exit;
    }

    // ===== Mục lục =====
    list($newContent, $toc) = generate_toc($rs['content']);

    // ===== Tags =====
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

    // ===== Tin liên quan =====
    $sql_related = "
        SELECT a.id, a.unique_key, a.img_thumb_vn, a.dated,
               d.name AS title
        FROM {$GLOBALS['db_sp']}.articlelist AS a
        LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
            ON d.articlelist_id = a.id AND d.languageid = {$langid}
        WHERE a.id != {$rs['articlelist_id']}
          AND a.comp = {$rs['comp']}
          AND a.active = 1
        ORDER BY a.num DESC
        LIMIT 6
    ";

    $related = $GLOBALS["sp"]->getAll($sql_related);

    echo json_encode([
        "title"   => $rs['name'],
        "content" => $newContent,
        "thumb"   => $rs['img_thumb_vn'],
        "dated"   => $rs['dated'],
        "tags"    => $tags,
        "toc"     => $toc,
        "related" => $related
    ], JSON_UNESCAPED_UNICODE);

    exit;
}


// =================================================
// 📄 DANH SÁCH BÀI VIẾT
// =================================================

$page = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = 10;
$start = ($page - 1) * $perPage;

$sql = "
    SELECT a.id, a.img_thumb_vn, a.dated,
           d.name,
           d.unique_key,
           d.content, d.keyword, d.des
    FROM {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
        ON a.id = d.articlelist_id AND d.languageid = {$langid}
    WHERE a.active = 1 AND a.comp = 3
    ORDER BY a.num DESC
    LIMIT {$start}, {$perPage}
";

$articles = $GLOBALS['sp']->getAll($sql);

// ===== Tổng =====
$total = (int)$GLOBALS['sp']->getOne("
    SELECT COUNT(*)
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE active = 1 AND comp = 3
");

echo json_encode([
    "items" => $articles,
    "pagination" => [
        "page" => $page,
        "perPage" => $perPage,
        "total" => $total,
        "totalPages" => ceil($total / $perPage)
    ]
], JSON_UNESCAPED_UNICODE);