<?php

$id = intval($_POST['id']);

if($id <= 0) {
    jsonResponse([
        "status" => false,
        "message" => "ID không hợp lệ"
    ]);
}

/* lấy comp + ảnh */
$row = $GLOBALS['sp']->GetRow("
 SELECT comp,img_thumb_vn
 FROM {$GLOBALS['db_sp']}.articlelist
 WHERE id=?
 ", [$id]);

if(!$row) {
    jsonResponse([
        "status" => false,
        "message" => "Item không tồn tại"
    ]);
}

$comp_id = $row['comp'];
$image = $row['img_thumb_vn'];

/* xoá file ảnh */
if($image) {

    $oldPath = $_SERVER['DOCUMENT_ROOT']."/".$image;


    $realPath = realpath($oldPath);
    $baseDir  = realpath($_SERVER['DOCUMENT_ROOT'].'/hinh-anh/');

    if (
        $realPath &&
        $baseDir &&
        strpos($realPath, $baseDir) === 0 &&
        is_file($realPath)
    ) {
        unlink($realPath);
    }

}
/* xoá gallery */
$rows = $GLOBALS['sp']->getAll("
SELECT img_vn
FROM gallery_sp
WHERE articlelist_id=?
", [$id]);

foreach($rows as $r) {

    $oldPath = $_SERVER['DOCUMENT_ROOT']."/".$r['img_vn'];


    $realPath = realpath($oldPath);
    $baseDir  = realpath($_SERVER['DOCUMENT_ROOT'].'/hinh-anh/');

    if (
        $realPath &&
        $baseDir &&
        strpos($realPath, $baseDir) === 0 &&
        is_file($realPath)
    ) {
        unlink($realPath);
    }
}

$GLOBALS['sp']->Execute("
DELETE FROM gallery_sp
WHERE articlelist_id=?
", [$id]);
/* xoá price */
$GLOBALS['sp']->Execute("
DELETE FROM {$GLOBALS['db_sp']}.articlelist_price
WHERE articlelist_id=?
", [$id]);
/* xoá detail */
$GLOBALS['sp']->Execute("
DELETE FROM {$GLOBALS['db_sp']}.articlelist_detail
WHERE articlelist_id=?
", array($id));

/* xoá item */
$GLOBALS['sp']->Execute("
 DELETE FROM {$GLOBALS['db_sp']}.articlelist
 WHERE id=?
", array($id));

/* rebuild num chỉ trong module đó */
$GLOBALS['sp']->Execute("SET @num := 0");

$GLOBALS['sp']->Execute("
 UPDATE {$GLOBALS['db_sp']}.articlelist
 SET num = (@num := @num + 1)
 WHERE comp = ?
 ORDER BY num ASC
", array($comp_id));

jsonResponse([
    "status" => true
]);
