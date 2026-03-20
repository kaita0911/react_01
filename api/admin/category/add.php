<?php


$id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
$comp   = isset($_POST['comp']) ? intval($_POST['comp']) : 0;
$parent = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
$active = isset($_POST['active']) ? intval($_POST['active']) : 1;
$home   = isset($_POST['home']) ? intval($_POST['home']) : 1;

$languages = isset($_POST['languages']) ? json_decode($_POST['languages'], true) : [];

// ===== CREATE OR UPDATE CATEGORY =====
if($id == 0) {
    $max = $GLOBALS['sp']->getOne("
        SELECT MAX(num) FROM {$GLOBALS['db_sp']}.categories WHERE comp=?
    ", [$comp]);
    $num = $max ? $max + 1 : 1;

    $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.categories
        (comp,parent_id,num,active,home)
        VALUES(?,?,?,?,?)
    ", [$comp,$parent,$num,$active,$home]);

    $id = $GLOBALS['sp']->Insert_ID();
} else {
    $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.categories
        SET parent_id=?, active=?, home=?
        WHERE id=?
    ", [$parent,$active,$home,$id]);
}

// ===== UPLOAD IMAGE =====
$uploadFolder = 'hinh-anh/cate/';
if(isset($_FILES['hinhdanhmuc']) && $_FILES['hinhdanhmuc']['name'] != '') {

    $firstLang = reset($languages);
    $name = isset($firstLang['name']) ? $firstLang['name'] : 'image';

    $ext = strtolower(pathinfo($_FILES['hinhdanhmuc']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if(!in_array($ext, $allowed)) {
        echo json_encode(["status" => false,"message" => "File không hợp lệ"]);
        exit;
    }

    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // XÓA HÌNH CŨ
    $old = $GLOBALS['sp']->getOne("SELECT img_vn FROM {$GLOBALS['db_sp']}.categories WHERE id=?", [$id]);
    if($old) {
        $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;
        $realPath = realpath($oldPath);
        $baseDir  = realpath($_SERVER['DOCUMENT_ROOT'].'/hinh-anh/');
        if($realPath && $baseDir && strpos($realPath, $baseDir) === 0 && is_file($realPath)) {
            unlink($realPath);
        }
    }

    $slug = slugify($name);
    $slug = $slug ?: 'image';
    $filename = $slug.'-'.time().'.'.$ext;

    if(move_uploaded_file($_FILES['hinhdanhmuc']['tmp_name'], $uploadDir.$filename)) {
        $image = $uploadFolder.$filename;
        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.categories
            SET img_vn=?
            WHERE id=?
        ", [$image,$id]);
    }
}

// ===== SAVE LANGUAGES =====
foreach($languages as $langId => $data) {

    $langId = intval($langId);
    $name = isset($data['name']) ? trim($data['name']) : '';
    if(!$name) {
        continue;
    }

    $slug = !empty($data['slug']) ? trim($data['slug']) : toSlug($name);
    $content = isset($data['content']) ? trim($data['content']) : '';
    $des = isset($data['des']) ? trim($data['des']) : '';
    $keyword = isset($data['keyword']) ? trim($data['keyword']) : '';

    $exists = $GLOBALS['sp']->getOne("
        SELECT id FROM {$GLOBALS['db_sp']}.categories_detail
        WHERE categories_id=? AND languageid=?
    ", [$id,$langId]);

    if($exists) {
        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.categories_detail
            SET name=?, slug=?, content=?, des=?, keyword=?
            WHERE categories_id=? AND languageid=?
        ", [$name,$slug,$content,$des,$keyword,$id,$langId]);
    } else {
        $GLOBALS['sp']->Execute("
            INSERT INTO {$GLOBALS['db_sp']}.categories_detail
            (categories_id,languageid,name,slug,content,des,keyword)
            VALUES(?,?,?,?,?,?,?)
        ", [$id,$langId,$name,$slug,$content,$des,$keyword]);
    }
}

echo json_encode([
    "status" => true,
    "id" => $id
]);
