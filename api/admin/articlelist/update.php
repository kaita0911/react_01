<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
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
$id = isset($_POST['id']) ? trim($_POST['id']) : '';
$category_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
$module = isset($_POST['module']) ? trim($_POST['module']) : '';
$active = isset($_POST['active']) ? intval($_POST['active']) : 1;
$new = isset($_POST['new']) ? intval($_POST['new']) : 1;
$hot = isset($_POST['hot']) ? intval($_POST['hot']) : 1;
$mostview = isset($_POST['mostview']) ? intval($_POST['mostview']) : 1;
$price = isset($_POST['price']) ? intval($_POST['price']) : 0;
$priceold = isset($_POST['priceold']) ? intval($_POST['priceold']) : 0;
$languages = isset($_POST['languages']) ? json_decode($_POST['languages'], true) : [];

if(!$id) {
    jsonResponse([
        "status" => false,
        "message" => "Thiếu id"
    ]);
}

/* lấy comp */
$comp_id = getCompId($module);

if(!$comp_id) {
    jsonResponse([
        "status" => false,
        "message" => "Module không tồn tại"
    ]);
}

$GLOBALS['sp']->Execute("
  DELETE FROM {$GLOBALS['db_sp']}.articlelist_categories
  WHERE articlelist_id=?
  ", [$id]);
if($category_id > 0) {

    $GLOBALS['sp']->Execute("
      INSERT INTO {$GLOBALS['db_sp']}.articlelist_categories
      (articlelist_id,categories_id)
      VALUES(?,?)
      ", [
        $id,
        $category_id
    ]);

}
/* lấy ảnh cũ */
$oldImage = $GLOBALS['sp']->getOne("
SELECT img_thumb_vn
FROM {$GLOBALS['db_sp']}.articlelist
WHERE id=?
", [$id]);
$hinhanh = $oldImage;


/* upload ảnh mới */
$module = preg_replace('/[^a-zA-Z0-9_-]/', '', $module);
$uploadFolder = 'hinh-anh/' . $module . '/';
$name = 'image';

if (!empty($languages)) {
    if (isset($languages[1]['name'])) {
        $name = $languages[1]['name'];
    } else {
        $firstLang = reset($languages);
        $name = isset($firstLang['name']) ? $firstLang['name'] : 'image';
    }
}
if(isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $allowed)) {
        jsonResponse([
            "status" => false,
            "message" => "File không hợp lệ"
        ]);
    }

    $slug = toSlug($name);
    $slug = substr($slug, 0, 100);

    if(empty($slug)) {
        $slug = 'image';
    }

    $filename = $slug . '-' . time() . '.' . $ext;

    /* folder theo comp */


    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir.$filename)) {
        jsonResponse([
            "status" => false,
            "message" => "Upload thất bại"
        ]);
    }

    $hinhanh = $uploadFolder.$filename;

    /* xoá ảnh cũ */
    if($oldImage) {

        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$oldImage;


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

}
///////
$delete_gallery = isset($_POST['delete_gallery'])
? json_decode($_POST['delete_gallery'], true)
: [];
if(!empty($delete_gallery)) {

    foreach($delete_gallery as $gid) {

        $row = $GLOBALS['sp']->GetRow("
      SELECT img_vn
      FROM gallery_sp
      WHERE id=?
      ", [$gid]);

        if($row) {

            $oldPath = $_SERVER['DOCUMENT_ROOT']."/".$row['img_vn'];

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
      WHERE id=?
      ", [$gid]);

    }

}

/* update vị trí gallery */
if(isset($_POST['gallery_update'])) {

    foreach($_POST['gallery_update'] as $g) {

        $g = json_decode($g, true);

        $gid = $g['id'];
        $num = $g['num'];

        $GLOBALS['sp']->Execute("
      UPDATE gallery_sp
      SET num=?
      WHERE id=?
      ", [
            $num,
            $gid
        ]);

    }

}
/* upload gallery */
$galleryNew = isset($_POST['gallery_new'])
    ? json_decode($_POST['gallery_new'], true)
    : [];

if (!empty($galleryNew)) {
    foreach ($galleryNew as $g) {

        $img = $g['path'];
        $num = intval($g['num']);

        $GLOBALS['sp']->Execute("
            INSERT INTO gallery_sp
            (articlelist_id,img_vn,num)
            VALUES(?,?,?)
        ", [
            $id,
            $img,
            $num
        ]);
    }
}

/* update price */
$exists = $GLOBALS['sp']->getOne("
SELECT COUNT(*)
FROM {$GLOBALS['db_sp']}.articlelist_price
WHERE articlelist_id=?
", [$id]);

if($exists) {

    $GLOBALS['sp']->Execute("
  UPDATE {$GLOBALS['db_sp']}.articlelist_price
  SET price=?, priceold=?
  WHERE articlelist_id=?
  ", [
        $price,
        $priceold,
        $id
    ]);

} else {

    $GLOBALS['sp']->Execute("
  INSERT INTO {$GLOBALS['db_sp']}.articlelist_price
  (articlelist_id,price,priceold)
  VALUES (?,?,?)
  ", [
        $id,
        $price,
        $priceold
    ]);

}
/* update articlelist */
$GLOBALS['sp']->Execute("
UPDATE {$GLOBALS['db_sp']}.articlelist
SET active=?, img_thumb_vn=?, new=?,hot=?,mostview=?
WHERE id=?
", [$active,$hinhanh,$new,$hot,$mostview,$id]);

/* update detail */
foreach($languages as $langid => $data) {

    $name  = isset($data['name']) ? trim($data['name']) : '';
    $slug  = isset($data['slug']) ? trim($data['slug']) : '';
    $short = isset($data['short']) ? $data['short'] : '';
    $content   = isset($data['content']) ? $data['content'] : '';
    $keyword   = isset($data['keyword']) ? $data['keyword'] : '';
    $des   = isset($data['des']) ? $data['des'] : '';

    $GLOBALS['sp']->Execute("
  UPDATE {$GLOBALS['db_sp']}.articlelist_detail
  SET name=?,slug=?,short=?,content=?, keyword = ?, des=?
  WHERE articlelist_id=? AND languageid=?
  ", [
        $name,
        $slug,
        $short,
        $content,
        $keyword,
        $des,
        $id,
        $langid
    ]);

}

jsonResponse([
"status" => true
]);
