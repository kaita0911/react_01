<?php
include_once('../includes/config.php');

// ===== 1️⃣ Lấy toàn bộ dữ liệu gốc =====
$all = $GLOBALS['sp']->getAll("SELECT * FROM categories_old ORDER BY id ASC");
if (!$all) {
    die("❌ Không có dữ liệu trong bảng categories_old.");
}

// ===== 2️⃣ Hàm đệ quy lấy toàn bộ con cháu của 1 danh mục =====
function get_branch($all, $root_id, &$result)
{
    foreach ($all as $row) {
        if (intval($row['pid']) == $root_id) {
            $result[] = $row;
            get_branch($all, intval($row['id']), $result);
        }
    }
}

// ===== 3️⃣ Lấy tất cả nhánh bắt đầu từ các danh mục có pid = 13 =====
$target_pid = 13;
$branch = array();

foreach ($all as $cat) {
    if (intval($cat['pid']) == $target_pid) {
        // thêm chính danh mục này
        $branch[] = $cat;
        // thêm toàn bộ con cháu của nó
        get_branch($all, intval($cat['id']), $branch);
    }
}

if (empty($branch)) {
    die("⚠️ Không có danh mục nào có pid = 13!");
}

// ===== 4️⃣ Insert dữ liệu =====
$map = array(); // id_cũ -> id_mới

foreach ($branch as $cat) {
    $img_vn = isset($cat['img_vn']) ? $cat['img_vn'] : '';
    $num = isset($cat['num']) ? intval($cat['num']) : 0;
    $active = 1;
    $comp = 2; // comp=2 như bạn yêu cầu

    // Insert vào bảng categories
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.categories (img_vn, num, active, comp)
        VALUES (?, ?, ?, ?)
    ", array($img_vn, $num, $active, $comp));

    $new_id = $GLOBALS['sp']->Insert_ID();
    $map[intval($cat['id'])] = $new_id;

    // Insert vào categories_detail
    $name_vn    = isset($cat['name_vn']) ? $cat['name_vn'] : '';
    $unique_key = !empty($cat['unique_key']) ? $cat['unique_key'] : 'cat_' . uniqid();
    $short_vn   = isset($cat['short_vn']) ? $cat['short_vn'] : '';
    $content_vn = isset($cat['content_vn']) ? $cat['content_vn'] : '';
    $keyword_vn = isset($cat['keyword_vn']) ? $cat['keyword_vn'] : '';
    $des_vn     = isset($cat['des_vn']) ? $cat['des_vn'] : '';

    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.categories_detail
        (categories_id, name, unique_key, short, content, keyword, des, languageid)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ", array($new_id, $name_vn, $unique_key, $short_vn, $content_vn, $keyword_vn, $des_vn));

    echo "✅ Đã thêm danh mục cũ ID={$cat['id']} → mới ID={$new_id}<br>";
}

// ===== 5️⃣ Tạo quan hệ cha-con trong categories_related =====
foreach ($branch as $cat) {
    $old_id = intval($cat['id']);
    $old_pid = intval($cat['pid']);

    // Bỏ qua những cái có pid = 13 (vì giờ coi nó là gốc)
    if ($old_pid == 13) continue;

    if ($old_pid > 0 && isset($map[$old_id]) && isset($map[$old_pid])) {
        $new_child  = $map[$old_id];
        $new_parent = $map[$old_pid];
        $GLOBALS['sp']->execute("
            INSERT INTO {$GLOBALS['db_sp']}.categories_related (category_id, related_id)
            VALUES (?, ?)
        ", array($new_child, $new_parent));

        echo "📁 Quan hệ: con {$new_child} → cha {$new_parent}<br>";
    }
}

echo "<br><br>🎯 Hoàn tất: đã bỏ mục 'Dự án' (id=13), coi các mục pid=13 là cấp cha gốc!";
