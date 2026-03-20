<?php

$id   = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
$lang = isset($_POST["languageid"]) ? intval($_POST["languageid"]) : 0;
$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";

if (!$id || !$lang) {
    echo json_encode([
        "status" => false,
        "message" => "Thiếu id hoặc language"
    ]);
    exit;
}

$slug = slugify($name);

// ⭐ CHECK tồn tại
$exists = $GLOBALS['sp']->getOne("
    SELECT COUNT(*)
    FROM {$GLOBALS['db_sp']}.articlelist_detail
    WHERE articlelist_id = ? AND languageid = ?
", [$id, $lang]);

if ($exists) {

    // 👉 UPDATE
    $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.articlelist_detail
        SET name = ?, slug = ?
        WHERE articlelist_id = ? AND languageid = ?
    ", [
        $name,
        $slug,
        $id,
        $lang
    ]);

} else {

    // 👉 INSERT (cái bạn đang thiếu)
    $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail
        (articlelist_id, languageid, name, slug)
        VALUES (?,?,?,?)
    ", [
        $id,
        $lang,
        $name,
        $slug
    ]);
}

echo json_encode([
    "status" => true
]);
