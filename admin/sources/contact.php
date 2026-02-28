<?php
require_once "functions/pagination.php"; // ✅ gọi phan trang
// ==========================
// Contact Controller
// ==========================
$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$type = intval(isset($_REQUEST['type']) ? $_REQUEST['type'] : 0);
global $db_sp, $sp, $smarty;

switch ($act) {
	case "popup":
		$id = (int)$_GET['id'];
	 
		$edit = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.contact WHERE id = $id LIMIT 1");
	 
		    // đánh dấu đã đọc
		$GLOBALS["sp"]->execute("UPDATE {$GLOBALS['db_sp']}.contact SET is_read = 1 WHERE id = ?", array($id));

		$smarty->assign("edit", $edit);
		$smarty->display("contact/popup.tpl");
		exit;
	// ==========================
	// EDIT CONTACT
	// ==========================
	case 'edit':

		$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
		$sql = "SELECT * FROM {$db_sp}.contact WHERE id = {$id}";
		$rs = $sp->getRow($sql);

		$smarty->assign('edit', $rs);
		$template = 'contact/edit.tpl';
		break;

	// ==========================
	// DELETE MULTIPLE CONTACTS
	// ==========================
	case 'dellistajax':
		ob_clean(); // Xóa mọi thứ đã in ra trước đó
		$ids = isset($_POST['cid']) ? $_POST['cid'] : '';
		if ($ids !== '') {
			$idList = implode(',', array_map('intval', explode(',', $ids)));

			$GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.contact WHERE id IN ($idList)");

			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
		exit;

		// ==========================
		// DEFAULT: LIST CONTACTS
		// ==========================
	default:
		// ===== Điều kiện lọc cơ bản =====
		$where = "";
		$join = ""; // nếu cần JOIN bảng khác thì thêm
		$order = "ORDER BY id DESC";
		// ==== Tham số phân trang ====
		$page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
		$per_page = 20;
		// ==== Gọi hàm paginate ====
		$result = paginate($GLOBALS["sp"], "{$GLOBALS['db_sp']}.contact AS a", $join, $where, $order, $page, $per_page);
		$articles = $result['data'];
		$pagination = $result['pagination'];

		// $smarty->assign([
		// 	'link_url' => $link_url,
		// 	'view' => $rs,
		// 	'checkPer1' => checkPermision($idpem, 1) ? 'true' : 'false',
		// 	'checkPer2' => checkPermision($idpem, 2) ? 'true' : 'false',
		// 	'checkPer3' => checkPermision($idpem, 3) ? 'true' : 'false',
		// ]);
		$smarty->assign('pagination', $pagination);
		$smarty->assign('view', $articles);
		$template = 'contact/list.tpl';
		break;
}

// ==========================
// Render Smarty
// ==========================
$smarty->assign('tabmenu', 3);
$smarty->display('header.tpl');
$smarty->display($template);
$smarty->display('footer.tpl');
