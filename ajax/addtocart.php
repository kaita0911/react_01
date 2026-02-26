<?php
session_start();
include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
// ====================================================
// ✅ 1. Kiểm tra nếu chỉ yêu cầu lấy số lượng giỏ hàng
// ====================================================
if (isset($_POST['action']) && $_POST['action'] == 'getCount') {
  $total_items = 0;
  if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
      if (!empty($item['checked'])) { // chỉ tính item được check
        $total_items++; // cộng quantity của item
      }
    }
  }
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

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$color_ids = isset($_POST['colorids']) ? $_POST['colorids'] : [];
$size_ids = isset($_POST['sizeids']) ? $_POST['sizeids'] : [];
if (count($color_ids) === 0) $color_ids = ['0'];
if (count($size_ids) === 0)  $size_ids  = ['0'];


// Lấy tất cả màu trong 1 query
$colorNames = [];
if (!empty($color_ids)) {
  $ids = implode(',', array_map('intval', $color_ids));
  $res = $GLOBALS['sp']->getAll("SELECT id, name FROM {$GLOBALS['db_sp']}.colors WHERE id IN ($ids)");
  foreach ($res as $row) {
    $colorNames[$row['id']] = $row['name'];
  }
}

// Lấy tất cả size trong 1 query
$sizeNames = [];
if (!empty($size_ids)) {
  $ids = implode(',', array_map('intval', $size_ids));
  $res = $GLOBALS['sp']->getAll("SELECT id, name FROM {$GLOBALS['db_sp']}.size WHERE id IN ($ids)");
  foreach ($res as $row) {
    $sizeNames[$row['id']] = $row['name'];
  }
}

// // Nếu không chọn màu hoặc size → không thêm vào giỏ hàng
// if (empty($color_ids) || empty($size_ids)) {
//   echo json_encode([
//     'success' => false,
//     'message' => 'Vui lòng chọn đầy đủ màu sắc và kích thước.'
//   ]);
//   exit;
// }
foreach ($color_ids as $color_id) {
  foreach ($size_ids as $size_id) {
    $color_id = trim($color_id);
    $size_id  = trim($size_id);
    // Tạo key duy nhất cho mỗi tổ hợp
    //$key = $id . '_' . ($color_id ?? '0') . '_' . ($size_id ?? '0');
    $key = $id . '_' . (isset($color_id) ? $color_id : '0') . '_' . (isset($size_id) ? $size_id : '0');
    // Lấy tên màu
    $color_name = isset($colorNames[$color_id]) ? $colorNames[$color_id] : null;
    $size_name  = isset($sizeNames[$size_id]) ? $sizeNames[$size_id] : null;
    if (isset($_SESSION['cart'][$key])) {
      $_SESSION['cart'][$key]['quantity'] += $quantity;
    } else {
      $_SESSION['cart'][$key] = [
        'id'        => $product['id'],
        'name'      => $product['name'],
        'image'     => $product['image'],
        'unique_key' => $product['unique_key'],
        'price'     => $product['price'],
        'priceold'     => $product['priceold'],
        'quantity'  => $quantity,
        'color_name'  => $color_name,
        'size_name'   => $size_name,
        'key'        => $key, // <-- lưu luôn key để tiện JS/Smarty
        'checked' => true,
      ];
    }
  }
}

// Tổng số sản phẩm trong giỏ
$total_items = 0;
foreach ($_SESSION['cart'] as $item) {
  if (!empty($item['checked'])) { // chỉ đếm item được check
    $total_items++;
  }
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
    'priceold'    => number_format($product['priceold'], 0, ',', '.') . '₫',
    'image'    => $product['image'],
    'unique_key'      => $product['unique_key'],
    'quantity' => $quantity,
    'color_name' => $color_name,
    'size_name' => $size_name,
    'key' => $key,
  ]
]);
exit;
