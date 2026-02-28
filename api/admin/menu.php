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

$act = isset($_GET['act']) ? $_GET['act'] : 'list';

switch ($act) {
    ////xoá nhièu
    case "delete_multiple":

        $ids = isset($_POST['ids']) ? $_POST['ids'] : array();
    
        if (empty($ids)) {
            echo json_encode(array("status" => false));
            exit;
        }
    
        try {
    
            foreach ($ids as $id) {
    
                $id = intval($id);
    
                $GLOBALS['sp']->Execute(
                    "DELETE FROM {$GLOBALS['db_sp']}.menu_detail WHERE menu_id=?",
                    array($id)
                );
    
                $GLOBALS['sp']->Execute(
                    "DELETE FROM {$GLOBALS['db_sp']}.menu WHERE id=?",
                    array($id)
                );
            }
    
            echo json_encode(array("status" => true));
    
        } catch (Exception $e) {
    
            echo json_encode(array(
                "status" => false,
                "message" => $e->getMessage()
            ));
        }
    
        break;
    // =========================
    // DELETE
    // =========================
    case "delete":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {
            echo json_encode(array(
                "status" => false,
                "message" => "ID không hợp lệ"
            ));
            exit;
        }

        try {

            // xoá detail trước
            $sql1 = "DELETE FROM {$GLOBALS['db_sp']}.menu_detail
                    WHERE menu_id=?";
            $GLOBALS['sp']->Execute($sql1, array($id));

            // xoá menu
            $sql2 = "DELETE FROM {$GLOBALS['db_sp']}.menu
                    WHERE id=?";
            $GLOBALS['sp']->Execute($sql2, array($id));

            echo json_encode(array("status" => true));

        } catch (Exception $e) {

            echo json_encode(array(
                "status" => false,
                "message" => $e->getMessage()
            ));
        }

        break;
    // =========================
    // săp xếp
    case "reorder":
        $ids  = isset($_POST['id']) ? $_POST['id'] : array();
        $nums = isset($_POST['num']) ? $_POST['num'] : array();
        foreach ($ids as $i => $id) {

        $id  = intval($id);
        $num = isset($nums[$i]) ? intval($nums[$i]) : 0;

        $conn->query("UPDATE menu SET num=$num WHERE id=$id");
        }

        echo json_encode(["status" => true]);
    break;
    // =========================
    // =========================
    // THÊM MỚI
    // =========================
    case "add":

        $comp     = isset($_POST['comp']) ? intval($_POST['comp']) : 0;
        $link_out = isset($_POST['link_out']) ? trim($_POST['link_out']) : '';
        $has_sub  = isset($_POST['has_sub']) ? intval($_POST['has_sub']) : 0;

        $name       = isset($_POST['name']) ? trim($_POST['name']) : '';
        $unique_key = isset($_POST['unique_key']) ? trim($_POST['unique_key']) : '';
        $lang       = isset($_POST['languageid']) ? intval($_POST['languageid']) : 1;

        if ($name == '') {
            echo json_encode(array(
                "status" => false,
                "message" => "Tên menu không được rỗng"
            ));
            exit;
        }

        try {

            // ===== lấy num cuối + 1 =====
            $row = $GLOBALS["sp"]->getRow("
                SELECT MAX(num) AS max_num
                FROM {$GLOBALS['db_sp']}.menu
            ");

            $num = intval($row['max_num']) + 1;

            // ===== INSERT menu =====
            $sql = "INSERT INTO {$GLOBALS['db_sp']}.menu
                    (comp, link_out, has_sub, num,active)
                    VALUES (?, ?, ?, ?,1)";

            $GLOBALS['sp']->Execute($sql, array(
                $comp,
                $link_out,
                $has_sub,
                $num
            ));

            // ===== lấy ID vừa insert =====
            $menu_id = $GLOBALS['sp']->Insert_ID();

            // ===== INSERT menu_detail =====
            $sql2 = "INSERT INTO {$GLOBALS['db_sp']}.menu_detail
                    (menu_id, languageid, name, unique_key)
                    VALUES (?, ?, ?, ?)";

            $GLOBALS['sp']->Execute($sql2, array(
                $menu_id,
                $lang,
                $name,
                $unique_key
            ));

            echo json_encode(array(
                "status" => true,
                "id"     => $menu_id
            ));

        } catch (Exception $e) {

            echo json_encode(array(
                "status" => false,
                "message" => $e->getMessage()
            ));
        }

        break;

    // =========================
    // update
    // =========================
    case "update":

        $id       = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $comp     = isset($_POST['comp']) ? intval($_POST['comp']) : 0;
        $link_out = isset($_POST['link_out']) ? trim($_POST['link_out']) : '';
        $has_sub  = isset($_POST['has_sub']) ? intval($_POST['has_sub']) : 0;
    
        $name       = isset($_POST['name']) ? trim($_POST['name']) : '';
        $unique_key = isset($_POST['unique_key']) ? trim($_POST['unique_key']) : '';
        $lang       = isset($_POST['languageid']) ? intval($_POST['languageid']) : 1;
    
        if ($id <= 0) {
            echo json_encode(array(
                "status" => false,
                "message" => "ID không hợp lệ"
            ));
            exit;
        }
    
        try {
    
            // ===== UPDATE bảng menu =====
            $sql = "UPDATE {$GLOBALS['db_sp']}.menu
                    SET comp=?, link_out=?, has_sub=?
                    WHERE id=?";
    
            $GLOBALS['sp']->Execute($sql, array(
                $comp,
                $link_out,
                $has_sub,
                $id
            ));
    
            // ===== UPDATE menu_detail =====
            $sql2 = "UPDATE {$GLOBALS['db_sp']}.menu_detail
                     SET name=?, unique_key=?
                     WHERE menu_id=? AND languageid=?";
    
            $GLOBALS['sp']->Execute($sql2, array(
                $name,
                $unique_key,
                $id,
                $lang
            ));
    
            echo json_encode(array(
                "status" => true
            ));
    
        } catch (Exception $e) {
    
            echo json_encode(array(
                "status" => false,
                "message" => $e->getMessage()
            ));
        }
    
        break;
    // =========================
    // LIST MENU
    // =========================
    case "list":

        // Lấy menu
        $menus = $GLOBALS["sp"]->getAll("
            SELECT * 
            FROM {$GLOBALS['db_sp']}.menu 
            ORDER BY num ASC
        ");

        // Lấy chi tiết menu
        $details = $GLOBALS["sp"]->getAll("
            SELECT * 
            FROM {$GLOBALS['db_sp']}.menu_detail
        ");

        // Gom detail theo menu_id
        $detailMap = [];

        foreach ($details as $d) {
            $detailMap[$d['menu_id']][$d['languageid']] = $d;
        }

        // Gắn detail vào menu
        foreach ($menus as &$item) {
            $id = $item['id'];
            $item['details'] = isset($detailMap[$id]) ? $detailMap[$id] : [];
        }

        echo json_encode([
            "status" => true,
            "data"   => $menus
        ]);

        break;

    // =========================
    // DETAIL MENU
    // =========================
    case "detail":

        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);

        $menu = $GLOBALS["sp"]->getRow("
            SELECT * 
            FROM {$GLOBALS['db_sp']}.menu 
            WHERE id = {$id}
        ");

        $details = $GLOBALS["sp"]->getAll("
            SELECT * 
            FROM {$GLOBALS['db_sp']}.menu_detail
            WHERE menu_id = {$id}
        ");

        echo json_encode([
            "status" => true,
            "data"   => [
                "menu"    => $menu,
                "details" => $details
            ]
        ]);

        break;

    default:
        echo json_encode([
            "status" => false,
            "message" => "Invalid action"
        ]);
}