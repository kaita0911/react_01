<?php

function getCategoryChain($categoryId, $langid)
{
    $chain = [];
    while ($categoryId) {
        $category = $GLOBALS['sp']->getRow("
            SELECT c.id, d.name, d.unique_key, cr.related_id
            FROM {$GLOBALS['db_sp']}.categories AS c
            LEFT JOIN {$GLOBALS['db_sp']}.categories_detail AS d
                ON d.categories_id = c.id AND d.languageid = {$langid}
            LEFT JOIN {$GLOBALS['db_sp']}.categories_related AS cr
                ON cr.category_id = c.id
            WHERE c.id = {$categoryId}
            LIMIT 1
        ");

        if (!$category) break;

        array_unshift($chain, [
            'id' => $category['id'],
            'name' => $category['name'],
            'unique_key' => $category['unique_key']
        ]);

        if (!empty($category['related_id'])) {
            $categoryId = $category['related_id'];
        } else {
            break;
        }
    }
    return $chain;
}

function buildBreadcrumb($langid, $path_url, $cat1 = '', $unique_key = '')
{
    // --- 1️⃣ Home ---
    if ($langid == 1) {
        $home_name = 'Trang chủ';
    } elseif ($langid == 2) {
        $home_name = 'Home';
    } elseif ($langid == 3) {
        $home_name = 'ホーム';
    } else {
        $home_name = 'Home';
    }

    $breadcrumbs = array(
        array('name' => $home_name, 'link' => $path_url)
    );

    $menu = null;

    // --- 2️⃣ Lấy menu nếu $cat1 có ---
    if (!empty($cat1)) {
        $menu = $GLOBALS['sp']->getRow("
            SELECT m.id, m.comp, IFNULL(d.name, m.comp) AS name, d.unique_key
            FROM {$GLOBALS['db_sp']}.menu AS m
            LEFT JOIN {$GLOBALS['db_sp']}.menu_detail AS d
                ON d.menu_id = m.id AND d.languageid = {$langid}
            WHERE d.unique_key = '{$cat1}'
            LIMIT 1
        ");

        if ($menu && !empty($menu['name'])) {
            $breadcrumbs[] = array('name' => $menu['name'], 'link' => "{$path_url}/{$menu['unique_key']}/");
        }
    }

    // --- 3️⃣ Chi tiết bài viết ---
    if (!empty($unique_key)) {
        $article = $GLOBALS['sp']->getRow("
            SELECT a.id, a.comp, d.name AS article_name
            FROM {$GLOBALS['db_sp']}.articlelist AS a
            LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
                ON d.articlelist_id = a.id AND d.languageid = {$langid}
            WHERE d.unique_key = '{$unique_key}'
            LIMIT 1
        ");

        if ($article) {
            // 3a. Nếu menu chưa có → thử lấy menu theo comp bài viết
            if (!$menu) {
                $menuArticle = $GLOBALS['sp']->getRow("
                    SELECT m.id, IFNULL(d.name, m.comp) AS name, d.unique_key
                    FROM {$GLOBALS['db_sp']}.menu AS m
                    LEFT JOIN {$GLOBALS['db_sp']}.menu_detail AS d
                        ON d.menu_id = m.id AND d.languageid = {$langid}
                    WHERE m.comp = '{$article['comp']}'
                    LIMIT 1
                ");
                if ($menuArticle && !empty($menuArticle['name'])) {
                    $breadcrumbs[] = array('name' => $menuArticle['name'], 'link' => "{$path_url}/{$menuArticle['unique_key']}/");
                    $menu = $menuArticle;
                }
            }

            // 3b. Thêm category chain
            $article_categories = $GLOBALS['sp']->getAll("
                SELECT categories_id
                FROM {$GLOBALS['db_sp']}.articlelist_categories
                WHERE articlelist_id = {$article['id']}
            ");

            if ($article_categories) {
                foreach ($article_categories as $ac) {
                    $chain = getCategoryChain($ac['categories_id'], $langid);
                    foreach ($chain as $c) {
                        $exists = false;
                        foreach ($breadcrumbs as $b) {
                            if ($b['name'] === $c['name']) {
                                $exists = true;
                                break;
                            }
                        }
                        if (!$exists) {
                            $breadcrumbs[] = array(
                                'name' => $c['name'],
                                'link' => "{$path_url}/{$c['unique_key']}/"
                            );
                        }
                    }
                }
            }

            // 3c. Thêm bài viết cuối cùng
            $breadcrumbs[] = array('name' => $article['article_name'], 'link' => '');
        }
    }
    // --- 4️⃣ Nếu chỉ có cat1 nhưng chưa thêm category ---
    elseif (!empty($cat1) && !$menu) {
        $category = $GLOBALS['sp']->getRow("
            SELECT c.id
            FROM {$GLOBALS['db_sp']}.categories AS c
            LEFT JOIN {$GLOBALS['db_sp']}.categories_detail AS d
                ON d.categories_id = c.id AND d.languageid = {$langid}
            WHERE d.unique_key = '{$cat1}'
            LIMIT 1
        ");
        if ($category) {
            $chain = getCategoryChain($category['id'], $langid);
            foreach ($chain as $c) {
                $breadcrumbs[] = array(
                    'name' => $c['name'],
                    'link' => "{$path_url}/{$c['unique_key']}"
                );
            }
        }
    }

    return $breadcrumbs;
}
// function buildBreadcrumb($langid, $path_url, $cat1 = '')
// {
//     // --- 1️⃣ HOME ---
//     $home_name = ($langid == 1 ? 'Trang chủ' : ($langid == 2 ? 'Home' : 'ホーム'));

//     $breadcrumbs = [];
//     $breadcrumbs[] = ['name' => $home_name, 'link' => $path_url];

//     $category = null;
//     $menu = null;

//     // --- 2️⃣ ƯU TIÊN MAPPING UNIQUE_KEY VỚI CATEGORY ---
//     if (!empty($cat1)) {
//         $category = $GLOBALS['sp']->getRow("
//             SELECT c.id, d.name, d.unique_key
//             FROM {$GLOBALS['db_sp']}.categories AS c
//             LEFT JOIN {$GLOBALS['db_sp']}.categories_detail AS d
//                 ON d.categories_id = c.id AND d.languageid = {$langid}
//             WHERE d.unique_key = '{$cat1}'
//             LIMIT 1
//         ");

//         if ($category) {
//             // Lấy chain cha → con
//             $chain = getCategoryChain($category['id'], $langid);

//             foreach ($chain as $c) {
//                 $breadcrumbs[] = [
//                     'name' => $c['name'],
//                     'link' => "{$path_url}/{$c['unique_key']}"
//                 ];
//             }
//         }
//     }

//     // --- 3️⃣ Nếu KHÔNG có category → thử tìm MENU ---
//     if (!$category && !empty($cat1)) {
//         $menu = $GLOBALS['sp']->getRow("
//             SELECT m.id, IFNULL(d.name, m.comp) AS name, d.unique_key
//             FROM {$GLOBALS['db_sp']}.menu AS m
//             LEFT JOIN {$GLOBALS['db_sp']}.menu_detail AS d
//                 ON d.menu_id = m.id AND d.languageid = {$langid}
//             WHERE d.unique_key = '{$cat1}'
//             LIMIT 1
//         ");

//         if ($menu) {
//             $breadcrumbs[] = [
//                 'name' => $menu['name'],
//                 'link' => "{$path_url}/{$menu['unique_key']}"
//             ];
//         } else {
//             // Lấy bài viết
//             $article = $GLOBALS['sp']->getRow("
//         SELECT a.id, a.comp, d.name AS article_name
//         FROM {$GLOBALS['db_sp']}.articlelist AS a
//         LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
//             ON d.articlelist_id = a.id AND d.languageid = {$langid}
//         WHERE d.unique_key = '{$cat1}'
//         LIMIT 1
//     ");

//             if ($article) {

//                 // --- 4a. Nếu chưa có menu → tìm menu theo comp bài viết ---
//                 if (!$menu) {
//                     $menu = $GLOBALS['sp']->getRow("
//                 SELECT m.id, IFNULL(d.name, m.comp) AS name, d.unique_key
//                 FROM {$GLOBALS['db_sp']}.menu AS m
//                 LEFT JOIN {$GLOBALS['db_sp']}.menu_detail AS d
//                     ON d.menu_id = m.id AND d.languageid = {$langid}
//                 WHERE m.comp = '{$article['comp']}'
//                 LIMIT 1
//             ");

//                     if ($menu) {
//                         $breadcrumbs[] = [
//                             'name' => $menu['name'],
//                             'link' => "{$path_url}/{$menu['unique_key']}"
//                         ];
//                     }
//                 }

//                 // --- 4b. Lấy tất cả categories của bài viết ---
//                 $article_categories = $GLOBALS['sp']->getAll("
//             SELECT categories_id
//             FROM {$GLOBALS['db_sp']}.articlelist_categories
//             WHERE articlelist_id = {$article['id']}
//         ");

//                 if ($article_categories) {
//                     foreach ($article_categories as $ac) {
//                         $chain = getCategoryChain($ac['categories_id'], $langid);

//                         foreach ($chain as $c) {
//                             // Không cho trùng
//                             $exists = false;
//                             foreach ($breadcrumbs as $b) {
//                                 if ($b['name'] === $c['name']) {
//                                     $exists = true;
//                                     break;
//                                 }
//                             }
//                             if (!$exists) {
//                                 $breadcrumbs[] = [
//                                     'name' => $c['name'],
//                                     'link' => "{$path_url}/{$c['unique_key']}"
//                                 ];
//                             }
//                         }
//                     }
//                 }

//                 // --- 4c. Add bài viết ---
//                 $breadcrumbs[] = ['name' => $article['article_name'], 'link' => ''];
//             }
//         }
//     }

//     return $breadcrumbs;
// }

//////////
function renderPagination($currentPage, $totalPages, $baseUrl = null)
{
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $querySort = $sort ? '&sort=' . urlencode($sort) : '';

    if ($totalPages <= 1) return '';

    if ($baseUrl === null) {
        // Lấy URL hiện tại mà không có query string
        $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');
    }

    $html = '<ul class="pagination">';
    // === Nút Trước ===
    if ($currentPage > 1) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage - 1) . $querySort . '">&laquo;</a></li>';
    } else {
        $html .= '<li class="disabled"><span>&laquo;</span></li>';
    }

    // === Các số trang ===
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="active"><span>' . $i . '</span></li>';
        } else {
            $html .= '<li><a href="' . $baseUrl . '?page=' . $i . $querySort . '">' . $i . '</a></li>';
        }
    }

    // === Nút Tiếp ===
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . $baseUrl . '?page=' . ($currentPage + 1) . $querySort . '">&raquo;</a></li>';
    } else {
        $html .= '<li class="disabled"><span>&raquo;</span></li>';
    }

    $html .= '</ul>';
    return $html;
}

// Các file CSS gốc
$css_files = array(
    __DIR__ . '/../assets/css/style.css',
    __DIR__ . '/../assets/css/home.css',
    __DIR__ . '/../assets/css/about.css',
    __DIR__ . '/../assets/css/cart.css',
    __DIR__ . '/../assets/css/contact.css',
    __DIR__ . '/../assets/css/products.css',
    __DIR__ . '/../assets/css/project.css',
    __DIR__ . '/../assets/css/service.css',
    __DIR__ . '/../assets/css/articles.css',
    __DIR__ . '/../assets/css/recruiment.css',
    __DIR__ . '/../assets/css/slick.css'
);

// File output
$output_file = __DIR__ . '/../assets/css/bundle.min.css';
// Tạo bundle.min.css nếu chưa tồn tại hoặc file CSS cập nhật
$regenerate = !file_exists($output_file);
if (!$regenerate) {
    $output_mtime = filemtime($output_file);
    foreach ($css_files as $file) {
        if (file_exists($file) && filemtime($file) > $output_mtime) {
            $regenerate = true;
            break;
        }
    }
}

// Gom tất cả CSS
if ($regenerate) {
    $all_css = '';
    foreach ($css_files as $file) {
        if (file_exists($file)) {
            $all_css .= file_get_contents($file);
        }
    }

    // Minify chuẩn
    $all_css = preg_replace('!/\*.*?\*/!s', '', $all_css);
    $all_css = preg_replace('/\s*([\{\}:;,])\s*/', '$1', $all_css);
    $all_css = str_replace(array("\n", "\r", "\t"), '', $all_css);
    $all_css = preg_replace('/\s+}/', '}', $all_css);
    $all_css = preg_replace('/\s+/', ' ', $all_css);

    file_put_contents($output_file, trim($all_css));
}


// Đọc nội dung CSS và mã hoá Base64
$css_content = file_get_contents($output_file);
$encoded = base64_encode($css_content);

// In ra JS inject CSS
echo "<script>
document.head.insertAdjacentHTML(
    'beforeend',
    '<style>' + atob(\"$encoded\") + '</style>'
);
</script>";
////toc content////
if (!function_exists('generate_toc')) {
    function generate_toc($content)
    {
        $toc = array();

        // Tìm tất cả thẻ h2, h3 trong nội dung
        if (preg_match_all('/<h([2-3])[^>]*>(.*?)<\/h[2-3]>/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $level = $match[1];
                //$title = strip_tags($match[2]);
                $title = html_entity_decode(strip_tags($match[2]), ENT_QUOTES, 'UTF-8');
                // Tạo id thân thiện (vd: "Thi công trần" -> "thi-cong-tran")
                //$id = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
                $id = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
                if (preg_match('/^[0-9]/', $id)) {
                    $id = 'h-' . $id; // thêm tiền tố nếu bắt đầu bằng số
                }
                // Gắn id vào nội dung
                $content = str_replace($match[0], '<h' . $level . ' id="' . $id . '">' . $match[2] . '</h' . $level . '>', $content);

                // Thêm vào danh sách mục lục
                $toc[] = array(
                    'level' => $level,
                    'title' => $title,
                    'id'    => $id
                );
            }
        }

        // Trả về: nội dung đã thêm id + danh sách mục lục
        return array($content, $toc);
    }
}

///tag tu khoa
function removeVietnameseTones($str)
{
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", "y", $str);
    $str = preg_replace("/(đ)/u", "d", $str);
    $str = preg_replace("/[^a-zA-Z0-9\s]/", "", $str); // loại ký tự đặc biệt
    $str = preg_replace("/\s+/", "-", trim($str)); // thay khoảng trắng bằng dấu -
    return strtolower($str);
}

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
