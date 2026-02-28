<?php
require_once "functions/pagination.php";

/* ❌ Chống cache admin (PHP 5.6 OK) */
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : "";

switch ($act) {
	case "popup":
		$id = (int)$_GET['id'];
	 
		$edit = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.register_info WHERE id = $id LIMIT 1");
	 
		    // đánh dấu đã đọc
		$GLOBALS["sp"]->execute("UPDATE {$GLOBALS['db_sp']}.register_info SET is_read = 1 WHERE id = ?", array($id));

		$smarty->assign("edit", $edit);
		$smarty->display("register_info/register_popup.tpl");
		exit;
	 
	/* ==========================
       MARK READ + REDIRECT
    ========================== */
	case "edit":

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		if ($id > 0) {
			$GLOBALS["sp"]->execute("
                UPDATE {$GLOBALS['db_sp']}.register_info
                SET is_read = 1
                WHERE id = ?
            ", array($id));
		}

		header("Location: index.php?do=register_info&act=view&id=" . $id);
		exit;


		/* ==========================
       VIEW CHI TIẾT
    ========================== */
	case "view":

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		$rs = $GLOBALS["sp"]->getRow("
            SELECT *
            FROM {$GLOBALS['db_sp']}.register_info
            WHERE id = $id
        ");

		$smarty->assign("edit", $rs);
		$template = "register_info/edit.tpl";
		break;


	/* ==========================
       DELETE AJAX
    ========================== */
	case "dellistajax":

		ob_clean();
		$ids = isset($_POST['cid']) ? $_POST['cid'] : '';

		if ($ids != '') {
			$idArr = explode(',', $ids);
			$idArr = array_map('intval', $idArr);
			$idList = implode(',', $idArr);

			$GLOBALS["sp"]->query("
                DELETE FROM {$GLOBALS['db_sp']}.register_info
                WHERE id IN ($idList)
            ");

			echo json_encode(array('success' => true));
		} else {
			echo json_encode(array('success' => false));
		}
		exit;


		/* ==========================
       LIST
    ========================== */
	default:

		$order = "ORDER BY a.id DESC";

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		if ($page < 1) $page = 1;

		$per_page = 30;

		$result = paginate(
			$GLOBALS["sp"],
			"{$GLOBALS['db_sp']}.register_info as a",
			"",
			"",
			$order,
			$page,
			$per_page
		);

		$smarty->assign('articlelist', $result['data']);
		$smarty->assign('pagination', $result['pagination']);
		$template = "register_info/list.tpl";
		break;
}

$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");
