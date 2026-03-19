<?php

function toSlug($str)
{
    $str = strtolower($str);

    $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
    $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
    $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
    $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
    $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
    $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
    $str = preg_replace('/[đ]/u', 'd', $str);

    $str = preg_replace('/[^a-z0-9-]+/', '-', $str);
    $str = trim($str, '-');

    return $str;
}
$id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
$comp   = isset($_POST['comp']) ? intval($_POST['comp']) : '';
$parent = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : '';
$active = isset($_POST['active']) ? intval($_POST['active']) : 1;
$home = isset($_POST['home']) ? intval($_POST['home']) : 1;

$languages = isset($_POST['languages'])
    ? json_decode($_POST['languages'], true)
    : array();


if($id == 0) {

    $max = $GLOBALS['sp']->getOne("
SELECT MAX(num)
FROM {$GLOBALS['db_sp']}.categories
WHERE comp=?
", array($comp));

    $num = $max ? $max + 1 : 1;

    $GLOBALS['sp']->Execute("
INSERT INTO {$GLOBALS['db_sp']}.categories
(comp,parent_id,num,active,home)
VALUES(?,?,?,?,?)
", array($comp,$parent,$num,$active,$home));

    $id = $GLOBALS['sp']->Insert_ID();

} else {

    $GLOBALS['sp']->Execute("
UPDATE {$GLOBALS['db_sp']}.categories
SET parent_id=?,active=? , home = ?
WHERE id=?
", array($parent,$active,$home,$id));

}
/* ===== UPLOAD IMAGE ===== */
$name = 'image';

if (!empty($languages)) {
    if (isset($languages[1]['name'])) {
        $name = $languages[1]['name'];
    } else {
        $firstLang = reset($languages);
        $name = isset($firstLang['name']) ? $firstLang['name'] : 'image';
    }
}
$uploadFolder = 'hinh-anh/cate/';
if(isset($_FILES['hinhdanhmuc']) && $_FILES['hinhdanhmuc']['name'] != '') {
    $ext = strtolower(pathinfo($_FILES['hinhdanhmuc']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    /* XÓA HÌNH CŨ */

    $old = $GLOBALS['sp']->getOne("
    SELECT img_vn
    FROM {$GLOBALS['db_sp']}.categories
    WHERE id=?
    ", array($id));
    /* XÓA HÌNH CŨ */
    $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;
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
    $slug = toSlug($name);
    $slug = substr($slug, 0, 100);

    if(empty($slug)) {
        $slug = 'image';
    }

    $filename = $slug . '-' . time() . '.' . $ext;

    if(move_uploaded_file($_FILES['hinhdanhmuc']['tmp_name'], $uploadDir.$filename)) {
        $image = $uploadFolder.$filename;

        $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.categories
        SET img_vn=?
        WHERE id=?
        ", array($image,$id));
    }

}


/* ===== SAVE LANGUAGE ===== */

foreach($languages as $langId => $data) {

    $name = isset($data['name']) ? trim($data['name']) : '';
    $slug = isset($data['slug']) ? trim($data['slug']) : '';
    $content = isset($data['content']) ? trim($data['content']) : '';
    $des = isset($data['des']) ? trim($data['des']) : '';
    $keyword = isset($data['keyword']) ? trim($data['keyword']) : '';

    if(!$name) {
        continue;
    }

    $exists = $GLOBALS['sp']->getOne("
SELECT id
FROM {$GLOBALS['db_sp']}.categories_detail
WHERE categories_id=? AND languageid=?
", array($id,$langId));

    if($exists) {

        $GLOBALS['sp']->Execute("
  UPDATE {$GLOBALS['db_sp']}.categories_detail
  SET name=?, slug=?, content = ?, des = ?, keyword = ?
  WHERE categories_id=? AND languageid=?
  ", array($name, $slug, $content ,$des,$keyword,$id, $langId));

    } else {

        $GLOBALS['sp']->Execute("
  INSERT INTO {$GLOBALS['db_sp']}.categories_detail
  (categories_id,languageid,name,slug,content,des,keyword)
  VALUES(?,?,?,?, ?,?,?)
  ", array($id,$langId,$name,$slug,$content,$des,$keyword));

    }

}


echo json_encode(array(
    "status" => true,
    "id" => $id
));
