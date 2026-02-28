<?php
// Bắt đầu session và set thời gian tồn tại 1 ngày
ini_set('session.gc_maxlifetime', 86400);
session_set_cookie_params(86400);
session_start();

if (isset($_POST['language'])) {
    $_SESSION['admin_lang'] = $_POST['language'];
}

// Trả về 200 OK cho AJAX
http_response_code(200);
echo 'ok';
