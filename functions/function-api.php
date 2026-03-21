<?php

if (!function_exists('generate_toc')) {

    function generate_toc($content)
    {
        $toc = [];
        $usedIds = [];

        // Tìm tất cả h2, h3
        if (preg_match_all('/<h([2-3])([^>]*)>(.*?)<\/h[2-3]>/iu', $content, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $match) {

                $level = $match[1];
                $attrs = $match[2];
                $inner = $match[3];

                // Tiêu đề sạch
                $title = html_entity_decode(strip_tags($inner), ENT_QUOTES, 'UTF-8');

                // ===== tạo slug =====
                $id = removeVietnameseTones($title);

                if ($id === '') {
                    $id = 'heading';
                }

                // Nếu bắt đầu bằng số
                if (preg_match('/^[0-9]/', $id)) {
                    $id = 'h-' . $id;
                }

                // ===== tránh trùng id =====
                $baseId = $id;
                $i = 1;

                while (in_array($id, $usedIds)) {
                    $id = $baseId . '-' . $i;
                    $i++;
                }

                $usedIds[] = $id;

                // ===== thay heading =====
                $newHeading = "<h{$level} id=\"{$id}\"{$attrs}>{$inner}</h{$level}>";

                $content = str_replace($match[0], $newHeading, $content);

                // ===== thêm vào TOC =====
                $toc[] = [
                    "level" => (int)$level,
                    "title" => $title,
                    "id"    => $id
                ];
            }
        }

        return [$content, $toc];
    }
}
function removeVietnameseTones($str)
{
    $str = mb_strtolower($str, 'UTF-8');

    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", "y", $str);
    $str = preg_replace("/(đ)/u", "d", $str);

    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/\s+/', '-', trim($str));

    return $str;
}
function slugify($str)
{
    if (!$str) {
        return '';
    }

    // lowercase
    $str = mb_strtolower($str, 'UTF-8');

    // bỏ dấu
    $search = [/* giữ nguyên như bạn */];
    $replace = [/* giữ nguyên như bạn */];

    $str = str_replace($search, $replace, $str);

    // chỉ giữ a-z 0-9 và space
    $str = preg_replace('/[^a-z0-9\s]/', '', $str);

    // chuẩn hóa khoảng trắng
    $str = preg_replace('/\s+/', ' ', trim($str));

    // 🔥 THÊM DÒNG NÀY
    $str = str_replace(' ', '-', $str);

    return $str;
}
