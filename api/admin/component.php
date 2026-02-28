<?php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Credentials: true");
}
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once("../../includes/config.php");

$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {
    // ================= LIST ACTIVE =================
    case "list_active":

    $sql = "SELECT c.*, d.name AS detail_name
            FROM {$GLOBALS['db_sp']}.component AS c
            LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS d
            ON c.id = d.component_id
            WHERE c.active = 1
            ORDER BY c.id DESC";

    $rs = $GLOBALS['sp']->getAll($sql);

    echo json_encode(array(
        "status" => true,
        "data"   => $rs
    ));
    break;
    // ================= UPDATE =================
    case "update":  

        $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $num    = isset($_POST['num']) ? intval($_POST['num']) : 0;
        $do     = isset($_POST['do']) ? trim($_POST['do']) : '';
        $active = isset($_POST['active']) ? intval($_POST['active']) : 1;
        $name   = isset($_POST['detail_name']) ? trim($_POST['detail_name']) : '';
        //$content = isset($_POST['content']) ? trim($_POST['content']) : '';

        if ($id <= 0 || $do == '' || $name == '') {
            echo json_encode([
                "status" => false,
                "message" => "Thiếu dữ liệu"
            ]);
            break;
        }

        try {

            // 1️⃣ Update bảng component
            $sql = "UPDATE {$GLOBALS['db_sp']}.component
                    SET num=?, do=?, active=?
                    WHERE id=?";

            $GLOBALS['sp']->execute($sql, [$num, $do, $active, $id]);

            // 2️⃣ Update bảng component_detail
            $sql2 = "UPDATE {$GLOBALS['db_sp']}.component_detail
                    SET name=?
                    WHERE component_id=?";

            $GLOBALS['sp']->execute($sql2, [$name, $id]);

            echo json_encode([
                "status" => true,
                "data" => [
                    "id" => $id,
                    "num" => $num,
                    "do" => $do,
                    "active" => $active,
                    "detail_name" => $name
                ]
            ]);

        } catch (Exception $e) {

            echo json_encode([
                "status" => false,
                "message" => "Lỗi hệ thống: " . $e->getMessage()
            ]);
        }

        break;
    // ================= ADD =================
    case "add":

    $num    = isset($_POST['num']) ? intval($_POST['num']) : 0;
    $do     = isset($_POST['do']) ? trim($_POST['do']) : '';
    $active = isset($_POST['active']) ? intval($_POST['active']) : 1;
    $name   = isset($_POST['detail_name']) ? trim($_POST['detail_name']) : '';
    //$content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if ($do == '' || $name == '') {
        echo json_encode([
            "status" => false,
            "message" => "Thiếu dữ liệu"
        ]);
        break;
    }

    try {

        // 1️⃣ Insert bảng component
        $sql = "INSERT INTO {$GLOBALS['db_sp']}.component (num, do, active)
                VALUES (?, ?, ?)";

        $GLOBALS['sp']->execute($sql, [$num, $do, $active]);

        // Lấy ID vừa thêm
        $component_id = $GLOBALS['sp']->Insert_ID();

        // 2️⃣ Insert bảng component_detail
        $sql2 = "INSERT INTO {$GLOBALS['db_sp']}.component_detail
                 (component_id, name)
                 VALUES (?, ?)";

        $GLOBALS['sp']->execute($sql2, [$component_id, $name]);

        echo json_encode([
            "status" => true,
            "data" => [
                "id" => $component_id,
                "num" => $num,
                "do" => $do,
                "active" => $active,
                "detail_name" => $name
            ]
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => false,
            "message" => "Lỗi hệ thống: " . $e->getMessage()
        ]);
    }

    break;
    // ================= LIST =================
    case "list":

        $sql = "SELECT c.*, d.name AS detail_name
                FROM {$GLOBALS['db_sp']}.component AS c
                LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS d
                ON c.id = d.component_id
                ORDER BY c.id DESC";

        $rs = $GLOBALS['sp']->getAll($sql);

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));
        break;


    // ================= DETAIL =================
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "SELECT c.*, d.name, d.content
                FROM {$GLOBALS['db_sp']}.component AS c
                LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS d
                ON c.id = d.component_id
                WHERE c.id=?";

        $rs = $GLOBALS['sp']->getRow($sql, array($id));

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));
        break;

    // ================= xoá =================
    case "delete":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        if ($id <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "ID không hợp lệ"
            ]);
            exit;
        }
    
        // Xóa detail trước
        $GLOBALS['sp']->Execute(
            "DELETE FROM {$GLOBALS['db_sp']}.component_detail WHERE component_id=?",
            array($id)
        );
    
        // Xóa component
        $GLOBALS['sp']->Execute(
            "DELETE FROM {$GLOBALS['db_sp']}.component WHERE id=?",
            array($id)
        );
    
        echo json_encode([
            "status" => true
        ]);
    
        break;

  // ================= xoá nhiều =================
    case "delete_multiple":
        $data = json_decode(file_get_contents("php://input"), true);
        $ids = isset($data['ids']) ? $data['ids'] : array();
        

        if (empty($ids)) {
        echo json_encode(["status" => false, "message" => "Không có ID"]);
        exit;
        }

        $ids_str = implode(",", array_map('intval', $ids));

        $sql = "DELETE FROM component WHERE id IN ($ids_str)";
        $GLOBALS['sp']->Execute($sql);

        echo json_encode(["status" => true]);
        break;
   
    default:
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid action"
        ));
}