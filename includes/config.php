<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ================= CONFIGURATION =================
$config = [];

// ✅ BASE PATH & URL (đặt tên thư mục dự án của bạn)
$config['BASE_DIR'] = $_SERVER['DOCUMENT_ROOT'];
$config['BASE_URL'] = 'http://react_01.local';

// ================= DATABASE =================
$DBTYPE = 'mysql';
$DBHOST = 'localhost';
$DBUSER = 'root';      // user mặc định XAMPP
$DBPASSWORD = '';       // trống (nếu bạn chưa đặt password MySQL)
$DBNAME = 'tanphatmobile';  // nhớ tạo DB này trong phpMyAdmin

// ================= BOOTSTRAP =================
require_once($config['BASE_DIR'] . '/includes/bootstrap.php');

// ================= CONNECT DATABASE =================
$db = new mysqli($DBHOST, $DBUSER, $DBPASSWORD, $DBNAME);
$db->set_charset("utf8");

if ($db->connect_error) {
    die("❌ Kết nối Database thất bại: " . $db->connect_error);
}

// echo "✅ Kết nối Database thành công!<br>";
