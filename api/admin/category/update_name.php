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

// ⭐ Check tồn tại
$exists = $GLOBALS['sp']->getOne("
    SELECT COUNT(*)
    FROM {$GLOBALS['db_sp']}.categories_detail
    WHERE categories_id = ? AND languageid = ?
", [$id, $lang]);

if ($exists) {
    // 👉 UPDATE
    $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.categories_detail
        SET name = ?, slug = ?
        WHERE categories_id = ? AND languageid = ?
    ", [
        $name,
        $slug,
        $id,
        $lang
    ]);
} else {
    // 👉 INSERT nếu chưa có
    $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.categories_detail
        (categories_id, languageid, name, slug)
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
