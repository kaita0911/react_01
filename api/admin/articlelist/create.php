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
$module = isset($_POST['module']) ? trim($_POST['module']) : '';
$active = isset($_POST['active']) ? intval($_POST['active']) : 1;
$new = isset($_POST['new']) ? intval($_POST['new']) : 1;
$hot = isset($_POST['hot']) ? intval($_POST['hot']) : 1;
$mostview = isset($_POST['mostview']) ? intval($_POST['mostview']) : 1;
$price = isset($_POST['price']) ? intval($_POST['price']) : '';
$priceold = isset($_POST['priceold']) ? intval($_POST['priceold']) : '';
$languages = isset($_POST['languages']) ? json_decode($_POST['languages'], true) : [];
$category_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
if($module == '' || empty($languages)) {
    jsonResponse([
        "status" => false,
        "message" => "Thiếu dữ liệu"
    ]);
}

$comp_id = getCompId($module);

if(!$comp_id) {
    jsonResponse([
        "status" => false,
        "message" => "Module không tồn tại"
    ]);
}
$module = preg_replace('/[^a-zA-Z0-9_-]/', '', $module);
$uploadFolder = 'hinh-anh/' . $module . '/';
$hinhanh = '';
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

    if (!in_array(strtolower($ext), $allowed)) {
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

    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

    // ✅ tạo folder trước
    if(!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // ✅ rồi mới check quyền
    if (!is_writable($uploadDir)) {
        jsonResponse([
            "status" => false,
            "message" => "Folder không có quyền ghi: " . $uploadDir
        ]);
    }

    // ✅ upload
    if (!move_uploaded_file($_FILES['hinhanh']['tmp_name'], $uploadDir.$filename)) {
        jsonResponse([
            "status" => false,
            "message" => "Upload thất bại"
        ]);
    }

    $hinhanh = $uploadFolder.$filename;
}
$maxNum = $GLOBALS['sp']->getOne("
 SELECT MAX(num)
 FROM {$GLOBALS['db_sp']}.articlelist
 WHERE comp=?
 ", array($comp_id));

$num = intval($maxNum) + 1;


$GLOBALS['sp']->Execute("
     INSERT INTO {$GLOBALS['db_sp']}.articlelist
     (comp,num,active,hot,new,mostview,img_thumb_vn)
     VALUES(?,?,?,?,?,?,?)
 ", array($comp_id,$num,$active,$hot,$new,$mostview,$hinhanh));

$id = $GLOBALS['sp']->Insert_ID();

if($category_id > 0) {

    $GLOBALS['sp']->Execute("
     INSERT INTO {$GLOBALS['db_sp']}.articlelist_categories
     (articlelist_id, categories_id)
     VALUES (?,?)
 ", [
        $id,
        $category_id
    ]);

}


if($price !== '' || $priceold !== '') {

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

$gallery = isset($_POST['gallery_paths'])
    ? json_decode($_POST['gallery_paths'], true)
    : [];

if(!empty($gallery)) {
    foreach($gallery as $k => $img) {

        $num = $k + 1;

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

foreach($languages as $langid => $data) {

    $name  = isset($data['name']) ? trim($data['name']) : '';
    $slug  = isset($data['slug']) ? trim($data['slug']) : '';
    $short = isset($data['short']) ? $data['short'] : '';
    $content   = isset($data['content']) ? $data['content'] : '';
    $keyword   = isset($data['keyword']) ? $data['keyword'] : '';
    $des   = isset($data['des']) ? $data['des'] : '';

    $GLOBALS['sp']->Execute("
     INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail
     (articlelist_id,languageid,name,slug,short,content,keyword,des)
     VALUES(?,?,?,?,?,?,?,?)
 ", array(
        $id,
        $langid,
        $name,
        $slug,
        $short,
        $content,
        $keyword,
        $des
    ));
}

jsonResponse([
    "status" => true,
    "id" => $id
]);
