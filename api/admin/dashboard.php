<?php

/* ================= CORS ================= */
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

/* ================= CONFIG ================= */
require_once("../../includes/config.php");

/* ================= TIMEZONE ================= */
date_default_timezone_set('Asia/Ho_Chi_Minh');

/* ================= RESPONSE ================= */
$data = [
    "orders" => 0,
    "orders_today" => 0,
    "revenue" => 0,
    "revenue_today" => 0,
    "pending" => 0,
    "shipping" => 0,
    "done" => 0,
    "contacts" => 0,
    "products" => 0
];

/* ================= REVENUE BY MONTH ================= */

/* ================= ORDERS ================= */
$sql = "
SELECT 
    COUNT(*) as total_orders,

    SUM(CASE 
        WHEN DATE(created_at) = CURDATE() 
        THEN 1 ELSE 0 END) as orders_today,

    SUM(CASE WHEN status=0 THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status=1 THEN 1 ELSE 0 END) as shipping,
    SUM(CASE WHEN status=2 THEN 1 ELSE 0 END) as done

FROM {$GLOBALS['db_sp']}.orders
";

$row = $GLOBALS['sp']->getRow($sql);

if ($row) {
    $data['orders'] = intval($row['total_orders']);
    $data['orders_today'] = intval($row['orders_today']);
    $data['pending'] = intval($row['pending']);
    $data['shipping'] = intval($row['shipping']);
    $data['done'] = intval($row['done']);
}

// if ($row) {
//     $data['orders'] = intval($row['total_orders']);
//     $data['orders_today'] = intval($row['orders_today']);
//     $data['revenue'] = intval($row['revenue']);
//     $data['revenue_today'] = intval($row['revenue_today']);
//     $data['pending'] = intval($row['pending']);
//     $data['shipping'] = intval($row['shipping']);
//     $data['done'] = intval($row['done']);
// }
/* ================= REVENUE ================= */
$sql = "
SELECT 
    SUM(ol.tamtinh) as revenue,

    SUM(CASE 
        WHEN DATE(o.created_at) = CURDATE() 
        THEN ol.tamtinh ELSE 0 END) as revenue_today

FROM {$GLOBALS['db_sp']}.orders_line ol
LEFT JOIN {$GLOBALS['db_sp']}.orders o 
ON o.id = ol.order_id
";

$row = $GLOBALS['sp']->getRow($sql);

if ($row) {
    $data['revenue'] = intval($row['revenue']);
    $data['revenue_today'] = intval($row['revenue_today']);
}
/* ================= REVENUE BY MONTH ================= */
$sql = "
SELECT 
    MONTH(o.created_at) as m,
    SUM(ol.tamtinh) as total
FROM {$GLOBALS['db_sp']}.orders_line ol
LEFT JOIN {$GLOBALS['db_sp']}.orders o 
ON o.id = ol.order_id
WHERE YEAR(o.created_at) = YEAR(NOW())
GROUP BY MONTH(o.created_at)
";

$list = $GLOBALS['sp']->getAll($sql);

$revenueByMonth = array_fill(1, 12, 0);

foreach ($list as $row) {
    $revenueByMonth[intval($row['m'])] = intval($row['total']);
}

$data['revenue_month'] = array_values($revenueByMonth);
/* ================= CONTACT ================= */
$sql = "SELECT COUNT(*) as total 
        FROM {$GLOBALS['db_sp']}.contact 
        WHERE is_read = 0";

$row = $GLOBALS['sp']->getRow($sql);

if ($row) {
    $data['contacts'] = intval($row['total']);
}

/* ================= PRODUCTS ================= */
$sql = "SELECT COUNT(*) as total 
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE comp = 2";

$row = $GLOBALS['sp']->getRow($sql);

if ($row) {
    $data['products'] = intval($row['total']);
}
/* ================= RESPONSE ================= */
echo json_encode([
    "status" => true,
    "data" => $data
]);
