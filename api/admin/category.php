<?php

/* ================= CORS ================= */

if(isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once("../../includes/config.php");
require_once("../../functions/function-api.php");

/* ================= GET PARAM ================= */

$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = isset($_REQUEST['comp']) ? intval($_REQUEST['comp']) : 0;
$lang = isset($_REQUEST['lang']) ? intval($_REQUEST['lang']) : 1;


/* ================= BUILD TREE ================= */

function buildTree($rows)
{

    $map  = array();
    $tree = array();

    foreach($rows as $r) {
        $r['children'] = array();
        $map[$r['id']] = $r;
    }

    foreach($map as $id => $node) {

        if($node['parent_id'] && isset($map[$node['parent_id']])) {
            $map[$node['parent_id']]['children'][] = &$map[$id];
        } else {
            $tree[] = &$map[$id];
        }

    }

    return $tree;

}


/* ================= DELETE CHILD ================= */

function deleteChild($id)
{

    $rows = $GLOBALS['sp']->getAll("
    SELECT id
    FROM {$GLOBALS['db_sp']}.categories
    WHERE parent_id=?
    ", array($id));

    foreach($rows as $r) {
        deleteChild($r['id']);
    }

    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.categories
    WHERE id=?
    ", array($id));

    $GLOBALS['sp']->Execute("
    DELETE FROM {$GLOBALS['db_sp']}.categories_detail
    WHERE categories_id=?
    ", array($id));

}



/* =================================================
   SWITCH
================================================= */

switch($act) {
    /* =================================================
       REORDER
    ================================================= */
    case "reorder_tree":

        $ids = isset($_POST['id']) ? $_POST['id'] : array();
        $parents = isset($_POST['parent_id']) ? $_POST['parent_id'] : array();
        $nums = isset($_POST['num']) ? $_POST['num'] : array();

        if(!empty($ids)) {

            foreach($ids as $i => $id) {

                $id = intval($id);

                $parent = isset($parents[$i]) ? intval($parents[$i]) : 0;
                $num = isset($nums[$i]) ? intval($nums[$i]) : 0;

                $GLOBALS['sp']->Execute("
                    UPDATE {$GLOBALS['db_sp']}.categories
                    SET parent_id = ?, num = ?
                    WHERE id = ?
                ", array($parent, $num, $id));

            }

        }

        echo json_encode(array(
            "status" => true
        ));

        exit;
        /* =================================================
           LIST
        ================================================= */

    case "list":

        $rows = $GLOBALS['sp']->getAll("
        SELECT 
        c.id,
        c.parent_id,
        c.active,c.home,
        c.num,c.img_vn,
        cd.name,cd.languageid
        FROM {$GLOBALS['db_sp']}.categories c
        LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
        ON cd.categories_id = c.id
        WHERE c.comp = ?
        ORDER BY c.num ASC
        ", array($comp));
        $data = [];
        foreach($rows as $r) {

            $id = $r['id'];

            if(!isset($data[$id])) {
                $data[$id] = [
                    "id" => $r["id"],
                    "parent_id" => $r["parent_id"],
                    "active" => $r["active"],
                    "home" => $r["home"],
                    "num" => $r["num"],
                    "img_vn" => $r["img_vn"],
                    "names" => []
                ];
            }

            if($r["languageid"]) {
                $data[$id]["names"][$r["languageid"]] = $r["name"];
            }
        }

        $tree = buildTree(array_values($data));

        echo json_encode([
            "status" => true,
            "data" => $tree
        ], JSON_UNESCAPED_UNICODE);

        exit;



        /* =================================================
           DETAIL
        ================================================= */

    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $row = $GLOBALS['sp']->getRow("
    SELECT *
    FROM {$GLOBALS['db_sp']}.categories
    WHERE id=?
    ", array($id));

        $langs = $GLOBALS['sp']->getAll("
    SELECT *
    FROM {$GLOBALS['db_sp']}.categories_detail
    WHERE categories_id=?
    ", array($id));

        echo json_encode(array(
            "status" => true,
            "category" => $row,
            "languages" => $langs
        ), JSON_UNESCAPED_UNICODE);

        exit;
        /* =================================================
   UPDATE IMAGE LIST
================================================= */

    case "update_image":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if($id && isset($_FILES['image']) && $_FILES['image']['name'] != '') {

            $uploadFolder = 'hinh-anh/cate/';
            $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/'.$uploadFolder;

            if(!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            /* LẤY HÌNH CŨ */
            $old = $GLOBALS['sp']->getOne("
        SELECT img_vn
        FROM {$GLOBALS['db_sp']}.categories
        WHERE id=?
        ", [$id]);

            if($old) {
                $oldPath = $_SERVER['DOCUMENT_ROOT'].'/'.$old;
                if(file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            /* TẠO FILE NAME */
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = time().'_'.rand(1000, 9999).'.'.$ext;

            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir.$filename)) {

                $image = $uploadFolder.$filename;

                $GLOBALS['sp']->Execute("
            UPDATE {$GLOBALS['db_sp']}.categories
            SET img_vn=?
            WHERE id=?
            ", [$image,$id]);

                echo json_encode([
                    "status" => true,
                    "image" => $image
                ]);
                exit;
            }
        }

        echo json_encode([
            "status" => false
        ]);

        exit;
        /* =================================================
                   UPDATE NAME LIST
                ================================================= */
    case "update_name":

        $id = $_POST["id"];
        $lang = $_POST["languageid"];
        $name = $_POST["name"];
        $slug = slugify($name);
        $GLOBALS['sp']->execute("
            UPDATE {$GLOBALS['db_sp']}.categories_detail
            SET name = ?, slug = ?
            WHERE categories_id = ?
            AND languageid = ?
            ", [$name,$slug,$id,$lang]);

        echo json_encode([
        "status" => true
        ]);

        exit;

        /* =================================================
           ADD / UPDATE
        ================================================= */

    case "add":

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

        $uploadFolder = 'hinh-anh/cate/';
        if(isset($_FILES['hinhdanhmuc']) && $_FILES['hinhdanhmuc']['name'] != '') {


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
            if($old && file_exists($oldPath)) {
                unlink($oldPath);
            }
            $ext  = pathinfo($_FILES['hinhdanhmuc']['name'], PATHINFO_EXTENSION);
            $filename = time().'_'.rand(1000, 9999).'.'.$ext;

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

        exit;



        /* =================================================
           DELETE
        ================================================= */

    case "delete":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if($id) {
            deleteChild($id);
        }

        echo json_encode(array(
            "status" => true
        ));

        exit;



        /* =================================================
           ORDER
        ================================================= */

    case "order":

        $ids = isset($_POST['ids']) ? $_POST['ids'] : array();

        foreach($ids as $num => $id) {

            $GLOBALS['sp']->Execute("
UPDATE {$GLOBALS['db_sp']}.categories
SET num=?
WHERE id=?
", array($num + 1,$id));

        }

        echo json_encode(array(
            "status" => true
        ));

        exit;

}
