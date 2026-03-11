<?php

// ================= CORS =================
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once("../../includes/config.php");

$act = isset($_GET['act']) ? $_GET['act'] : 'list';

/* ================= HELPER ================= */

function jsonResponse($data){
    echo json_encode($data);
    exit;
}

function getCompId($module){

    $comp = $GLOBALS['sp']->getRow("
        SELECT id
        FROM {$GLOBALS['db_sp']}.component
        WHERE `do`=?
        LIMIT 1
    ", array($module));

    if(!$comp){
        return 0;
    }

    return intval($comp['id']);
}


/* ================= SWITCH ================= */

switch($act){

/* ==========================================
   LIST
========================================== */

case "list":

    $module = isset($_GET['module']) ? trim($_GET['module']) : '';

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if($page < 1) $page = 1;

    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 30;
    if($limit <= 0) $limit = 30;
    if($limit > 200) $limit = 200;

    $start = ($page-1) * $limit;

    if($module==''){
        jsonResponse(array(
            "status"=>false,
            "message"=>"Thiếu module"
        ));
    }

    $comp_id = getCompId($module);

    if(!$comp_id){
        jsonResponse(array(
            "status"=>false,
            "message"=>"Module không tồn tại"
        ));
    }

    $total = $GLOBALS['sp']->getOne("
        SELECT COUNT(id)
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE comp=?
    ", array($comp_id));

    $items = $GLOBALS['sp']->getAll("
        SELECT
            a.id,
            a.num,a.img_thumb_vn,
            a.active,
            (
                SELECT name
                FROM {$GLOBALS['db_sp']}.articlelist_detail
                WHERE articlelist_id=a.id
                AND languageid=1
                LIMIT 1
            ) AS name
        FROM {$GLOBALS['db_sp']}.articlelist a
        WHERE a.comp=?
        ORDER BY a.num ASC
        LIMIT $start,$limit
    ", array($comp_id));

    jsonResponse(array(
        "status"=>true,
        "data"=>$items,
        "total"=>intval($total),
        "limit"=>$limit,
        "page"=>$page,
        "totalPage"=>ceil($total/$limit)
    ));

break;


/* ==========================================
   ADD
========================================== */

case "add":

    $module = isset($_POST['module']) ? trim($_POST['module']) : '';
    $active = isset($_POST['active']) ? intval($_POST['active']) : 1;
    
    $languages = isset($_POST['languages']) ? json_decode($_POST['languages'], true) : [];
  
    if($module=='' || empty($languages)){
        jsonResponse([
            "status"=>false,
            "message"=>"Thiếu dữ liệu"
        ]);
    }
    
    $comp_id = getCompId($module);
    
    if(!$comp_id){
        jsonResponse([
            "status"=>false,
            "message"=>"Module không tồn tại"
        ]);
    }
    ///hình ảnh
    $uploadFolder = 'hinh-anh/';

    switch($comp_id){
        case 1:
            $uploadFolder .= 'tin-tuc/';
            break;

        case 2:
            $uploadFolder .= 'thumbs/';
            break;

        default:
            $uploadFolder .= 'khac/';
    }
    $hinhanh = '';

    if(isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error']==0){

        $ext = pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION);
        $filename = time().'_'.rand(1000,9999).'.'.$ext;
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;
    
        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777,true);
        }
    
        move_uploaded_file($_FILES['hinhanh']['tmp_name'],$uploadDir.$filename);
    
        $hinhanh = $uploadFolder.$filename;
    }
    /* lấy num lớn nhất */
    $maxNum = $GLOBALS['sp']->getOne("
        SELECT MAX(num)
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE comp=?
    ", array($comp_id));
    
    $num = intval($maxNum) + 1;
    
    /* insert articlelist */
    $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist
        (comp,num,active,img_thumb_vn)
        VALUES(?,?,?,?)
    ", array($comp_id,$num,$active,$hinhanh));
    
    $id = $GLOBALS['sp']->Insert_ID();
    
    /* insert detail theo language */
    foreach($languages as $langid => $data){
    
        $name  = isset($data['name']) ? trim($data['name']) : '';
        $slug  = isset($data['slug']) ? trim($data['slug']) : '';
        $short = isset($data['short']) ? $data['short'] : '';
        $des   = isset($data['des']) ? $data['des'] : '';
    
        $GLOBALS['sp']->Execute("
            INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail
            (articlelist_id,languageid,name,unique_key,short,content)
            VALUES(?,?,?,?,?,?)
        ", array(
            $id,
            $langid,
            $name,
            $slug,
            $short,
            $des
        ));
    }
    
    jsonResponse([
        "status"=>true,
        "id"=>$id
    ]);
    
    break;
    /* ==========================================
   Detail
    ========================================== */
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
        if(!$id){
            jsonResponse([
                "status"=>false,
                "message"=>"Thiếu id"
            ]);
        }
    
        /* lấy article */
        $article = $GLOBALS['sp']->GetRow("
            SELECT *
            FROM {$GLOBALS['db_sp']}.articlelist
            WHERE id=?
            LIMIT 1
        ",[$id]);
    
        if(!$article){
            jsonResponse([
                "status"=>false,
                "message"=>"Không tìm thấy dữ liệu"
            ]);
        }
      /* lấy price */
        $price = $GLOBALS['sp']->GetRow("
            SELECT price, priceold
            FROM {$GLOBALS['db_sp']}.articlelist_price
            WHERE articlelist_id=?
            LIMIT 1
        ", [$id]);
        /* lấy detail đa ngôn ngữ */
        $rs = $GLOBALS['sp']->Execute("
            SELECT languageid,name,unique_key,short,content
            FROM {$GLOBALS['db_sp']}.articlelist_detail
            WHERE articlelist_id=?
        ",[$id]);
    
        $languages = [];
    
        while(!$rs->EOF){
    
            $languages[$rs->fields['languageid']] = [
                "name"=>$rs->fields['name'],
                "slug"=>$rs->fields['unique_key'],
                "short"=>$rs->fields['short'],
                "des"=>$rs->fields['content']
            ];
    
            $rs->MoveNext();
        }
    
        jsonResponse([
            "status"=>true,
            "data"=>[
                "active"=>$article['active'],
                "img_thumb_vn"=>$article['img_thumb_vn'],
                "languages"=>$languages,
                "price"=>$price['price'],
                "priceold"=>$price['priceold'],
            ]
        ]);
    
    break;
/* ==========================================
   UPDATE
========================================== */

case "update":

    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    $module = isset($_POST['module']) ? trim($_POST['module']) : '';
    $active = isset($_POST['active']) ? intval($_POST['active']) : 1;
    $price = isset($_POST['price']) ? intval($_POST['price']) : 0;
    $priceold = isset($_POST['priceold']) ? intval($_POST['priceold']) : 0;
    $languages = isset($_POST['languages']) ? json_decode($_POST['languages'], true) : [];
    
    if(!$id){
        jsonResponse([
            "status"=>false,
            "message"=>"Thiếu id"
        ]);
    }
    
    /* lấy comp */
    $comp_id = getCompId($module);
    
    if(!$comp_id){
        jsonResponse([
            "status"=>false,
            "message"=>"Module không tồn tại"
        ]);
    }
    
    /* lấy ảnh cũ */
    $oldImage = $GLOBALS['sp']->getOne("
    SELECT img_thumb_vn
    FROM {$GLOBALS['db_sp']}.articlelist
    WHERE id=?
    ",[$id]);
    
    $hinhanh = $oldImage;
    
    /* upload ảnh mới */
    if(isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error']==0){
    
        $ext = pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION);
        $filename = time().'_'.rand(1000,9999).'.'.$ext;
    
        /* folder theo comp */
        if($comp_id == 1){
            $uploadFolder = "hinh-anh/tin-tuc/";
        }else{
            $uploadFolder = "hinh-anh/thumbs/";
        }
    
        $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;
    
        if(!is_dir($uploadDir)){
            mkdir($uploadDir,0777,true);
        }
    
        move_uploaded_file($_FILES['hinhanh']['tmp_name'],$uploadDir.$filename);
    
        $hinhanh = $uploadFolder.$filename;
    
        /* xoá ảnh cũ */
        if($oldImage){
    
            $oldFile = $_SERVER['DOCUMENT_ROOT'].'/'.$oldImage;
    
            if(file_exists($oldFile)){
                unlink($oldFile);
            }
    
        }
    
    }
    /* update price */
    $exists = $GLOBALS['sp']->getOne("
    SELECT COUNT(*)
    FROM {$GLOBALS['db_sp']}.articlelist_price
    WHERE articlelist_id=?
    ", [$id]);

    if($exists){

        $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.articlelist_price
        SET price=?, priceold=?
        WHERE articlelist_id=?
        ",[
            $price,
            $priceold,
            $id
        ]);

    }else{

        $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_price
        (articlelist_id,price,priceold)
        VALUES (?,?,?)
        ",[
            $id,
            $price,
            $priceold
        ]);

    }
    /* update article */
    $GLOBALS['sp']->Execute("
    UPDATE {$GLOBALS['db_sp']}.articlelist
    SET active=?, img_thumb_vn=?
    WHERE id=?
    ",[$active,$hinhanh,$id]);
    
    /* update detail */
    foreach($languages as $langid => $data){
    
        $name  = isset($data['name']) ? trim($data['name']) : '';
        $slug  = isset($data['slug']) ? trim($data['slug']) : '';
        $short = isset($data['short']) ? $data['short'] : '';
        $des   = isset($data['des']) ? $data['des'] : '';
    
        $GLOBALS['sp']->Execute("
        UPDATE {$GLOBALS['db_sp']}.articlelist_detail
        SET name=?,unique_key=?,short=?,content=?
        WHERE articlelist_id=? AND languageid=?
        ",[
            $name,
            $slug,
            $short,
            $des,
            $id,
            $langid
        ]);
    
    }
    
    jsonResponse([
    "status"=>true
    ]);
    
    break;

/* ==========================================
   DELETE
========================================== */

case "delete":

    $id = intval($_POST['id']);
    
    if($id <= 0){
        jsonResponse([
            "status"=>false,
            "message"=>"ID không hợp lệ"
        ]);
    }
    
    /* lấy comp + ảnh */
    $row = $GLOBALS['sp']->GetRow("
        SELECT comp,img_thumb_vn
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE id=?
        ",[$id]);

        if(!$row){
        jsonResponse([
            "status"=>false,
            "message"=>"Item không tồn tại"
        ]);
        }

    $comp_id = $row['comp'];
    $image = $row['img_thumb_vn'];
        
    /* xoá file ảnh */
    if($image){

        $filePath = $_SERVER['DOCUMENT_ROOT']."/".$image;

        if(file_exists($filePath)){
            unlink($filePath);
        }

    }
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
        "status"=>true
    ]);
    
    break;

/* ==========================================
   DELETE MULTI
========================================== */

case "delete_multi":

    $ids = isset($_POST['ids']) ? $_POST['ids'] : array();
    
    if(!is_array($ids) || empty($ids)){
        jsonResponse([
            "status"=>false,
            "message"=>"Không có dữ liệu"
        ]);
    }
    
    $comp_ids = [];
    
    foreach($ids as $id){
    
        $id = intval($id);
        if($id <= 0) continue;
    
        /* lấy thông tin item */
        $row = $GLOBALS['sp']->GetRow("
            SELECT comp,img_thumb_vn
            FROM {$GLOBALS['db_sp']}.articlelist
            WHERE id=?
        ",[$id]);
    
        if(!$row) continue;
    
        $comp_ids[$row['comp']] = $row['comp'];
    
        /* xoá file ảnh */
        if(!empty($row['img_thumb_vn'])){
    
            $file = $_SERVER['DOCUMENT_ROOT'].'/'.$row['img_thumb_vn'];
    
            if(file_exists($file) && is_file($file)){
                unlink($file);
            }
    
        }
    
        /* xoá detail */
        $GLOBALS['sp']->Execute("
            DELETE FROM {$GLOBALS['db_sp']}.articlelist_detail
            WHERE articlelist_id=?
        ",[$id]);
    
        /* xoá item */
        $GLOBALS['sp']->Execute("
            DELETE FROM {$GLOBALS['db_sp']}.articlelist
            WHERE id=?
        ",[$id]);
    }
    
    /* rebuild num theo từng module */
    foreach($comp_ids as $comp){
    
        $GLOBALS['sp']->Execute("SET @num := 0");
    
        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.articlelist
            SET num = (@num := @num + 1)
            WHERE comp=?
            ORDER BY num ASC
        ",[$comp]);
    
    }
    
    jsonResponse([
        "status"=>true
    ]);
    
    break;


/* ==========================================
   REORDER
========================================== */

case "reorder":

    $ids = isset($_POST['id']) ? $_POST['id'] : array();
    $nums = isset($_POST['num']) ? $_POST['num'] : array();

    if(empty($ids) || empty($nums)){
        jsonResponse(array("status"=>false));
    }

    for($i=0;$i<count($ids);$i++){

        $id = intval($ids[$i]);
        $num = intval($nums[$i]);

        $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.articlelist
            SET num=?
            WHERE id=?
        ", array($num,$id));
    }

    jsonResponse(array("status"=>true));

break;

}