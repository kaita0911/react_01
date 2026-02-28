<?php
global $sp, $db_sp;

$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$page = max(1, intval(isset($_GET['page']) ? $_GET['page'] : 1));

// =========================
// Lấy thông tin chung
// =========================
$info = $sp->getRow("SELECT * FROM {$db_sp}.infos WHERE id = 21");
$smarty->assign("infos14", $info);

// =========================
// Xử lý các hành động
// =========================
switch ($act) {
	case "popup":

		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if ($id <= 0) {
		   exit('Không có dữ liệu');
		}
	 
		// Lấy đơn hàng
		$order = $sp->getRow(
		   "SELECT * FROM {$db_sp}.orders WHERE id = ?",
		   [$id]
		);
	 
		if (!$order) {
		   exit('Đơn hàng không tồn tại');
		}
	 
		// Lấy chi tiết sản phẩm trong đơn
		$order_lines = $sp->getAll(
		   "SELECT * FROM {$db_sp}.orders_line WHERE order_id = ?",
		   [$id]
		);
	 
		// Đánh dấu đã đọc
		$sp->execute(
		   "UPDATE {$db_sp}.orders SET is_read = 1 WHERE id = ?",
		   [$id]
		);
	 
		// Assign cho Smarty
		$smarty->assign("order", $order);
		$smarty->assign("order_line_view", $order_lines);
	 
		// ❗ CHỈ LOAD NỘI DUNG POPUP
		$smarty->display("orders/popup.tpl");
		exit;
	 
	case "edit":
		$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
		// khi xem đơn hàng
		$sp->execute("
			UPDATE {$GLOBALS['db_sp']}.orders
			SET is_read = 1
			WHERE id = ?", [$id]);
		if ($id > 0) {
			$order = $sp->getRow("SELECT * FROM {$db_sp}.orders WHERE id = ?", [$id]);
			$order_lines = $sp->getAll("SELECT * FROM {$db_sp}.orders_line WHERE order_id = ?", [$id]);
			$smarty->assign("edit", $order);
			$smarty->assign("order_line_view", $order_lines);
		}
		$template = "orders/edit.tpl";
		break;
	case "ajax_update_status":
		if (ob_get_length()) ob_clean();
		$orderId = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$status = isset($_POST['status']) ? trim($_POST['status']) : '';
		if ($orderId && $status) {
			// ADODB: dùng Execute
			$sql = "UPDATE {$db_sp}.orders SET status=? WHERE id=?";
			$res = $GLOBALS["sp"]->Execute($sql, [$status, $orderId]);

			if ($res !== false) {
				echo 'ok';
			} else {
				echo 'error';
			}
			exit;
		}
		echo 'error';
		exit;
	case "dellistajax":
		ob_clean(); // Xóa output trước đó
		$ids = isset($_POST['cid']) ? $_POST['cid'] : '';
		if ($ids) {
			$idList = array_map('intval', explode(',', $ids));
			$idStr = implode(',', $idList);

			// Xóa chi tiết trước, rồi xóa đơn hàng
			$sp->query("DELETE FROM {$db_sp}.orders_line WHERE order_id IN ($idStr)");
			$sp->query("DELETE FROM {$db_sp}.orders WHERE id IN ($idStr)");

			echo json_encode(['success' => true]);
		} else {
			echo json_encode(['success' => false]);
		}
		exit;

	default:
		// =========================
		// Danh sách đơn hàng + phân trang
		// =========================
		$num_rows_page = 100;

		// Lấy tổng số bản ghi
		$total = $sp->getOne("SELECT COUNT(*) FROM {$db_sp}.orders");

		$num_page = max(1, ceil($total / $num_rows_page));
		$begin = ($page - 1) * $num_rows_page;

		$orders = $sp->getAll("SELECT * FROM {$db_sp}.orders ORDER BY id DESC LIMIT $begin, $num_rows_page");

		// Pagination
		$url = "index.php?do=orders";
		$iSEGSIZE = 10;
		$link_url = $num_page > 1 ? paginator($num_page, $page, $iSEGSIZE, $url) : '';
		// Danh sách bước trạng thái
		$steps = ['Chờ duyệt', 'Xác nhận', 'Đang giao', 'Hoàn thành'];

		// Thêm steps và currentIndex vào mỗi đơn hàng
		foreach ($orders as &$order) {
			$order['steps'] = $steps;
			// Mặc định trạng thái nếu rỗng
			if (empty($order['status'])) {
				$order['status'] = 'Chờ duyệt';
			}

			$order['currentIndex'] = array_search($order['status'], $steps);
			if ($order['currentIndex'] === false) $order['currentIndex'] = 0;
		}
		$smarty->assign([
			"view"     => $orders,
			"link_url" => $link_url,
			"number"   => $num_rows_page * ($page - 1)
		]);

		$template = "orders/list.tpl";
		break;
}

// =========================
// Render template
// =========================

$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");
