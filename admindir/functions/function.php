<?php

// ====================== PHÂN TRANG ======================
function pagiad($page, $num_page, $comp)
{
    $output = '';

    for ($i = 1; $i <= $num_page; $i++) {
        if ($i == $page) {
            $output .= '<li class="active"><a href="index.php?do=articlelist&comp=' . $comp . '&page=' . $i . '">' . $i . '</a></li>';
        } else {
            $output .= '<li><a href="index.php?do=articlelist&comp=' . $comp . '&page=' . $i . '">' . $i . '</a></li>';
        }
    }

    return $output;
}

function paginator($num_page, $page, $seg_size, $url)
{
    $alink = '';
    $lastpage = $num_page;
    $seg_num = ceil($num_page / $seg_size);
    $seg_cur = ceil($page / $seg_size);

    $first_page = 1;
    $back_page = $page - 1;
    $n = min($seg_cur * $seg_size, $lastpage);

    $seg_page = range(($seg_cur - 1) * $seg_size + 1, $n);

    // back buttons
    if ($seg_cur > 1) {
        $alink .= "<a href='$url&p=$first_page'>Đầu</a>";
        $alink .= "<a href='$url&p=$back_page'>&lt;&lt;</a>";
    } else {
        $alink .= "<span>Đầu</span><span>&lt;&lt;</span>";
    }

    foreach ($seg_page as $p) {
        if ($p == $page) $alink .= "<span style='color:#0066FF'>$p</span>";
        else $alink .= "<a href='$url&p=$p'>$p</a>";
    }

    // next buttons
    $next_page = $page + 1;
    $last_page = $lastpage;

    if ($seg_cur < $seg_num) {
        $alink .= "<a href='$url&p=$next_page'>&gt;&gt;</a>";
        $alink .= "<a href='$url&p=$last_page'>Cuối</a>";
    } else {
        $alink .= "<span>&gt;&gt;</span><span>Cuối</span>";
    }

    return $alink;
}

// ====================== KIỂM TRA LOGIN ======================
function CheckCountLogin()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    $r = $GLOBALS["sp"]->getRow("SELECT * FROM $GLOBALS[db_sp].banned_ip WHERE ip='$ip'");
    if ($r) {
        echo "<script>document.location.href='../index.html';</script>";
        exit;
    }

    $timeout = time() - 3600;
    $GLOBALS["sp"]->execute("DELETE FROM $GLOBALS[db_sp].banned_ip WHERE timestamp < $timeout");

    if (isset($_SESSION['counter_artseed_login']) && $_SESSION['counter_artseed_login'] > 3) {
        $GLOBALS["sp"]->execute("INSERT INTO $GLOBALS[db_sp].banned_ip(ip,timestamp) VALUES ('$ip', '" . time() . "')");
    }
}

// ====================== SQL HELPER ======================
function StripSql($data)
{
    return str_replace("\''", "''", str_replace("'", "''", $data));
}

function vaInsert($table, $arr)
{
    if (empty($arr)) return false;
    $keys = array_keys($arr);
    $values = array_map('StripSql', array_values($arr));
    $sql = "INSERT INTO $GLOBALS[db_sp].$table (`" . implode("`,`", $keys) . "`) VALUES ('" . implode("','", $values) . "');";
    $GLOBALS["sp"]->execute($sql);
    return $GLOBALS["sp"]->Insert_ID();
}

function vaUpdate($table, $arr, $where = "")
{
    if (empty($arr)) return false;
    $updates = [];
    foreach ($arr as $k => $v) $updates[] = "`$k`='" . StripSql($v) . "'";
    $sql = "UPDATE $GLOBALS[db_sp].$table SET " . implode(',', $updates);
    if ($where) $sql .= " WHERE $where";
    $GLOBALS["sp"]->execute($sql);
}

function vaDelete($table, $where)
{
    $GLOBALS["sp"]->execute("DELETE FROM $GLOBALS[db_sp].$table WHERE $where");
}


// ====================== XỬ LÝ CHUỖI ======================

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

function RenameFile($filename)
{
    $filename = str_replace(["&", ",", " - "], "", $filename);
    $filename = str_replace(" ", "-", $filename);
    return $filename;
}

function CheckUploadImg($ext)
{
    $valid = [".jpeg", ".jpg", ".bmp", ".gif", ".png", ".swf"];
    return in_array(strtolower($ext), $valid);
}

function SubStrEx($str, $length)
{
    if (strlen($str) <= $length) return $str;
    $pos = strpos($str, " ", $length);
    return $pos ? substr($str, 0, $pos) . '...' : $str;
}

// ====================== REDIRECT ======================
function page_transfer2($url)
{
    echo "<script>document.location.href='$url';</script>";
}

// ====================== QUYỀN ======================
function checkPer()
{
    return $_SESSION['group_artseed_user'] == -1;
}

function checkPermision($cid, $act)
{
    $sql = $cid ?
        "SELECT * FROM $GLOBALS[db_sp].permissions WHERE ((perm LIKE '%$act%') OR (perm LIKE '%4%')) AND cid=$cid AND uid=" . $_SESSION["admin_artseed_id"] :
        "SELECT * FROM $GLOBALS[db_sp].permissions WHERE ((perm LIKE '%$act%') OR (perm LIKE '%4%')) AND uid=" . $_SESSION["admin_artseed_id"];
    return ceil(count($GLOBALS["sp"]->getAll($sql))) > 0 || $_SESSION['group_artseed_user'] == -1;
}

function page_permision()
{
    echo "<script>alert('Bạn không có quyền, vui lòng liên hệ người quản trị.');</script>";
}


function convertToWebp($source, $destination, $quality = 85)
{
    // Kiểm tra xem có phải ảnh hợp lệ không
    if (!file_exists($source)) return false;

    $info = getimagesize($source);
    if (!$info) return false;

    $mime = $info['mime'];

    // Nếu là PNG trong suốt thì bỏ qua
    if ($mime == 'image/png') {
        $im = imagecreatefrompng($source);
        if (!$im) return false;

        // Kiểm tra có alpha không (trong suốt)
        $hasAlpha = false;
        $width = imagesx($im);
        $height = imagesy($im);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgba = imagecolorat($im, $x, $y);
                $alpha = ($rgba & 0x7F000000) >> 24; // 0 = opaque, 127 = full transparent
                if ($alpha > 0) {
                    $hasAlpha = true;
                    break 2;
                }
            }
        }

        imagedestroy($im);

        // Nếu có alpha → không convert
        if ($hasAlpha) {
            return false;
        }
    }

    // Tiếp tục convert nếu không có alpha
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }

    $result = imagewebp($image, $destination, $quality);
    imagedestroy($image);
    return $result;
}

function addTextWatermark($src, $dest, $text = "WATERMARK")
{

    $font = __DIR__ . "/../fonts/Roboto.ttf"; // tuyệt đối theo vị trí file function.php
    if (!file_exists($font)) return false;

    $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));

    // Mở ảnh đúng định dạng
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($src);
            break;
        case 'png':
            $image = imagecreatefrompng($src);
            break;
        case 'webp':
            $image = imagecreatefromwebp($src);
            break;
        default:
            return false;
    }
    if (!$image) return false;

    $color = imagecolorallocatealpha($image, 255, 255, 255, 90); // chữ trắng mờ
    $fontSize = 30;
    $angle = 0;

    $imgW = imagesx($image);
    $imgH = imagesy($image);
    $textBox = imagettfbbox($fontSize, $angle, $font, $text);
    if (!$textBox) return false;

    $textW = abs($textBox[2] - $textBox[0]);
    $textH = abs($textBox[7] - $textBox[1]);

    // 📌 Tính tọa độ để nằm giữa ảnh
    $x = ($imgW - $textW) / 2;
    $y = ($imgH + $textH) / 2;
    imagettftext($image, $fontSize, $angle, $x, $y, $color, $font, $text);

    // Lưu lại
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($image, $dest, 90);
            break;
        case 'png':
            imagepng($image, $dest);
            break;
        case 'webp':
            imagewebp($image, $dest, 90);
            break;
    }

    imagedestroy($image);
    return true;
}


// function addLogoWatermark($src, $dest, $logoPath)
// {
//     if (!file_exists($src) || !file_exists($logoPath)) return false;

//     $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
//     switch ($ext) {
//         case 'jpg':
//         case 'jpeg':
//             $image = imagecreatefromjpeg($src);
//             break;
//         case 'png':
//             $image = imagecreatefrompng($src);
//             break;
//         case 'webp':
//             $image = imagecreatefromwebp($src);
//             break;
//         default:
//             return false;
//     }

//     imagealphablending($image, true);
//     imagesavealpha($image, true);

//     $logo = imagecreatefrompng($logoPath);
//     imagealphablending($logo, true);
//     imagesavealpha($logo, true);

//     $imgW = imagesx($image);
//     $imgH = imagesy($image);
//     $logoW = imagesx($logo);
//     $logoH = imagesy($logo);

//     // Vị trí logo ở giữa
//     $x = ($imgW - $logoW) / 2;
//     $y = ($imgH - $logoH) / 2;

//     imagecopy($image, $logo, $x, $y, 0, 0, $logoW, $logoH);

//     // Lưu ảnh
//     switch ($ext) {
//         case 'jpg':
//         case 'jpeg':
//             imagejpeg($image, $dest, 90);
//             break;
//         case 'png':
//             imagepng($image, $dest);
//             break;
//         case 'webp':
//             imagewebp($image, $dest, 90);
//             break;
//     }

//     imagedestroy($image);
//     imagedestroy($logo);
//     return true;
// }
// function addLogoWatermarkOpacity($src, $dest, $logoPath, $margin = 20, $scale = 0.2, $opacity = 50)
// {
//     if (!file_exists($src) || !file_exists($logoPath)) return false;

//     $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
//     if (!in_array($ext, ['jpg', 'jpeg', 'png'])) return false;

//     // 1️⃣ Mở ảnh gốc
//     $image = ($ext === 'png') ? imagecreatefrompng($src) : imagecreatefromjpeg($src);
//     imagesavealpha($image, true);
//     imagealphablending($image, true);

//     // 2️⃣ Mở logo PNG
//     $logo = imagecreatefrompng($logoPath);
//     $logoW = imagesx($logo);
//     $logoH = imagesy($logo);

//     // 3️⃣ Resize logo
//     $imgW = imagesx($image);
//     $imgH = imagesy($image);
//     $newLogoW = (int) $imgW * $scale;
//     $newLogoH = (int) ($newLogoW * ($logoH / $logoW));

//     $logoResized = imagecreatetruecolor($newLogoW, $newLogoH);
//     imagesavealpha($logoResized, true);
//     imagealphablending($logoResized, false);

//     // Fill transparent
//     $transparent = imagecolorallocatealpha($logoResized, 0, 0, 0, 127);
//     imagefill($logoResized, 0, 0, $transparent);

//     // Copy logo gốc vào logo resized
//     imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, $newLogoW, $newLogoH, $logoW, $logoH);

//     // 4️⃣ Tạo overlay mờ
//     $opacityPercent = $opacity / 100;
//     for ($x = 0; $x < $newLogoW; $x++) {
//         for ($y = 0; $y < $newLogoH; $y++) {
//             $rgba = imagecolorat($logoResized, $x, $y);
//             $a = ($rgba >> 24) & 0xFF; // Alpha gốc
//             $newA = 127 - (127 - $a) * $opacityPercent; // áp opacity
//             $rgb = imagecolorsforindex($logoResized, $rgba);
//             $color = imagecolorallocatealpha($logoResized, $rgb['red'], $rgb['green'], $rgb['blue'], $newA);
//             imagesetpixel($logoResized, $x, $y, $color);
//         }
//     }

//     // 5️⃣ Góc phải dưới
//     $x = $imgW - $newLogoW - $margin;
//     $y = $imgH - $newLogoH - $margin;

//     // 6️⃣ Copy logo resized vào ảnh gốc (giữ alpha)
//     imagecopy($image, $logoResized, $x, $y, 0, 0, $newLogoW, $newLogoH);

//     // 7️⃣ Lưu ảnh
//     if ($ext === 'png') imagepng($image, $dest);
//     else imagejpeg($image, $dest, 90);

//     // 8️⃣ Giải phóng bộ nhớ
//     imagedestroy($image);
//     imagedestroy($logo);
//     imagedestroy($logoResized);

//     return true;
// }

function vn_to_slug($str)
{
    if (!$str) return '';

    // Đưa về chữ thường
    $str = mb_strtolower($str, 'UTF-8');

    // Bỏ dấu tiếng Việt
    $search = [
        // a
        'à',
        'á',
        'ạ',
        'ả',
        'ã',
        'â',
        'ầ',
        'ấ',
        'ậ',
        'ẩ',
        'ẫ',
        'ă',
        'ằ',
        'ắ',
        'ặ',
        'ẳ',
        'ẵ',
        // e
        'è',
        'é',
        'ẹ',
        'ẻ',
        'ẽ',
        'ê',
        'ề',
        'ế',
        'ệ',
        'ể',
        'ễ',
        // i
        'ì',
        'í',
        'ị',
        'ỉ',
        'ĩ',
        // o
        'ò',
        'ó',
        'ọ',
        'ỏ',
        'õ',
        'ô',
        'ồ',
        'ố',
        'ộ',
        'ổ',
        'ỗ',
        'ơ',
        'ờ',
        'ớ',
        'ợ',
        'ở',
        'ỡ',
        // u
        'ù',
        'ú',
        'ụ',
        'ủ',
        'ũ',
        'ư',
        'ừ',
        'ứ',
        'ự',
        'ử',
        'ữ',
        // y
        'ỳ',
        'ý',
        'ỵ',
        'ỷ',
        'ỹ',
        // d
        'đ'
    ];

    $replace = [
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        'i',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'y',
        'y',
        'y',
        'y',
        'y',
        'd'
    ];

    $str = str_replace($search, $replace, $str);

    // Loại bỏ ký tự đặc biệt, chỉ giữ chữ + số + space
    $str = preg_replace('/[^a-z0-9\s]/', '', $str);

    // Chuẩn hóa khoảng trắng
    $str = preg_replace('/\s+/', ' ', trim($str));

    return $str;
}

function remove_vn($str)
{
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    $str = strip_tags($str);

    $str = mb_strtolower($str, 'UTF-8');
    $search = [
        'à',
        'á',
        'ạ',
        'ả',
        'ã',
        'â',
        'ầ',
        'ấ',
        'ậ',
        'ẩ',
        'ẫ',
        'ă',
        'ằ',
        'ắ',
        'ặ',
        'ẳ',
        'ẵ',
        'è',
        'é',
        'ẹ',
        'ẻ',
        'ẽ',
        'ê',
        'ề',
        'ế',
        'ệ',
        'ể',
        'ễ',
        'ì',
        'í',
        'ị',
        'ỉ',
        'ĩ',
        'ò',
        'ó',
        'ọ',
        'ỏ',
        'õ',
        'ô',
        'ồ',
        'ố',
        'ộ',
        'ổ',
        'ỗ',
        'ơ',
        'ờ',
        'ớ',
        'ợ',
        'ở',
        'ỡ',
        'ù',
        'ú',
        'ụ',
        'ủ',
        'ũ',
        'ư',
        'ừ',
        'ứ',
        'ự',
        'ử',
        'ữ',
        'ỳ',
        'ý',
        'ỵ',
        'ỷ',
        'ỹ',
        'đ'
    ];
    $replace = [
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'a',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'e',
        'i',
        'i',
        'i',
        'i',
        'i',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'o',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'u',
        'y',
        'y',
        'y',
        'y',
        'y',
        'd'
    ];
    return str_replace($search, $replace, $str);
}
