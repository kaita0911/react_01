<?php

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
    case "mark_read":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $GLOBALS['sp']->Execute(
            "UPDATE {$GLOBALS['db_sp']}.contact
    SET is_read=1
    WHERE id=?",
            array($id)
        );

        echo json_encode([
        "status" => true
        ]);

        break;
    case "count_new":

        $sql = "SELECT COUNT(*) as total
                FROM {$GLOBALS['db_sp']}.contact
                WHERE is_read = 0";

        $row = $GLOBALS['sp']->getRow($sql);

        echo json_encode([
            "status" => true,
            "total" => intval($row['total'])
        ]);

        break;
        /* ================= LIST ================= */
    case "list":
        $page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

        if ($page <= 0) {
            $page = 1;
        }
        if ($limit <= 0) {
            $limit = 20;
        }

        $offset = ($page - 1) * $limit;

        /* total */
        $sqlTotal = "SELECT COUNT(*) as total
                     FROM {$GLOBALS['db_sp']}.contact";

        $rowTotal = $GLOBALS['sp']->getRow($sqlTotal);
        $total = $rowTotal['total'];
        /* data */
        $sql = "SELECT *
                FROM {$GLOBALS['db_sp']}.contact
                ORDER BY id DESC   
                LIMIT $offset,$limit";

        $rs = $GLOBALS['sp']->getAll($sql);

        echo json_encode(array(
            "status" => true,
            "data"   => $rs,
            "total"  => intval($total),
            "page"   => $page,
            "limit"  => $limit
        ));

        break;


        /* ================= DETAIL ================= */
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $GLOBALS['sp']->Execute(
            "UPDATE {$GLOBALS['db_sp']}.contact
             SET is_read=1
             WHERE id=?",
            array($id)
        );
        $sql = "SELECT *
                FROM {$GLOBALS['db_sp']}.contact
                WHERE id=?";

        $rs = $GLOBALS['sp']->getRow($sql, array($id));

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));

        break;


        /* ================= ADD ================= */
    case "add":

        $name    = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        if ($name == '' || $email == '') {

            echo json_encode([
                "status" => false,
                "message" => "Thiếu dữ liệu"
            ]);

            exit;
        }

        $sql = "INSERT INTO {$GLOBALS['db_sp']}.contact
                (name,email,phone,message,created_at)
                VALUES (?,?,?,?,NOW())";

        $GLOBALS['sp']->Execute($sql, array(
            $name,
            $email,
            $phone,
            $message
        ));

        $id = $GLOBALS['sp']->Insert_ID();

        echo json_encode([
            "status" => true,
            "data" => [
                "id" => $id,
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "message" => $message
            ]
        ]);

        break;


        /* ================= DELETE ================= */
    case "delete":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id <= 0) {

            echo json_encode([
                "status" => false,
                "message" => "ID không hợp lệ"
            ]);

            exit;
        }

        $GLOBALS['sp']->Execute(
            "DELETE FROM {$GLOBALS['db_sp']}.contact WHERE id=?",
            array($id)
        );

        echo json_encode([
            "status" => true
        ]);

        break;


        /* ================= DELETE MULTIPLE ================= */
    case "delete_multiple":

        $data = json_decode(file_get_contents("php://input"), true);

        $ids = isset($data['ids']) ? $data['ids'] : array();

        if (empty($ids)) {

            echo json_encode([
                "status" => false,
                "message" => "Không có ID"
            ]);

            exit;
        }

        $ids_str = implode(",", array_map('intval', $ids));

        $sql = "DELETE FROM {$GLOBALS['db_sp']}.contact
                WHERE id IN ($ids_str)";

        $GLOBALS['sp']->Execute($sql);

        echo json_encode([
            "status" => true
        ]);

        break;


    default:

        echo json_encode(array(
            "status" => false,
            "message" => "Invalid action"
        ));
}
