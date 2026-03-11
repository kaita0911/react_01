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

$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {

    // ================= LIST =================
    case "list":

        $sql = "SELECT * FROM language ORDER BY id ASC";
        $data  = $GLOBALS["sp"]->getAll($sql);

        echo json_encode(array(
            "status" => true,
            "data" => $data
        ));
    break;



    // ================= ADD =================
    case "add":

        $name   = isset($_POST['name']) ? trim($_POST['name']) : '';
        $code   = isset($_POST['code']) ? trim($_POST['code']) : '';
        $active = isset($_POST['active']) ? intval($_POST['active']) : 1;
    
        if ($name == '' || $code == '') {
            echo json_encode(array(
                "status" => false,
                "message" => "Thiếu dữ liệu"
            ));
            exit;
        }
    
        try {
    
            $sql = "INSERT INTO language (name, code, active)
                    VALUES (?, ?, ?)";
    
            $GLOBALS['sp']->execute($sql, [$name, $code, $active]);
    
            $id = $GLOBALS['sp']->Insert_ID();
    
            echo json_encode(array(
                "status" => true,
                "data" => array(
                    "id" => $id,
                    "name" => $name,
                    "code" => $code,
                    "active" => $active
                )
            ));
    
        } catch (Exception $e) {
    
            echo json_encode(array(
                "status" => false,
                "message" => "Lỗi hệ thống: " . $e->getMessage()
            ));
    
        }
    
    break;
        ////
    case "set_default":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
        if ($id <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "ID không hợp lệ"
            ]);
            exit;
        }
    
        try {
    
            // reset tất cả
            $GLOBALS['sp']->execute("UPDATE language SET is_default = 0");
    
            // set mặc định
            $GLOBALS['sp']->execute(
                "UPDATE language SET is_default = 1 WHERE id=?",
                [$id]
            );
    
            echo json_encode([
                "status" => true
            ]);
    
        } catch (Exception $e) {
    
            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    
    break;

    // ================= UPDATE =================
    case "update":

        $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name   = isset($_POST['name']) ? $_POST['name'] : '';
        $code   = isset($_POST['code']) ? $_POST['code'] : '';
        $active = isset($_POST['active']) ? intval($_POST['active']) : 1;

        if (!$id) {
            echo json_encode(array(
                "status" => false,
                "message" => "Không tìm thấy ID"
            ));
            exit;
        }

        $sql = "
        UPDATE language
        SET 
        name='$name',
        code='$code',
        active='$active'
        WHERE id=$id
        ";

        $db->query($sql);

        echo json_encode(array(
            "status" => true
        ));

    break;



    // ================= DELETE =================
    case "delete":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (!$id) {
            echo json_encode(array(
                "status" => false,
                "message" => "Không tìm thấy ID"
            ));
            exit;
        }

        $db->query("DELETE FROM language WHERE id=$id");

        echo json_encode(array(
            "status" => true
        ));

    break;



    // ================= DELETE MULTIPLE =================
    case "delete_multiple":

        $input = json_decode(file_get_contents("php://input"), true);
        $ids = isset($input['ids']) ? $input['ids'] : array();

        if (empty($ids)) {
            echo json_encode(array(
                "status" => false,
                "message" => "Không có ID"
            ));
            exit;
        }

        $ids = array_map('intval', $ids);
        $ids = implode(",", $ids);

        $db->query("DELETE FROM language WHERE id IN ($ids)");

        echo json_encode(array(
            "status" => true
        ));

    break;



    default:
        echo json_encode(array(
            "status" => false,
            "message" => "API không tồn tại"
        ));
}