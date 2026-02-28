<?php
include_once('../includes/config.php');

/**
 * Chuyển Unicode sang không dấu và chuẩn slug
 */
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

    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', trim($str));

    return $str;
}

/**
 * Tạo unique_key tránh trùng
 */
function make_unique_key($key)
{
    $base = $key;
    $i = 1;

    while (true) {
        $exists = $GLOBALS['sp']->getRow("
            SELECT id FROM {$GLOBALS['db_sp']}.articlelist_detail 
            WHERE unique_key = ?
        ", array($key));

        if (!$exists) break;

        $key = $base . '-' . $i;
        $i++;
    }

    return $key;
}

// Lấy dữ liệu gốc từ bảng articles
$products = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.articles WHERE cid=6 order by id desc");

if (!$products) {
    die("Không có dữ liệu trong bảng articles.");
}

foreach ($products as $p) {

    // 1️⃣ Insert vào articlelist
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist 
        (comp, new, hot, img_thumb_vn, view, num, active, dated)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ", array(
        27,
        isset($p['capnhat']) ? $p['capnhat'] : 1,
        isset($p['noibat']) ? $p['noibat'] : 1,
        isset($p['img_thumb_vn']) ? $p['img_thumb_vn'] : '',
        isset($p['view']) ? $p['view'] : 0,
        isset($p['num']) ? $p['num'] : 0,
        isset($p['active']) ? $p['active'] : 1,
        isset($p['dated']) ? $p['dated'] : date('Y-m-d H:i:s'),
    ));

    $articlelist_id = $GLOBALS['sp']->Insert_ID();

    // 2️⃣ Tạo unique_key
    $unique_key = isset($p['unique_key']) && $p['unique_key'] != ''
        ? StripUnicode($p['unique_key'])
        : StripUnicode(isset($p['name_vn']) ? $p['name_vn'] : '');

    $unique_key = make_unique_key($unique_key);

    // 3️⃣ Insert vào articlelist_detail
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail 
        (articlelist_id, name, unique_key, short, content, keyword, des, languageid)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ", array(
        $articlelist_id,
        isset($p['name_vn']) ? $p['name_vn'] : '',
        $unique_key,
        isset($p['short_vn']) ? $p['short_vn'] : '',
        isset($p['content_vn']) ? $p['content_vn'] : '',
        isset($p['keyword']) ? $p['keyword'] : '',
        isset($p['des']) ? $p['des'] : '',
    ));
}

echo "✅ Chuyển dữ liệu từ articles sang articlelist & articlelist_detail thành công!";
