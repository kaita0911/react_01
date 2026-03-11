<?php

// ===== CORS =====
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

$act  = isset($_GET['act']) ? $_GET['act'] : 'list';
$lang = isset($_GET['lang']) ? intval($_GET['lang']) : 1;

switch ($act) {

    // =========================
    // LIST
    // =========================
    case "list":

        $sql = "
            SELECT 
                f.*,
                d.name,
                d.content,
                d.address
            FROM {$GLOBALS['db_sp']}.footer f
            LEFT JOIN {$GLOBALS['db_sp']}.footer_detail d
                ON f.id = d.footer_id
                AND d.languageid = $lang
            ORDER BY f.id DESC
        ";

        $data = $GLOBALS["sp"]->getAll($sql);

        echo json_encode([
            "success" => true,
            "data" => $data
        ]);

    break;


    // =========================
    // DETAIL
    // =========================
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
        // ===== FOOTER =====
        $footer = $GLOBALS["sp"]->getRow("
            SELECT *
            FROM {$GLOBALS['db_sp']}.footer
            WHERE id=$id
        ");
    
        if(!$footer){
            echo json_encode(array(
                "success" => false
            ));
            exit;
        }
    
        // ===== GET ALL LANGUAGE DETAIL =====
        $rows = $GLOBALS["sp"]->getAll("
            SELECT *
            FROM {$GLOBALS['db_sp']}.footer_detail
            WHERE footer_id=$id
        ");
    
        $languages = array();
    
        if(!empty($rows)){
            foreach($rows as $r){
                $languages[$r['languageid']] = array(
                    "name" => $r['name'],
                    "content" => $r['content'],
                    "address" => $r['address']
                );
            }
        }
    
        $footer["languages"] = $languages;
    
        echo json_encode(array(
            "success" => true,
            "data" => $footer
        ));
    
    break;


    // =========================
    // SAVE
    // =========================
    case "save":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        $hotline = isset($_POST['hotline']) ? addslashes($_POST['hotline']) : '';
        $email   = isset($_POST['email']) ? addslashes($_POST['email']) : '';
        $map     = isset($_POST['map']) ? addslashes($_POST['map']) : '';
    
        $languages = isset($_POST['languages']) 
            ? json_decode($_POST['languages'], true) 
            : array();
    
        // ===== SAVE FOOTER =====
        if ($id == 0) {
    
            $sql = "
            INSERT INTO {$GLOBALS['db_sp']}.footer
            (hotline,email,map)
            VALUES
            ('$hotline','$email','$map')
            ";
    
            $GLOBALS["sp"]->query($sql);
            $id = $GLOBALS["sp"]->Insert_ID();
    
        } else {
    
            $sql = "
            UPDATE {$GLOBALS['db_sp']}.footer
            SET
                hotline='$hotline',
                email='$email',
                map='$map'
            WHERE id=$id
            ";
    
            $GLOBALS["sp"]->query($sql);
        }
    
    
        // ===== SAVE MULTI LANGUAGE =====
        if (!empty($languages)) {
    
            foreach ($languages as $lang_id => $value) {
    
                $name    = isset($value['name']) ? addslashes($value['name']) : '';
                $content = isset($value['content']) ? addslashes($value['content']) : '';
                $address = isset($value['address']) ? addslashes($value['address']) : '';
    
                $detail = $GLOBALS["sp"]->getRow("
                    SELECT id
                    FROM {$GLOBALS['db_sp']}.footer_detail
                    WHERE footer_id=$id
                    AND languageid=$lang_id
                ");
    
                if ($detail) {
    
                    $sql = "
                    UPDATE {$GLOBALS['db_sp']}.footer_detail
                    SET
                        name='$name',
                        content='$content',
                        address='$address'
                    WHERE id=".$detail['id']."
                    ";
    
                } else {
    
                    $sql = "
                    INSERT INTO {$GLOBALS['db_sp']}.footer_detail
                    (footer_id,languageid,name,content,address)
                    VALUES
                    ($id,$lang_id,'$name','$content','$address')
                    ";
                }
    
                $GLOBALS["sp"]->query($sql);
            }
        }
    
        echo json_encode(array(
            "success" => true,
            "id" => $id
        ));
    
    break;

    // =========================
    // DELETE
    // =========================
    case "delete":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $GLOBALS["sp"]->query("
            DELETE FROM {$GLOBALS['db_sp']}.footer_detail
            WHERE footer_id=$id
        ");

        $GLOBALS["sp"]->query("
            DELETE FROM {$GLOBALS['db_sp']}.footer
            WHERE id=$id
        ");

        echo json_encode([
            "success" => true
        ]);

    break;
}