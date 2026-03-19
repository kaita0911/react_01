<?php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(!$id) {
    jsonResponse([
        "status" => false,
        "message" => "Thiếu id"
    ]);
}

/* lấy article */
$article = $GLOBALS['sp']->GetRow("
    SELECT *
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE id=?
    LIMIT 1
", [$id]);

if(!$article) {
    jsonResponse([
        "status" => false,
        "message" => "Không tìm thấy dữ liệu"
    ]);
}
/* lấy gallery */
$gallery = $GLOBALS['sp']->getAll("
SELECT id,img_vn,num
FROM gallery_sp
WHERE articlelist_id=?
ORDER BY num ASC
", [$id]);
/* lấy price */
$price = $GLOBALS['sp']->GetRow("
    SELECT price, priceold
    FROM {$GLOBALS['db_sp']}.articlelist_price
    WHERE articlelist_id=?
    LIMIT 1
", [$id]);
if(!$price) {
    $price = [
        "price" => 0,
        "priceold" => 0
    ];
}
/* lấy detail đa ngôn ngữ */
$rs = $GLOBALS['sp']->Execute("
    SELECT languageid,name,slug,short,content,keyword,des
    FROM {$GLOBALS['db_sp']}.articlelist_detail
    WHERE articlelist_id=?
", [$id]);

$languages = [];

while(!$rs->EOF) {

    $languages[$rs->fields['languageid']] = [
        "name" => $rs->fields['name'],
        "slug" => $rs->fields['slug'],
        "short" => $rs->fields['short'],
        "content" => $rs->fields['content'],
        "keyword" => $rs->fields['keyword'],
        "des" => $rs->fields['des']
    ];

    $rs->MoveNext();
}
$category = $GLOBALS['sp']->GetOne("
    SELECT categories_id
    FROM {$GLOBALS['db_sp']}.articlelist_categories
    WHERE articlelist_id=?
    LIMIT 1
", [$id]);
jsonResponse([
    "status" => true,
    "data" => [
        "parent_id" => $category,
        "active" => $article['active'],
        "hot" => $article['hot'],
        "new" => $article['new'],
        "mostview" => $article['mostview'],
        "img_thumb_vn" => $article['img_thumb_vn'],
        "languages" => $languages,
        "price" => $price['price'],
        "priceold" => $price['priceold'],
        "gallery" => $gallery
    ]
]);
