<?php
ob_start(); // Bắt mọi output rác
header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/email_config.php");

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . "/../libraries/phpmailer/class.phpmailer.php");
require_once(__DIR__ . "/../libraries/phpmailer/class.smtp.php");

//header('Content-Type: application/json; charset=utf-8');

$get_email = $sp->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 6");
$adminEmail = $get_email['plain_text_vn'];

$get_domain = $sp->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 2");
$domain = $get_domain['domain'];

$title = trim(isset($_POST['title']) ? $_POST['title'] : '');
$fullname = trim(isset($_POST['fullname']) ? $_POST['fullname'] : '');
$email    = trim(isset($_POST['email']) ? $_POST['email'] : '');
$phone    = trim(isset($_POST['phone']) ? $_POST['phone'] : '');
//$address  = trim(isset($_POST['address']) ? $_POST['address'] : '');
$message  = trim(isset($_POST['message']) ? $_POST['message'] : '');

if ($fullname === '' || $email === '' || $phone === '') {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc!']);
    exit;
}

// === Lưu vào database ===
try {
    $GLOBALS['sp']->execute("
        INSERT INTO {$GLOBALS['db_sp']}.register_info (title, fullname, email, phone, message, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ", [$title, $fullname, $email, $phone, $message]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Không thể lưu dữ liệu: ' . $e->getMessage()]);
    exit;
}

// === GỬI MAIL ===
try {
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host       = SMTP_SERVER;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->isHTML(true);
    $mail->setFrom(MAIL_FROM, $domain);

    // =========================
    // 1️⃣ Gửi cho ADMIN
    // =========================
    //$adminEmail = isset($emails['admin']) ? $emails['admin'] : MAIL_FROM;
    $mail->clearAllRecipients();

    $mail->addAddress($adminEmail, 'Admin');
    $mail->Subject = "📩 Khách hàng đăng ký tư vấn";
    $b_title = $title ? "<p><b>Bài viết:</b> {$title}</p>" : '';
    $bodyAdmin = "
        <h3>Thông tin đăng ký</h3>
        {$b_title}
        <p><b>Họ tên:</b> {$fullname}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Điện thoại:</b> {$phone}</p>
        <p><b>Nội dung:</b> {$message}</p>
        <p><i>Gửi lúc:</i> " . date("d/m/Y H:i") . "</p>
    ";
    $mail->Body = $bodyAdmin;
    $mail->send();

    // =========================
    // 2️⃣ Gửi xác nhận cho KHÁCH HÀNG
    // =========================
    $mail->clearAllRecipients();
    $mail->addAddress($email, $fullname);
    $mail->Subject = "✅ Cảm ơn bạn đã đăng ký thông tin tại {$domain}";
    $mail->Body = "
        <p>Xin chào <b>{$fullname}</b>,</p>
        <p>Cảm ơn Quý khách đã gửi thông tin cho chúng tôi!</p>
        <p>Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.</p>
        <hr>
        <p><i>Thông tin bạn đã gửi:</i></p>
        <ul>
            <li><b>Email:</b> {$email}</li>
            <li><b>Điện thoại:</b> {$phone}</li>
            <li><b>Nội dung:</b> {$message}</li>
        </ul>
        <p>Trân trọng!</p>
    ";
    $mail->send();
} catch (Exception $e) {
    error_log("Mail error: " . $mail->ErrorInfo);
    // Không dừng script — vì đã lưu DB ok
}
ob_clean(); // Xóa mọi warning/HTML trước đó
echo json_encode([
    'success' => true,
    'message' => 'Đăng ký thành công! Cảm ơn quy khách đã đăng ký. Chúng tôi sẽ liên lạc trong thời gian sớm nhất'
]);
exit;
