<?php
include_once('../includes/config.php');

echo "<h3>🚀 Bắt đầu chuyển dữ liệu danh mục + sản phẩm</h3>";
function StripUnicode($str)
{
    if (!$str) return '';

    $unicode = array(
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'd' => 'đ',
        'D' => 'Đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
    );

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    // Chuyển về chữ thường
    $str = strtolower($str);

    // Loại bỏ ký tự đặc biệt
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);

    // Thay khoảng trắng bằng dấu gạch ngang
    $str = preg_replace('/[\s-]+/', '-', trim($str));

    return $str;
}

// chống trùng unique_key
function make_unique_key($key)
{
    global $GLOBALS;

    $base = $key;
    $i = 1;

    while (true) {
        $exists = $GLOBALS['sp']->getRow("
            SELECT id FROM {$GLOBALS['db_sp']}.articlelist_detail 
            WHERE unique_key = ?
        ", [$key]);

        if (!$exists) break;

        $key = $base . '-' . $i;
        $i++;
    }

    return $key;
}
// ===== 1️⃣ LẤY TOÀN BỘ DỮ LIỆU CATEGORIES_OLD =====
$all = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.categories_old ORDER BY id ASC");
if (!$all) die("❌ Không có dữ liệu trong bảng categories_old.");

// ===== Hàm đệ quy lấy toàn bộ con cháu =====
function get_branch($all, $root_id, &$result)
{
    foreach ($all as $row) {
        if (intval($row['pid']) == $root_id) {
            $result[] = $row;
            get_branch($all, intval($row['id']), $result);
        }
    }
}

// ===== 2️⃣ LẤY NHÁNH BẮT ĐẦU TỪ pid = 13 =====
$target_pid = 13;
$branch = array();
foreach ($all as $cat) {
    if (intval($cat['pid']) == $target_pid) {
        $branch[] = $cat;
        get_branch($all, intval($cat['id']), $branch);
    }
}
if (empty($branch)) die("⚠️ Không có danh mục nào có pid = 13!");

// ===== 3️⃣ INSERT CATEGORIES + CATEGORIES_DETAIL =====
$map = array(); // id_cũ → id_mới

foreach ($branch as $cat) {
    $img_vn = isset($cat['img_vn']) ? $cat['img_vn'] : '';
    $num = isset($cat['num']) ? intval($cat['num']) : 0;
    $active = 1;
    $comp = 2;


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

    echo "✅ Danh mục cũ ID={$cat['id']} → mới ID={$new_id}<br>";
}

// ===== 4️⃣ INSERT QUAN HỆ CHA - CON (categories_related) =====
foreach ($branch as $cat) {
    $old_id = intval($cat['id']);
    $old_pid = intval($cat['pid']);
    if ($old_pid == 13) continue;

    if (isset($map[$old_id]) && isset($map[$old_pid])) {
        $GLOBALS['sp']->execute("
            INSERT INTO {$GLOBALS['db_sp']}.categories_related (category_id, related_id)
            VALUES (?, ?)
        ", array($map[$old_id], $map[$old_pid]));
        echo "📁 Quan hệ: con {$map[$old_id]} → cha {$map[$old_pid]}<br>";
    }
}

echo "<hr><h3>🛍️ Bắt đầu chuyển dữ liệu sản phẩm</h3>";

// ===== 5️⃣ LẤY DỮ LIỆU TỪ products =====
$products = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.products");
if (!$products) die("❌ Không có dữ liệu trong bảng products.");

// ===== 6️⃣ IMPORT articlelist + articlelist_detail + articlelist_categories =====
foreach ($products as $p) {
    $img_thumb_vn = isset($p['img_thumb_vn']) ? $p['img_thumb_vn'] : '';
    $view = isset($p['view']) ? intval($p['view']) : 0;
    $hot = isset($p['spnoibat']) ? intval($p['spnoibat']) : 1;
    $num = isset($p['num']) ? intval($p['num']) : 0;
    $active = isset($p['active']) ? intval($p['active']) : 1;
    $dated = !empty($p['dated']) ? $p['dated'] : date('Y-m-d');

    // Insert vào articlelist
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist (comp, img_thumb_vn, view,hot, num, active, dated)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ", array(2, $img_thumb_vn, $view, $hot, $num, $active, $dated));

    $article_id = $GLOBALS['sp']->Insert_ID();

    // Insert vào articlelist_detail
    $name_vn = isset($p['name_vn']) ? $p['name_vn'] : '';
    //$unique_key = !empty($p['unique_key']) ? $p['unique_key'] : 'art_' . uniqid();
    $short_vn = isset($p['short_vn']) ? $p['short_vn'] : '';
    $content_vn = isset($p['content_vn']) ? $p['content_vn'] : '';
    $keyword = isset($p['keyword']) ? $p['keyword'] : '';
    $des = isset($p['des']) ? $p['des'] : '';
    // Tạo unique_key
    $unique_key = !empty($p['unique_key'])
        ? StripUnicode($p['unique_key'])
        : StripUnicode(isset($p['name_vn']) ? $p['name_vn'] : '');

    $unique_key = make_unique_key($unique_key);
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail 
        (articlelist_id, name, unique_key, short, content, keyword, des, languageid)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ", array($article_id, $name_vn, $unique_key, $short_vn, $content_vn, $keyword, $des));
    //////////////them gallery 
    // ===== 7️⃣ IMPORT GALLERY THEO products.id =====
    $old_product_id = intval($p['id']);

    // Lấy toàn bộ hình từ bảng gallery cũ
    $gallery_rows = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.gallery WHERE cid = ?", array($old_product_id));

    if ($gallery_rows) {
        foreach ($gallery_rows as $g) {

            $img_vn  = isset($g['img_vn']) ? $g['img_vn'] : '';
            $name_vn = isset($g['name_vn']) ? $g['name_vn'] : '';
            $unique_key = isset($g['unique_key']) ? $g['unique_key'] : '';
            $num = isset($g['num']) ? intval($g['num']) : 0;
            $active = isset($g['active']) ? intval($g['active']) : 1;

            // Insert vào gallery_sp (mới)
            $GLOBALS['sp']->execute("
            INSERT INTO {$GLOBALS['db_sp']}.gallery_sp 
            (articlelist_id, img_vn, name_vn, unique_key, num, active)
            VALUES (?, ?, ?, ?, ?, ?)
        ", array($article_id, $img_vn, $name_vn, $unique_key, $num, $active));

            echo "🖼️ Đã import hình cho SP cũ ID={$old_product_id} → SP mới ID={$article_id}<br>";
        }
    } else {
        echo "⚠️ SP ID={$old_product_id} không có hình trong gallery.<br>";
    }

    // Gắn vào danh mục mới (articlelist_categories)
    $old_cid = intval($p['cid']);
    if ($old_cid > 0 && isset($map[$old_cid])) {
        $new_cid = $map[$old_cid];
        $GLOBALS['sp']->execute("
            INSERT INTO {$GLOBALS['db_sp']}.articlelist_categories (articlelist_id, categories_id)
            VALUES (?, ?)
        ", array($article_id, $new_cid));

        echo "🧩 Gắn sản phẩm ID={$p['id']} → danh mục mới ID={$new_cid}<br>";
    } else {
        echo "⚠️ Sản phẩm ID={$p['id']} không tìm thấy danh mục hợp lệ (cid={$old_cid})<br>";
    }
}

echo "<hr><h3>🎯 Hoàn tất chuyển toàn bộ danh mục + sản phẩm!</h3>";
