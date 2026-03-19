<?php

$ids = isset($_POST['ids']) ? $_POST['ids'] : array();

if(!is_array($ids) || empty($ids)) {
    jsonResponse([
        "status" => false,
        "message" => "Không có dữ liệu"
    ]);
}

$comp_ids = [];

foreach($ids as $id) {

    $id = intval($id);
    if($id <= 0) {
        continue;
    }

    /* lấy thông tin item */
    $row = $GLOBALS['sp']->GetRow("
      SELECT comp,img_thumb_vn
      FROM {$GLOBALS['db_sp']}.articlelist
      WHERE id=?
  ", [$id]);

    if(!$row) {
        continue;
    }

    $comp_ids[$row['comp']] = $row['comp'];

    /* xoá file ảnh */
    if(!empty($row['img_thumb_vn'])) {

        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$row['img_thumb_vn'];

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
    $rows = $GLOBALS['sp']->GetAll("
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
    /* xoá detail   */
    $GLOBALS['sp']->Execute("
      DELETE FROM {$GLOBALS['db_sp']}.articlelist_detail
      WHERE articlelist_id=?
  ", [$id]);

    /* xoá item */
    $GLOBALS['sp']->Execute("
      DELETE FROM {$GLOBALS['db_sp']}.articlelist
      WHERE id=?
  ", [$id]);
}

/* rebuild num theo từng module */
foreach($comp_ids as $comp) {

    $GLOBALS['sp']->Execute("SET @num := 0");

    $GLOBALS['sp']->Execute("
      UPDATE {$GLOBALS['db_sp']}.articlelist
      SET num = (@num := @num + 1)
      WHERE comp=?
      ORDER BY num ASC
  ", [$comp]);

}

jsonResponse([
    "status" => true
]);
