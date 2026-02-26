<?php
require_once(__DIR__ . "/../includes/email_config.php");
require_once(__DIR__ . "/../libraries/phpmailer/class.phpmailer.php");
require_once(__DIR__ . "/../libraries/phpmailer/class.smtp.php");

/**
 * Hàm khởi tạo PHPMailer cấu hình sẵn
 */
function createMailer()
{
    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    $mail->Host       = SMTP_SERVER;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USER;
    $mail->Password   = MAIL_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->SetFrom(MAIL_FROM, MAIL_FROMNAME);
    $mail->IsHTML(true);
    return $mail;
}

/**
 * Gửi email (dùng chung cho order và contact)
 */
function sendEmail($subject, $body, $email, $adminEmail, $attachment = null)
{
    $mail = createMailer();

    // Lấy email admin từ DB
    $get_email  = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 6");
    $adminEmail = isset($get_email['plain_text_vn']) ? $get_email['plain_text_vn'] : '';

    if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Admin email không hợp lệ!");
        return false;
    }

    $mail->Subject = $subject;
    $mail->Body    = $body;
    // 🟦 Thêm file đính kèm nếu có
    if ($attachment && file_exists($attachment)) {
        $mail->addAttachment($attachment, basename($attachment));
    }
    // Gửi cho admin
    $mail->ClearAllRecipients();
    $mail->AddAddress($adminEmail, 'Admin');
    if (!$mail->Send()) {
        error_log("Mailer admin error: " . $mail->ErrorInfo);
    }

    // Gửi khách hàng (nếu có)
    if (!empty($email)) {
        $mail->clearAllRecipients();
        $mail->addAddress($email);
        if (!$mail->Send()) {
            error_log("Mailer customer error: " . $mail->ErrorInfo);
        }
    }
    return true;
}

/**
 * Gửi email thông báo đơn hàng
 */
function sendOrderEmails($orderData, $path_url)
{
    $get_email  = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 6");
    $adminEmail = isset($get_email['plain_text_vn']) ? $get_email['plain_text_vn'] : '';

    $get_domain = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 2");
    $domain     = isset($get_domain['domain']) ? $get_domain['domain'] : '';

    $orderId       = isset($orderData['id']) ? $orderData['id'] : '';
    $customerName  = isset($orderData['customer_name']) ? $orderData['customer_name'] : '';
    $phone         = isset($orderData['phone']) ? $orderData['phone'] : '';
    $email         = isset($orderData['email']) ? $orderData['email'] : '';
    $address       = isset($orderData['address']) ? $orderData['address'] : '';
    $wards         = isset($orderData['wards']) ? $orderData['wards'] : '';
    $district      = isset($orderData['district']) ? $orderData['district'] : '';
    $city          = isset($orderData['city']) ? $orderData['city'] : '';
    $content       = isset($orderData['content']) ? $orderData['content'] : '';
    $payment       = isset($orderData['payment']) ? $orderData['payment'] : '';
    $shipped       = isset($orderData['shipped']) ? $orderData['shipped'] : '';
    $total         = isset($orderData['total']) ? $orderData['total'] : 0;
    $cart          = isset($orderData['cart']) ? $orderData['cart'] : [];

    // Danh sách sản phẩm
    $productListHtml = '<ul style="list-style:none;padding:0;">';
    foreach ($cart as $item) {
        $price      = isset($item['price']) ? $item['price'] : 0;
        $qty  = isset($item['qty']) ? $item['qty'] : 1;
        $name = htmlspecialchars(isset($item['title']) ? $item['title'] : '');
        //$imageUrl   = htmlspecialchars($path_url . '/' . ltrim(isset($item['image']) ? $item['image'] : '', '/'));
        $itemTotal  = $price * $qty;
        $imagePath = isset($item['image']) ? $item['image'] : '';
        if (is_array($imagePath)) {
            $imagePath = $imagePath[0]; // lấy ảnh đầu tiên nếu là mảng
        }
        $imagePath = ltrim($imagePath, '/');
        // Nếu là WebP → chuyển sang JPG tạm
        if (preg_match('/\.webp$/i', $imagePath)) {
            $jpgPath = preg_replace('/\.webp$/i', '.jpg', $imagePath);
            $jpgFull = __DIR__ . '/../' . $jpgPath;
            $webpFull = __DIR__ . '/../' . $imagePath;
            if (file_exists($webpFull) && !file_exists($jpgFull)) {
                $im = imagecreatefromwebp($webpFull);
                if ($im) {
                    imagejpeg($im, $jpgFull, 90);
                    imagedestroy($im);
                }
            }
            $imagePath = $jpgPath; // dùng bản JPG
        } // Link ảnh cho email
        $imageUrl = $path_url . '/' . $imagePath;

        $productListHtml .= "
            <li style='margin-bottom:8px'>
                <img src='{$imageUrl}' alt='{$name}' width='50' style='vertical-align:middle;margin-right:8px;'/>
                {$name} - {$qty} x " . number_format($price, 0, ',', '.') . "₫ 
                = <b>" . number_format($itemTotal, 0, ',', '.') . "₫</b>
            </li>";
    }
    $productListHtml .= '</ul>';
    $b_email = $email ? "<p><b>Email:</b> {$email}</p>" : '';
    // <p><b>Giao hàng:</b> {$shipped}</p>
    $body = "
        <p><b>Mã đơn:</b> {$orderId}</p>
        <p><b>Khách hàng:</b> {$customerName}</p>
        <p><b>Điện thoại:</b> {$phone}</p>
        <p><b>Địa chỉ:</b> {$address}, {$wards}, {$district}, {$city}</p>
        {$b_email}
        <p><b>Ghi chú:</b> {$content}</p>
        <p><b>Thanh toán:</b> {$payment}</p>
        <p><b>Tổng tiền:</b> <b>" . number_format($total, 0, ',', '.') . "₫</b></p>
        <h3>Chi tiết sản phẩm:</h3>
        {$productListHtml}
    ";

    return sendEmail("Đơn hàng mới từ {$domain}", $body, $email, $adminEmail);
}

/**
 * Gửi email thông báo liên hệ
 */
function sendContactEmail($contactData)
{
    $get_email  = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 6");
    $adminEmail = isset($get_email['plain_text_vn']) ? $get_email['plain_text_vn'] : '';

    //$get_domain = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = 2");
    //$domain     = isset($get_domain['domain']) ? $get_domain['domain'] : '';

    $name    = htmlspecialchars(isset($contactData['name']) ? $contactData['name'] : '');
    $phone   = htmlspecialchars(isset($contactData['phone']) ? $contactData['phone'] : '');
    $email   = htmlspecialchars(isset($contactData['email']) ? $contactData['email'] : '');
    $address = htmlspecialchars(isset($contactData['address']) ? $contactData['address'] : '');
    $message = nl2br(htmlspecialchars(isset($contactData['message']) ? $contactData['message'] : ''));
    // File đính kèm (hiển thị tên trong email)
    $filePath = isset($contactData['file']) ? $contactData['file'] : '';
    $fileName = $filePath ? basename($filePath) : '';
    $fileupload = $fileName ? "<p><b>File đính kèm:</b> {$fileName}</p>" : '';

    $body = "
        <p><b>Họ tên:</b> {$name}</p>
        <p><b>Điện thoại:</b> {$phone}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Địa chỉ:</b> {$address}</p>
        <p><b>Nội dung:</b>{$message}</p>
        {$fileupload}
    ";

    return sendEmail("Khách hàng đăng ký tư vấn", $body, $email, $adminEmail);
}
