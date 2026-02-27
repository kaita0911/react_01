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

    // ================= LIST =================
    case "list":

        $sql = "SELECT * FROM infos ORDER BY id ASC";
        $rs  = $GLOBALS["sp"]->getAll($sql);

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));

        break;

    // ================= DETAIL =================
    case "detail":

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "SELECT * FROM infos WHERE id = ?";
        $rs  = $GLOBALS["sp"]->getRow($sql, array($id));

        echo json_encode(array(
            "status" => true,
            "data"   => $rs
        ));

        break;

    default:
        echo json_encode(array(
            "status" => false,
            "message" => "Invalid action"
        ));
}