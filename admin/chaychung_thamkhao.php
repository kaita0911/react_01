<?php
include_once('../includes/config.php');

echo "<h3>🚀 Bắt đầu chuyển dữ liệu danh mục + sản phẩm</h3>";
// ===== 5️⃣ LẤY DỮ LIỆU TỪ products =====
$products = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.articles where cid = 6");
if (!$products) die("❌ Không có dữ liệu trong bảng products.");

// ===== 6️⃣ IMPORT articlelist + articlelist_detail + articlelist_categories =====
foreach ($products as $p) {
    $img_thumb_vn = isset($p['img_thumb_vn']) ? $p['img_thumb_vn'] : '';
    $view = isset($p['view']) ? intval($p['view']) : 0;
    $num = isset($p['num']) ? intval($p['num']) : 0;
    $hot = isset($p['noibat']) ? intval($p['noibat']) : 1;
    $mostview = isset($p['capnhat']) ? intval($p['capnhat']) : 1;
    $active = isset($p['active']) ? intval($p['active']) : 1;
    $dated = !empty($p['dated']) ? $p['dated'] : date('Y-m-d');

    // Insert vào articlelist
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist (comp, img_thumb_vn, view, num, hot, mostview, active, dated)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ", array(27, $img_thumb_vn, $view, $num, $hot, $mostview, $active, $dated));

    $article_id = $GLOBALS['sp']->Insert_ID();

    // Insert vào articlelist_detail
    $name_vn = isset($p['name_vn']) ? $p['name_vn'] : '';
    $unique_key = !empty($p['unique_key']) ? $p['unique_key'] : 'art_' . uniqid();
    $short_vn = isset($p['short_vn']) ? $p['short_vn'] : '';
    $content_vn = isset($p['content_vn']) ? $p['content_vn'] : '';
    $keyword = isset($p['keyword']) ? $p['keyword'] : '';
    $des = isset($p['des']) ? $p['des'] : '';

    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail 
        (articlelist_id, name, unique_key, short, content, keyword, des, languageid)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ", array($article_id, $name_vn, $unique_key, $short_vn, $content_vn, $keyword, $des));
}

echo "<hr><h3>🎯 Hoàn tất chuyển toàn bộ danh mục + sản phẩm!</h3>";
