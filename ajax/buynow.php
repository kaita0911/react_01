<?php
session_start();
include_once('../includes/config.php');
include_once('../includes/get_languages.php');

// ====================================================
// ✅ 1. Kiểm tra nếu chỉ yêu cầu lấy số lượng giỏ hàng
// ====================================================
if (isset($_POST['action']) && $_POST['action'] == 'getCount') {
	$total_items = count(isset($_SESSION['cart']) ? $_SESSION['cart'] : []);
	echo json_encode(['total_items' => $total_items]);
	exit;
}

// ====================================================
// ✅ 2. Xử lý thêm sản phẩm vào giỏ hàng
// ====================================================
$id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
$quantity = max(1, intval(isset($_POST['quantity']) ? $_POST['quantity'] : 1));

if ($id <= 0) {
	echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
	exit;
}

// Lấy thông tin sản phẩm từ DB
$product = $GLOBALS['sp']->getRow("
    SELECT 
        a.id, a.img_thumb_vn AS image,
        d.name, d.unique_key,
        p.price, p.priceold
    FROM {$GLOBALS['db_sp']}.articlelist AS a
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
        ON d.articlelist_id = a.id AND d.languageid = {$langid}
    LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p
        ON p.articlelist_id = a.id
    WHERE a.id = {$id} AND a.active = 1
    LIMIT 1
");

if (!$product) {
	echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
	exit;
}

// ====================================================
// ✅ 3. Thêm sản phẩm vào session giỏ hàng
// ====================================================
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$id])) {
	$_SESSION['cart'][$id]['quantity'] += $quantity;
} else {
	$_SESSION['cart'][$id] = [
		'id'       => $product['id'],
		'name'     => $product['name'],
		'image'    => $product['image'],
		'unique_key'      => $product['unique_key'],
		'price'    => $product['price'],
		'quantity' => $quantity
	];
}

// Tổng số sản phẩm trong giỏ
$total_items = 0;
foreach ($_SESSION['cart'] as $item) {
	$total_items += $item['quantity'];
}

// ====================================================
// ✅ 4. Trả kết quả
// ====================================================
echo json_encode([
	'success' => true,
	'total_items' => $total_items,
	'product' => [
		'name'     => $product['name'],
		'price'    => number_format($product['price'], 0, ',', '.') . '₫',
		'image'    => $product['image'],
		'unique_key'      => $product['unique_key'],
		'quantity' => $quantity
	]
]);
exit;
