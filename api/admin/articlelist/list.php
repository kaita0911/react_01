<?php

// Lấy thông số
$module = isset($_GET['module']) ? trim($_GET['module']) : '';
$page   = isset($_GET['page']) ? intval($_GET['page']) : 1;
if($page < 1) {
    $page = 1;
}

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 30;
if($limit <= 0) {
    $limit = 30;
}
if($limit > 200) {
    $limit = 200;
}

$start = ($page - 1) * $limit;

if($module == '') {
    jsonResponse([
        "status" => false,
        "message" => "Thiếu module"
    ]);
}

$comp_id = getCompId($module);

if(!$comp_id) {
    jsonResponse([
        "status" => false,
        "message" => "Module không tồn tại"
    ]);
}

/* ================= TOTAL ================= */
$total = $GLOBALS['sp']->getOne("
    SELECT COUNT(id)
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE comp=?
", [$comp_id]);

/* ================= ARTICLE LIST ================= */
$rows = $GLOBALS['sp']->getAll("
    SELECT id,num,img_thumb_vn,active,hot,mostview,new
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE comp=?
    ORDER BY num DESC
    LIMIT $start,$limit
", [$comp_id]);

$data = [];

foreach($rows as $row) {
    // Lấy name đa ngôn ngữ
    $rs = $GLOBALS['sp']->Execute("
        SELECT languageid,name,slug
        FROM {$GLOBALS['db_sp']}.articlelist_detail
        WHERE articlelist_id=?
    ", [$row['id']]);

    $names = [];
    $slugs = [];

    while(!$rs->EOF) {
        $langId = $rs->fields['languageid'];

        $names[$langId] = $rs->fields['name'];
        $slugs[$langId] = $rs->fields['slug']; // 👈 thêm dòng này

        $rs->MoveNext();
    }

    $row['names'] = $names;
    $row['slug'] = $slugs; // 👈 thêm dòng này
    $data[] = $row;
}

// Trả kết quả JSON
jsonResponse([
    "status" => true,
    "data" => $data,
    "total" => intval($total),
    "limit" => $limit,
    "page" => $page,
    "totalPage" => ceil($total / $limit)
]);
