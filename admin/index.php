<?php


include_once(__DIR__ . "/#include/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
include_once(__DIR__ . "/functions/function.php");
//include_once(__DIR__ . "/functions/categories.class.php");
date_default_timezone_set('Asia/Ho_Chi_Minh');
$now = date('H:i:s d-m-Y');
$smarty->assign('now', $now);
@session_start();

$languages = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1 ORDER BY id ASC");
$smarty->assign("languages", $languages);
$defaultLangRow = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1 AND `is_default`=1 LIMIT 1");
// Nếu có session, ưu tiên session
$currentLang = $defaultLangRow['id']; // dùng id ngôn ngữ mặc định
// Assign cho Smarty để dùng trong header.tpl
$smarty->assign('currentLang', $currentLang);
// -----------------------------
// ⚙️ Lấy dữ liệu cấu hình cơ bản
// -----------------------------
function getInfo($id)
{
	return $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = {$id}");
}

// Thông tin hiển thị
$smarty->assign("showvouchers", getInfo(22));
$smarty->assign("showphiship", getInfo(23));
$smarty->assign("showcart", getInfo(12));
$smarty->assign("showform", getInfo(14));
$smarty->assign("showanhdanhmuc", getInfo(19));
$smarty->assign("showtime", getInfo(13));
$smarty->assign("logoadmin", getInfo(1));
// // -----------------------------
// // 🧩 Danh sách ngôn ngữ
// // -----------------------------
// $sql_lg = "SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active = 1";
// $rs_lg = $GLOBALS["sp"]->getAll($sql_lg);
// $smarty->assign("languages", $rs_lg);
// $smarty->assign("countlang", count($rs_lg));

// -----------------------------
// 👤 Thông tin admin
// -----------------------------
$adminInfo = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.admin WHERE id = 3");
$smarty->assign("admin", $adminInfo);

// -----------------------------
// 📋 Sinh danh sách menu bên trái
// -----------------------------
// $sql = "
//     SELECT * 
//     FROM {$GLOBALS['db_sp']}.component 
//     WHERE active = 1 
//       AND id NOT IN (8, 23, 14, 15)
//     ORDER BY num ASC
// ";
$sql = "
  SELECT c.*, d.name
  FROM {$GLOBALS['db_sp']}.component c
  LEFT JOIN {$GLOBALS['db_sp']}.component_detail d ON c.id = d.component_id
  WHERE c.active= 1
  ORDER BY c.num ASC
";
//$rows = $GLOBALS['sp']->getAll($sql);
$components = $GLOBALS["sp"]->getAll($sql);

$listMenuLeft = [];
foreach ($components as $item) {
	$menu = [
		'id' => $item['id'],
		'name' => $item['name'],
		'icon' => $item['iconfont'],
		'links' => [
			'add' => "index.php?do=articlelist&act=add&comp={$item['id']}",
			'list' => "index.php?do=articlelist&comp={$item['id']}",
		],
	];

	if ($item['nhomcon'] == 1) {
		$menu['category'] = "index.php?do=categories&comp={$item['id']}";
	}
	if ($item['brand'] == 1) {
		$menu['brand'] = "index.php?do=brands&comp={$item['id']}";
	}
	if ($item['hinhmodule'] == 1 || $item['motamodule'] == 1) {
		$menu['detail'] = "index.php?do=component&act=edit&id={$item['id']}";
	}
	if ($item['kichthuoc'] == 1) {
		$menu['size'] = "index.php?do=articlelist&comp=14";
	}
	if ($item['mausac'] == 1) {
		$menu['color'] = "index.php?do=articlelist&comp=15";
	}

	$listMenuLeft[] = $menu;
}
$smarty->assign("ListMenuLeft", $listMenuLeft);

// // -----------------------------
// // 🚧 Kiểm tra trạng thái web
// // -----------------------------
// $rsweb = getInfo(13);
// if ($rsweb['open'] == 1 && (isset($_SESSION["admin_artseed_username"]) ? $_SESSION["admin_artseed_username"] : '') == 'admin') {
// 	echo "<div class='coloseweb'>
//             <img class='closeweb' 
//                  style='position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);' 
//                  src='{$config['BASE_URL']}/images/giahan.jpg' />
//           </div>";
// 	exit;
// }
$new_register = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.contact
    WHERE is_read = 0
");

$smarty->assign('new_contact_count', (int)$new_register['total']);

///
$new_register = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.register_info
    WHERE is_read = 0
");

$smarty->assign('new_register_count', (int)$new_register['total']);

///đơn hàng
// Đơn hàng
$r2 = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.orders
    WHERE is_read = 0
");
$smarty->assign('new_order_count', (int)$r2['total']);
//
$r2 = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.orders
");

$smarty->assign('total_order_count', (int)$r2['total']);

//Tổng sản phẩm
$r2 = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.articlelist
	where comp = 2
");

$smarty->assign('total_products_count', (int)$r2['total']);
//Tổng bài viết
$r2 = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.articlelist
	where comp = 1
");

$smarty->assign('total_news_count', (int)$r2['total']);
///
//Tổng bài viết
$r2 = $GLOBALS['sp']->getRow("
    SELECT COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.contact
");

$smarty->assign('total_contact_count', (int)$r2['total']);
// -----------------------------
// 📄 Xử lý router trang admin
// -----------------------------
$page = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;

$do = isset($_GET['do']) ? $_GET['do'] : 'main';

if (!isset($_SESSION["store_anthinh_login"])) {
	$do = "login";
}

$sourcePath = "./sources/{$do}.php";
if (!file_exists($sourcePath)) {
	die("Function '{$do}' not found!");
}

require $sourcePath;
