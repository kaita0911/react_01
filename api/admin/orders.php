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
    case "latest":

        $sql = "
        SELECT 
            o.id,o.order_code,
            o.name,
            o.status,
            SUM(ol.tamtinh) as total
        FROM {$GLOBALS['db_sp']}.orders o
        LEFT JOIN {$GLOBALS['db_sp']}.orders_line ol
            ON o.id = ol.order_id
        GROUP BY o.id
        ORDER BY o.id DESC
        LIMIT 5
        ";

        $rs = $GLOBALS['sp']->getAll($sql);

        echo json_encode([
            "status" => true,
            "data" => $rs
        ]);

        break;
        /* ================= COUNT ================= */
    case "count":

        $sql = "SELECT COUNT(*) as total
                FROM {$GLOBALS['db_sp']}.orders";

        $row = $GLOBALS['sp']->getRow($sql);

        echo json_encode([
            "status" => true,
            "total" => intval($row['total'])
        ]);

        break;


        /* ================= COUNT NEW ================= */
    case "count_new":

        $sql = "SELECT COUNT(*) as total
                FROM {$GLOBALS['db_sp']}.orders
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
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $status  = isset($_GET['status']) ? $_GET['status'] : '';
        if ($page <= 0) {
            $page = 1;
        }
        if ($limit <= 0) {
            $limit = 20;
        }

        $offset = ($page - 1) * $limit;
        /* ===== WHERE ===== */
        $where = "WHERE 1";

        if ($keyword != '') {
            $keyword = addslashes($keyword);
            $where .= " AND (o.name LIKE '%$keyword%' OR o.phone LIKE '%$keyword%' OR o.id LIKE '%$keyword%')";
        }

        if ($status !== '') {
            $where .= " AND o.status = '" . intval($status) . "'";
        }
        /* ===== total ===== */
        $sqlTotal = "SELECT COUNT(*) as total
                     FROM {$GLOBALS['db_sp']}.orders";

        $rowTotal = $GLOBALS['sp']->getRow($sqlTotal);
        $total = $rowTotal['total'];

        $sql = "SELECT 
        o.*,
        SUM(ol.tamtinh) as total
        FROM {$GLOBALS['db_sp']}.orders o
        LEFT JOIN {$GLOBALS['db_sp']}.orders_line ol
        ON o.id = ol.order_id
        $where
        GROUP BY o.id
        ORDER BY o.id DESC
        LIMIT $offset,$limit";
        $rs = $GLOBALS['sp']->getAll($sql);

        echo json_encode([
            "status" => true,
            "data"   => $rs,
            "total"  => intval($total),
            "page"   => $page,
            "limit"  => $limit
        ]);

        break;


        /* ================= DETAIL ================= */
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "ID không hợp lệ"
            ]);
            exit;
        }

        $GLOBALS['sp']->Execute(
            "UPDATE {$GLOBALS['db_sp']}.orders
             SET is_read = 1
             WHERE id=?",
            array($id)
        );

        $sql = "SELECT *
                FROM {$GLOBALS['db_sp']}.orders
                WHERE id=?";

        $order = $GLOBALS['sp']->getRow($sql, array($id));

        $sqlItems = "SELECT *
                     FROM {$GLOBALS['db_sp']}.orders_line
                     WHERE order_id=?";

        $items = $GLOBALS['sp']->getAll($sqlItems, array($id));

        echo json_encode([
            "status" => true,
            "data" => [
                "order" => $order,
                "items" => $items
            ]
        ]);

        break;


        /* ================= UPDATE STATUS ================= */
    case "update_status":

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

        if ($id <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "ID không hợp lệ"
            ]);
            exit;
        }

        $GLOBALS['sp']->Execute(
            "UPDATE {$GLOBALS['db_sp']}.orders
             SET status=?
             WHERE id=?",
            array($status, $id)
        );

        echo json_encode([
            "status" => true
        ]);

        break;


        /* ================= DASHBOARD ================= */
    case "dashboard":

        $sql = "
        SELECT 
        COUNT(*) as total_orders,
        SUM(total_price) as revenue,
        SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) as Đang chờ,
        SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) as Đang giao,
        SUM(CASE WHEN status=2 THEN 1 ELSE 0 END) as Hoàn thành
        FROM {$GLOBALS['db_sp']}.orders
        ";

        $row = $GLOBALS['sp']->getRow($sql);

        echo json_encode([
            "status" => true,
            "data" => $row
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
            "DELETE FROM {$GLOBALS['db_sp']}.orders_line
             WHERE order_id=?",
            array($id)
        );

        $GLOBALS['sp']->Execute(
            "DELETE FROM {$GLOBALS['db_sp']}.orders
             WHERE id=?",
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

        $sql = "DELETE FROM {$GLOBALS['db_sp']}.orders
                WHERE id IN ($ids_str)";
        $GLOBALS['sp']->Execute($sql);

        $sql2 = "DELETE FROM {$GLOBALS['db_sp']}.orders_line
                 WHERE order_id IN ($ids_str)";
        $GLOBALS['sp']->Execute($sql2);

        echo json_encode([
            "status" => true
        ]);

        break;


    default:

        echo json_encode([
            "status" => false,
            "message" => "Invalid action"
        ]);
}
