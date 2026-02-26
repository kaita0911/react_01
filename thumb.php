<?php
$cache_dir = __DIR__ . '/cache/';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0777, true);
}

$src    = isset($_GET['src']) ? ltrim($_GET['src'], '/') : '';
$width  = isset($_GET['width']) ? (int)$_GET['width'] : 0;
$height = isset($_GET['height']) ? (int)$_GET['height'] : 0;
$mode   = isset($_GET['mode']) ? $_GET['mode'] : 'contain';

if (!$src || (!$width && !$height)) die('Invalid parameters');

$source_path = __DIR__ . '/' . $src;
if (!file_exists($source_path)) die('Image not found');

$cache_file = $cache_dir . md5($src . $width . $height . $mode) . '.webp';
if (file_exists($cache_file)) {
    header('Content-Type: image/webp');
    readfile($cache_file);
    exit;
}

$ext = strtolower(pathinfo($source_path, PATHINFO_EXTENSION));

// ================= LOAD IMAGE =================
switch ($ext) {
    case 'jpg':
    case 'jpeg':
        $img = imagecreatefromjpeg($source_path);
        break;

    case 'png':
        $img = imagecreatefrompng($source_path);
        imagealphablending($img, false);   // 🔥 FIX QUAN TRỌNG
        imagesavealpha($img, true);
        break;

    case 'gif':
        $img = imagecreatefromgif($source_path);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        break;

    case 'webp':
        $img = imagecreatefromwebp($source_path);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        break;

    default:
        die('Unsupported image type');
}
if ($mode === 'scale') {
    imagealphablending($img, true);
    imagesavealpha($img, false);
}
$orig_w = imagesx($img);
$orig_h = imagesy($img);
$ratio  = $orig_w / $orig_h;

// ================= CALCULATE =================
switch ($mode) {
    case 'cover':
        $r = $width / $height;
        if ($ratio > $r) {
            $tmp_h = $height;
            $tmp_w = (int)($height * $ratio);
        } else {
            $tmp_w = $width;
            $tmp_h = (int)($width / $ratio);
        }
        break;

    case 'scale':
        if ($orig_w <= $width && $orig_h <= $height) {
            $tmp_w = $orig_w;
            $tmp_h = $orig_h;
        } else {
            if ($ratio >= 1) {
                $tmp_w = $width;
                $tmp_h = (int)($width / $ratio);
            } else {
                $tmp_h = $height;
                $tmp_w = (int)($height * $ratio);
            }
        }
        break;

    default: // contain
        $tmp_w = $width;
        $tmp_h = (int)($width / $ratio);
        if ($tmp_h > $height) {
            $tmp_h = $height;
            $tmp_w = (int)($height * $ratio);
        }
        break;
}

// ================= TEMP =================
$tmp = imagecreatetruecolor($tmp_w, $tmp_h);

if ($mode === 'contain' || $mode === 'scale') {
    // ✅ TMP nền trắng – KHÔNG alpha
    imagealphablending($tmp, true);
    $white = imagecolorallocate($tmp, 255, 255, 255);
    imagefill($tmp, 0, 0, $white);
} else {
    // ✅ TMP trong suốt cho contain / cover
    imagealphablending($tmp, false);
    imagesavealpha($tmp, true);
    $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
    imagefill($tmp, 0, 0, $transparent);
}

imagecopyresampled(
    $tmp,
    $img,
    0,
    0,
    0,
    0,
    $tmp_w,
    $tmp_h,
    $orig_w,
    $orig_h
);
// ================= FINAL =================
$final = imagecreatetruecolor($width, $height);

if ($mode === 'cover') {
    // cover mới cần alpha
    imagealphablending($final, false);
    imagesavealpha($final, true);
    $transparent2 = imagecolorallocatealpha($final, 0, 0, 0, 127);
    imagefill($final, 0, 0, $transparent2);
} else {
    // scale + contain → NỀN TRẮNG
    imagealphablending($final, true);
    $white = imagecolorallocate($final, 255, 255, 255);
    imagefill($final, 0, 0, $white);
}
$x = (int)(($width - $tmp_w) / 2);
$y = (int)(($height - $tmp_h) / 2);

imagecopy($final, $tmp, $x, $y, 0, 0, $tmp_w, $tmp_h);

// ================= SAVE =================
imagewebp($final, $cache_file, 90);

header('Content-Type: image/webp');
readfile($cache_file);

// ================= CLEAN =================
imagedestroy($img);
imagedestroy($tmp);
imagedestroy($final);
exit;
