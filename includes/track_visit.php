<?php
// === Lấy thông tin truy cập ===
$ip          = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$user_agent  = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$ref         = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host        = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
$url         = $scheme . '://' . $host . $request_uri;
$time        = date('Y-m-d H:i:s');

// Chuẩn hóa URL (cắt bớt nếu quá dài)
$url = mb_substr($url, 0, 255);

// === Phát hiện local (nhưng KHÔNG loại trừ) ===
$is_local = (strpos($ip, '127.') === 0 || strpos($ip, '192.168.') === 0 || strpos($host, '.local') !== false) ? 1 : 0;

// === Bỏ qua admin page ===
if (strpos($request_uri, '/admindir/') !== false) {
    return;
}

// === Tra cứu vùng theo IP ===
// Dùng ip-api.com miễn phí
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}"), true);

$country = isset($geo['country']) ? $geo['country'] : 'Unknown';
$region  = isset($geo['regionName']) ? $geo['regionName'] : 'Unknown';
$city    = isset($geo['city']) ? $geo['city'] : 'Unknown';

// Chỉ quan tâm Việt Nam
if ($country !== 'Vietnam') {
    $region = 'Khác';
}
// === Ghi log chi tiết (đếm số lượt truy cập từng link) ===
// Kiểm tra xem cùng IP có vừa vào cùng link trong vòng 10s không (tránh spam F5)
// === Ghi vào visit_logs ===
$GLOBALS['sp']->execute(
    "INSERT INTO {$GLOBALS['db_sp']}.visit_logs 
    (ip, url, visit_time, referrer, user_agent, country, region, city)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
    [$ip, $url, $time, $ref, $user_agent, $country, $region, $city]
);
// === Ghi / cập nhật bảng visits (mỗi IP chỉ 1 dòng) ===
$visit = $GLOBALS['sp']->getRow("SELECT id FROM {$GLOBALS['db_sp']}.visits WHERE ip = ?", [$ip]);

// if ($visit) {
//     // 👉 IP đã có → chỉ cập nhật lại thông tin
//     $GLOBALS['sp']->execute(
//         "UPDATE {$GLOBALS['db_sp']}.visits
//          SET last_active = ?, user_agent = ?, url = ?, referrer = ?, is_local = ?
//          WHERE ip = ?",
//         [$time, $user_agent, $url, $ref, $is_local, $ip]
//     );
// } else {
//     // 👉 IP chưa có → thêm mới
//     $GLOBALS['sp']->execute(
//         "INSERT INTO {$GLOBALS['db_sp']}.visits
//          (ip, user_agent, visit_time, last_active, url, referrer, is_local)
//          VALUES (?, ?, ?, ?, ?, ?, ?)",
//         [$ip, $user_agent, $time, $time, $url, $ref, $is_local]
//     );
// }

if ($visit) {
    $GLOBALS['sp']->execute(
        "UPDATE {$GLOBALS['db_sp']}.visits
         SET last_active = ?, user_agent = ?, url = ?, referrer = ?, is_local = ?, country = ?, region = ?, city = ?
         WHERE ip = ?",
        [$time, $user_agent, $url, $ref, $is_local, $country, $region, $city, $ip]
    );
} else {
    $GLOBALS['sp']->execute(
        "INSERT INTO {$GLOBALS['db_sp']}.visits
         (ip, user_agent, visit_time, last_active, url, referrer, is_local, country, region, city)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [$ip, $user_agent, $time, $time, $url, $ref, $is_local, $country, $region, $city]
    );
}
