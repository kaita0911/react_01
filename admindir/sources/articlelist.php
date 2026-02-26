
<?php
// ============================
// Controller: articlelist.php
// ============================
require_once "functions/categories.php"; // ✅ gọi hàm buildCategoryTree()
require_once "functions/pagination.php"; // ✅ gọi phan trang
global $db_sp, $sp;

// ============================
// 🧩 Lấy act & URL hiện tại
// ============================
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = intval(isset($_GET['comp']) ? $_GET['comp'] : 0);
$page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
$id = intval(isset($_GET['id']) ? $_GET['id'] : 0);



// ============================
// 🧱 Lấy dữ liệu cơ bản
// ============================
$tinhnang = $sp->getRow("SELECT * FROM {$GLOBALS['db_sp']}.component WHERE id = {$comp}");
$smarty->assign('tinhnang', $tinhnang);
// /////Danh sách màu & KICH THUOC
if ($tinhnang['mausac'] == 1 && $tinhnang['kichthuoc'] == 1) {
    $colors = $sp->getAll("SELECT * FROM {$GLOBALS['db_sp']}.colors WHERE active =1 order by num desc");
    $smarty->assign('colors', $colors);
    $sizes = $sp->getAll("SELECT * FROM {$GLOBALS['db_sp']}.size WHERE active =1 order by num desc");
    $smarty->assign('sizes', $sizes);
}

// Lấy ID bài viết (edit)
$article_id = $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);

////get Thương hiệu
function saveArticleBrand($article_id, $brand_id)
{
    // Xóa thương hiệu cũ của bài viết (nếu có)
    $GLOBALS['sp']->execute("DELETE FROM {$GLOBALS['db_sp']}.articlelist_brands WHERE articlelist_id = ?
          AND brands_id IN (SELECT id FROM {$GLOBALS['db_sp']}.categories WHERE comp = 76)
    ", [$article_id]);

    // Nếu người dùng chọn thương hiệu mới → lưu lại
    if (!empty($brand_id)) {
        $GLOBALS['sp']->execute("INSERT INTO {$GLOBALS['db_sp']}.articlelist_brands (articlelist_id, brands_id)
            VALUES (?, ?)
        ", [$article_id, $brand_id]);
    }
}

function getBrandsForArticle($article_id)
{
    $language_id = isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : 1;

    // Lấy tất cả thương hiệu
    $brands = $GLOBALS['sp']->getAll("
        SELECT c.*, cd.name AS detail_name
        FROM {$GLOBALS['db_sp']}.categories c
        LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
            ON cd.categories_id = c.id
           AND cd.languageid = ?
        WHERE c.comp = 76
        ORDER BY c.num ASC
    ", [$language_id]);

    // Lấy thương hiệu mà bài viết đang chọn
    $selectedBrandId = $GLOBALS['sp']->getOne("
        SELECT brands_id
        FROM {$GLOBALS['db_sp']}.articlelist_brands
        WHERE articlelist_id = ?
          AND brands_id IN (
              SELECT id FROM {$GLOBALS['db_sp']}.categories WHERE comp = 76
          )
        LIMIT 1
    ", [$article_id]);

    return [
        'brands' => $brands,
        'selectedBrandId' => $selectedBrandId
    ];
}

// Lấy danh sách thương hiệu + thương hiệu hiện tại
$brandData = getBrandsForArticle($article_id);

$smarty->assign('brands', $brandData['brands']);
$smarty->assign('selectedBrandId', $brandData['selectedBrandId']);
// ============================
// 🔹 Lấy danh mục đã chọn (bao gồm cha)
// ============================
$selected = [];
if ($id) {
    // Lấy category đã lưu cho article
    $selected = $GLOBALS['sp']->getCol("
        SELECT categories_id 
        FROM {$GLOBALS['db_sp']}.articlelist_categories 
        WHERE articlelist_id = {$id}
    ");

    // Lấy quan hệ cha-con
    $relations = $GLOBALS['sp']->getAll("SELECT category_id, related_id FROM {$GLOBALS['db_sp']}.categories_related");
    $parentMap = [];
    foreach ($relations as $rel) {
        $parentMap[$rel['category_id']] = $rel['related_id'];
    }

    // Build tất cả cha của các category đã chọn
    $finalSelected = [];
    foreach ($selected as $catId) {
        $finalSelected[$catId] = $catId;
        $current = $catId;
        while (isset($parentMap[$current]) && $parentMap[$current] > 0) {
            $pid = $parentMap[$current];
            $finalSelected[$pid] = $pid;
            $current = $pid;
        }
    }
    $selected = array_values($finalSelected); // [643, 646]
    $selected = array_map('intval', $selected); // ép kiểu int
}
$smarty->assign('selected', $selected);
$categories = buildCategoryTree($comp, $id);
$smarty->assign('categories', $categories);
// 🔁 Xử lý các hành động
// ============================
switch ($act) {
    /////////Xoa nhieu anh//////////
    case 'deleteimage':
        ob_clean();
        $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
        if ($id > 0) {
            // Lấy đường dẫn file ảnh
            $row = $GLOBALS['sp']->getRow("SELECT img_vn FROM {$GLOBALS['db_sp']}.gallery_sp WHERE id=$id");
            if ($row) {
                $filePath = '../' . $row['img_vn'];
                if (file_exists($filePath)) unlink($filePath); // xóa file
                $GLOBALS['sp']->query("DELETE FROM {$GLOBALS['db_sp']}.gallery_sp WHERE id=$id"); // xóa DB
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;

    case 'edit':
        $sql_attr = "
        SELECT *
        FROM {$GLOBALS['db_sp']}.articlelist_attributes a
        INNER JOIN {$GLOBALS['db_sp']}.articlelist_codes c
          ON c.id = a.code_id
        WHERE c.articlelist_id = {$id}
          AND a.color_code <> ''
           GROUP BY a.color_code
        ORDER BY a.id ASC
      ";

        $articlelist_attributes = $GLOBALS['sp']->getAll($sql_attr);
        $smarty->assign('articlelist_attributes', $articlelist_attributes);
        // 🔹 LẤY MÃ SẢN PHẨM
        $codes = $GLOBALS['sp']->GetAll("
            SELECT *
            FROM {$GLOBALS['db_sp']}.articlelist_codes
            WHERE articlelist_id = ?
            ORDER BY sort_order ASC
            ", [$id]);

        // 🔹 LẤY MÀU + GIÁ THEO MÃ
        foreach ($codes as &$c) {
            $c['variants'] = $GLOBALS['sp']->GetAll("
                SELECT *
                FROM {$GLOBALS['db_sp']}.articlelist_attributes
                WHERE code_id = ?
                ORDER BY sort_order ASC
            ", [$c['id']]);
        }
        unset($c);

        // 🔹 TRUYỀN RA TPL
        $smarty->assign('product_codes', $codes);

        //////Lay danh sach mau va kich thuoc
        if ($tinhnang['mausac'] == 1 && $tinhnang['kichthuoc'] == 1) {
            $selectedColors = $sp->GetCol("SELECT color_id FROM {$GLOBALS['db_sp']}.articlelist_color WHERE articlelist_id={$id}");
            $smarty->assign('selected_color', $selectedColors);
            $selectedSizes = $sp->GetCol("SELECT size_id FROM {$GLOBALS['db_sp']}.articlelist_size WHERE articlelist_id={$id}");
            $smarty->assign('selected_size', $selectedSizes);
        }

        $brands = getBrandsForArticle($id);
        $smarty->assign('selectedBrandId', $brands['selectedBrandId']);
        // thuộc tính
        $rs_properties = $sp->getAll("SELECT * FROM {$GLOBALS['db_sp']}.properties_component WHERE comp_id = {$comp} ORDER BY properties_id ASC");
        $smarty->assign('namethuoctinh', $rs_properties);
        $smarty->assign('check_count_thuoctinh', count($rs_properties));

        // danh mục cha
        $cats = $sp->getAll("SELECT * FROM {$GLOBALS['db_sp']}.categories WHERE active=1 AND comp={$comp}");
        $smarty->assign('checkcatdm', count($cats));

        // Lấy danh sách hình
        $rs_multi = $sp->getAll("SELECT * FROM {$GLOBALS['db_sp']}.gallery_sp WHERE articlelist_id={$id} ORDER BY num ASC");
        $smarty->assign('multiimages', $rs_multi);
        $smarty->assign('count_multi_images', count($rs_multi));

        ///Chi tiet////
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
        $articlelist = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.articlelist WHERE id={$id}");
        $articlelistDetail = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.articlelist_detail WHERE articlelist_id = {$id}");
        $priceRow = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.articlelist_price WHERE articlelist_id={$id}");

        // Chuẩn bị tags JSON cho mỗi ngôn ngữ
        $articleDetailWithTags = [];
        foreach ($articlelistDetail as $detail) {
            $tagsArray = [];
            if (!empty($detail['keyword'])) {
                $tagsArray = array_filter(explode(',', $detail['keyword'])); // tách tag theo dấu phẩy
            }
            $detail['tagsJson'] = json_encode($tagsArray); // JSON để JS parse
            $articleDetailWithTags[$detail['languageid']] = $detail;
        }
        $articlelistDetail = $articleDetailWithTags;


        // ✅ Format giá khi ra smarty
        if ($priceRow) {
            $priceRow['price']     = number_format(isset($priceRow['price']) ? $priceRow['price'] : 0, 0, ',', '.');
            $priceRow['priceold']  = number_format(isset($priceRow['priceold']) ? $priceRow['priceold'] : 0, 0, ',', '.');
        }

        $smarty->assign([
            "articlelistDetail" => $articlelistDetail,
            "articlelist" => $articlelist,
            "articlelistPrice"   => $priceRow
        ]);


        $template = 'articlelist/edit.tpl';
        break;


    case 'add':
        $template = 'articlelist/create.tpl';
        break;

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        $page = intval(isset($_POST['page']) ? $_POST['page'] : 1);
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));

            // 🔹 Xóa ảnh đại diện bài viết
            $thumbs = $GLOBALS["sp"]->getAll("SELECT img_thumb_vn FROM {$GLOBALS['db_sp']}.articlelist WHERE id IN ($idList)");
            // foreach ($thumbs as $row) {
            //     if (empty($row['img_thumb_vn'])) continue;
            
            //     $file = $_SERVER['DOCUMENT_ROOT'] . '/' . $row['img_thumb_vn'];
            
            //     if (file_exists($file)) {
            //         if (!unlink($file)) {
            //             echo 'KHÔNG XOÁ ĐƯỢC: ' . $file;
            //         }
            //     } else {
            //         echo 'KHÔNG TỒN TẠI: ' . $file;
            //     }
            // }
            foreach ($thumbs as $row) {
                if (empty($row['img_thumb_vn'])) continue;
            
                $dbPath   = $row['img_thumb_vn'];                 // hinh-anh/san-pham/abc.jpg
                $baseDir  = $_SERVER['DOCUMENT_ROOT'] . '/' . dirname($dbPath) . '/';
                $basename = basename($dbPath);
            
                if (!is_dir($baseDir)) continue;
            
                // duyệt file trong folder đó
                foreach (glob($baseDir . '*') as $file) {
                    if (basename($file) === $basename) {
                        unlink($file);
                        break;
                    }
                }
            }
            
            // 1️⃣ xoá giá + màu (variants)
            $GLOBALS["sp"]->query("DELETE v FROM {$GLOBALS['db_sp']}.articlelist_attributes v INNER JOIN {$GLOBALS['db_sp']}.articlelist_codes c ON c.id = v.code_id  WHERE c.articlelist_id IN ($idList)");

            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_detail WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist WHERE id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_price WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_categories WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_brands WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_size WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_color WHERE articlelist_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_codes WHERE articlelist_id IN ($idList)");

            // Xóa hình ảnh liên quan
            $images = $GLOBALS["sp"]->getCol("SELECT img_vn FROM {$GLOBALS['db_sp']}.gallery_sp WHERE articlelist_id IN ($idList)");
            $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/hinh-anh/hinh-san-pham/';

            foreach ($images as $img) {
                if (empty($img)) continue;

                $basename = basename($img); // đề phòng DB có path

                foreach (glob($baseDir . '*') as $file) {
                    if (basename($file) === $basename) {
                        unlink($file);
                        break;
                    }
                }
            }
            
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.gallery_sp WHERE articlelist_id IN ($idList)");

            // ✅ Kiểm tra lại tổng số bài viết còn lại
            $total = intval($GLOBALS["sp"]->getOne("SELECT COUNT(*) FROM {$GLOBALS['db_sp']}.articlelist WHERE comp = {$comp}"));
            $per_page = 10; // số bài mỗi trang
            $total_pages = max(ceil($total / $per_page), 1);
            // Nếu trang hiện tại > tổng số trang thì giảm đi 1

            if ($page > $total_pages) {
                $page = $total_pages;
            }

            echo json_encode(['success' => true, 'new_page' => $page]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;


    case 'refreshlistajax':
        ob_clean();

        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Không có ID nào được chọn!'
            ]);
            exit;
        }

        $idList = array_map('intval', explode(',', $ids));
        $now    = date("Y-m-d H:i:s");
        $count  = 0;

        foreach ($idList as $id) {

            // ============================
            // 1️⃣ LẤY SẢN PHẨM GỐC
            // ============================
            $r = $sp->getRow("
                    SELECT *
                    FROM {$GLOBALS['db_sp']}.articlelist
                    WHERE id = {$id}
                ");
            if (!$r) continue;

            // ============================
            // 2️⃣ COPY ARTICLELIST
            // ============================
            $newArr = [
                'name_vn'    => $r['name_vn'] . ' - Copy',
                'unique_key' => $r['unique_key'] . '-' . time(),
                'comp'       => $r['comp'],
                'active'     => 1,
                'new'        => $r['new'],
                'hot'        => $r['hot'],
                'mostview'   => $r['mostview'],
                'num'        => (int)$sp->getOne("
                        SELECT MAX(num)
                        FROM {$GLOBALS['db_sp']}.articlelist
                    ") + 1,
                'dated'      => $now,
                'dated_edit' => $now,
                'code'       => $r['code']
            ];

            // 👉 copy ảnh đại diện (giữ nguyên folder gốc)
            if (!empty($r['img_thumb_vn']) && file_exists('../' . $r['img_thumb_vn'])) {

                $oldPath = '../' . $r['img_thumb_vn'];

                $info    = pathinfo($oldPath);
                $dir     = $info['dirname'];      // thư mục hiện tại của ảnh
                $ext     = $info['extension'];
                $name    = $info['filename'];

                $newFile = $name . '_' . time() . '.' . $ext;
                $newPath = $dir . '/' . $newFile;

                if (@copy($oldPath, $newPath)) {
                    // lưu path tương đối (bỏ ../)
                    $newArr['img_thumb_vn'] = ltrim(str_replace('../', '', $newPath), '/');
                }
            }

            $id_new = vaInsert('articlelist', $newArr);
            if (!$id_new) continue;

            // ============================
            // 3️⃣ COPY ARTICLELIST_DETAIL
            // ============================
            $details = $sp->getAll("
                    SELECT *
                    FROM {$GLOBALS['db_sp']}.articlelist_detail
                    WHERE articlelist_id = {$id}
                ");

            foreach ($details as $dt) {
                vaInsert('articlelist_detail', [
                    'articlelist_id' => $id_new,
                    'languageid'     => $dt['languageid'],
                    'name'           => $dt['name'] . ' - Copy',
                    'unique_key'     => $dt['unique_key'] . '-' . time(),
                    'short'          => $dt['short'],
                    'content'        => $dt['content'],
                    'keyword'        => $dt['keyword'],
                    'des'            => $dt['des']
                ]);
            }

            // ============================
            // 4️⃣ COPY GIÁ CHUNG
            // ============================
            $price = $sp->getRow("
                    SELECT *
                    FROM {$GLOBALS['db_sp']}.articlelist_price
                    WHERE articlelist_id = {$id}
                ");

            if ($price) {
                vaInsert('articlelist_price', [
                    'articlelist_id' => $id_new,
                    'price'          => $price['price'],
                    'priceold'       => $price['priceold']
                ]);
            }

            // ============================
            // 5️⃣ COPY MÃ + THUỘC TÍNH
            // ============================
            $codes = $sp->getAll("
                    SELECT *
                    FROM {$GLOBALS['db_sp']}.articlelist_codes
                    WHERE articlelist_id = {$id}
                    ORDER BY sort_order ASC
                ");

            $codeMap = [];

            foreach ($codes as $c) {

                vaInsert('articlelist_codes', [
                    'articlelist_id' => $id_new,
                    'code'           => $c['code'],
                    'sort_order'     => $c['sort_order']
                ]);

                $new_code_id = $sp->Insert_ID();
                $codeMap[$c['id']] = $new_code_id;

                $attrs = $sp->getAll("
                        SELECT *
                        FROM {$GLOBALS['db_sp']}.articlelist_attributes
                        WHERE code_id = {$c['id']}
                        ORDER BY sort_order ASC
                    ");

                foreach ($attrs as $a) {
                    vaInsert('articlelist_attributes', [
                        'code_id'    => $new_code_id,
                        'color_name' => $a['color_name'],
                        'color_code' => $a['color_code'],
                        'price'      => $a['price'],
                        'sort_order' => $a['sort_order']
                    ]);
                }
            }
            // ============================
            // 7️⃣ COPY DANH MỤC
            // ============================
            $cats = $sp->getAll("
                SELECT *
                FROM {$GLOBALS['db_sp']}.articlelist_categories
                WHERE articlelist_id = {$id}
                ");

            foreach ($cats as $cat) {
                vaInsert('articlelist_categories', [
                    'articlelist_id' => $id_new,
                    'categories_id'  => $cat['categories_id']
                ]);
            }
            // ============================
            // 6️⃣ COPY GALLERY THEO MÀU
            // ============================
            $gallery = $sp->getAll("
                    SELECT *
                    FROM {$GLOBALS['db_sp']}.gallery_sp
                    WHERE articlelist_id = {$id}
                    ORDER BY num ASC
                ");

            foreach ($gallery as $g) {

                $newImgPath = '';

                if (!empty($g['img_vn']) && file_exists('../' . $g['img_vn'])) {
                    $info    = pathinfo($g['img_vn']);
                    $newFile = $info['filename'] . '_copy_' . time() . '.' . $info['extension'];
                    $newPath = '../hinh-anh/hinh-san-pham/' . $newFile;

                    @copy('../' . $g['img_vn'], $newPath);
                    $newImgPath = str_replace('../', '', $newPath);
                }

                vaInsert('gallery_sp', [
                    'articlelist_id' => $id_new,
                    'img_vn'         => $newImgPath,
                    'color_code'     => $g['color_code'],
                    'num'            => $g['num']
                ]);
            }

            $count++;
        }

        echo json_encode([
            'success' => true,
            'message' => "Đã sao chép {$count} sản phẩm thành công!",
            'count'   => $count
        ]);
        exit;


    case 'updatenumajax':
        ob_clean();
        $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            exit;
        }

        try {
            $row = $GLOBALS['sp']->getRow("
            SELECT MAX(num) AS maxnum 
            FROM {$GLOBALS['db_sp']}.articlelist
        ");
            $maxNum = intval(isset($row['maxnum']) ? $row['maxnum'] : 0);
            $newNum = $maxNum + 1;

            $GLOBALS['sp']->execute("
            UPDATE {$GLOBALS['db_sp']}.articlelist 
            SET num = {$newNum} 
            WHERE id = {$id}
        ");

            $item = $GLOBALS['sp']->getRow("
            SELECT id, name_vn, num, active 
            FROM {$GLOBALS['db_sp']}.articlelist 
            WHERE id = {$id}
        ");

            echo json_encode([
                'success' => true,
                'newNum' => $newNum,
                'item' => $item
            ]);
        } catch (Exception $e) {
            error_log("updatenumajax error: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage()
            ]);
        }
        exit;

    case 'order':
        ob_clean(); // Xóa tất cả output trước đó
        $ids = isset($_POST['id']) ? $_POST['id'] : [];
        $ordering = isset($_POST['num']) ? $_POST['num'] : [];

        if (!empty($ids) && !empty($ordering) && count($ids) === count($ordering)) {
            $cases = '';
            $idList = [];

            for ($i = 0; $i < count($ids); $i++) {
                $idInt = intval($ids[$i]);
                $num = intval($ordering[$i]);
                $cases .= "WHEN {$idInt} THEN {$num} ";
                $idList[] = $idInt;
            }

            if (!empty($idList)) {
                $idsString = implode(',', $idList);
                $sql = "UPDATE {$GLOBALS['db_sp']}.articlelist 
                            SET num = CASE id {$cases} END 
                            WHERE id IN ({$idsString})";

                $res = $GLOBALS["sp"]->execute($sql);

                if ($res !== false) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Danh mục không hợp lệ!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu để sắp xếp!']);
        }
        exit;

    case 'addsm':
    case 'editsm':
        saveArticle();
        // 👉 Nếu là AJAX upload ảnh
        // if (!empty($_FILES['img_thumb_vn'])) {
        //     ob_clean();
        //     echo json_encode([
        //         'success' => true
        //     ]);
        //     exit;
        // }
        page_transfer2("index.php?do=articlelist&comp={$comp}");
        break;

    default:
        
        // ===== Điều kiện lọc cơ bản =====
        $where = "WHERE a.comp = {$comp}";
        $join = ""; // nếu cần JOIN bảng khác thì thêm
        $order = "GROUP BY a.id ORDER BY a.num DESC";
        //Tổng bài viết///
        $sqlTotal = "SELECT COUNT(DISTINCT a.id) FROM {$GLOBALS['db_sp']}.articlelist a $join $where";
        $totalArticles = (int)$GLOBALS['sp']->getOne($sqlTotal);
        $smarty->assign('totalArticles', $totalArticles);

        // ==== Lấy từ khóa tìm kiếm (nếu có) ====

        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $cate_id = isset($_GET['cate_id']) ? intval($_GET['cate_id']) : 0;
        $join = " LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail ad ON ad.articlelist_id = a.id";

        // ==== Lọc theo danh mục (nếu có chọn) ====
        if ($cate_id > 0) {
            $join .= " LEFT JOIN {$GLOBALS['db_sp']}.articlelist_categories ac ON ac.articlelist_id = a.id";
            $where .= " AND ac.categories_id = " . $cate_id;
        }

        if ($keyword != '') {
            $where .= " AND ad.name LIKE " . $GLOBALS['sp']->qstr('%' . $keyword . '%');
        }
        // ==== Tham số phân trang ====
        $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        $per_page = 100;

        // ==== Gọi hàm paginate ====
        $result = paginate($GLOBALS["sp"], "{$GLOBALS['db_sp']}.articlelist AS a", $join, $where, $order, $page, $per_page);

        $articles = $result['data'];
        $pagination = $result['pagination'];

        // ==== Gộp chi tiết và giá ====
        $details = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.articlelist_detail");
        $prices = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.articlelist_price");

        $articlelistDetail = [];
        foreach ($details as $d) {
            // $articlelistDetail[$d['articlelist_id']] = $d;
            $articlelistDetail[$d['articlelist_id']][$d['languageid']] = $d;
        }

        $articlelistPrice = [];
        foreach ($prices as $p) {
            $p['price'] = number_format(isset($p['price']) ? $p['price'] : 0, 0, ',', '.');
            $p['priceold'] = number_format(isset($p['priceold']) ? $p['priceold'] : 0, 0, ',', '.');
            $articlelistPrice[$p['articlelist_id']] = $p;
        }

        // Gộp detail + price vào từng bài viết trong $articles
        foreach ($articles as &$item) {
            $id = $item['id'];
            $item['details'] = isset($articlelistDetail[$id]) ? $articlelistDetail[$id] : [];
            $item['price']   = isset($articlelistPrice[$id])   ? $articlelistPrice[$id]   : [];
        }
        unset($item);

        // ==== Truyền sang Smarty ====
        $smarty->assign('articlelist', $articles);
        $smarty->assign('pagination', $pagination);
        $template = 'articlelist/list.tpl';
        break;
}

// ============================
// 🧩 Hiển thị giao diện
// ============================
$smarty->assign('tabmenu', 0);
$smarty->display('header.tpl');
$smarty->display($template);
$smarty->display('footer.tpl');

function saveArticle()
{
    global $act, $comp;
    $logoPath = __DIR__ . "/../images/logo_nhathuy.png"; // đường dẫn logo
    $sp    = $GLOBALS['sp'];
    $id  = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    $now = date("Y-m-d H:i:s");
    $brand_id = isset($_POST['brand_id']) ? $_POST['brand_id'] : '';

    // ==== 1️⃣ Xử lý num tự động ====
    $newNum = ($act === 'addsm')
        ? (($sp->getOne("SELECT MAX(num) FROM {$GLOBALS['db_sp']}.articlelist") ?: 0) + 1)
        : intval(isset($_POST['num']) ? $_POST['num'] : 0);

    // ==== 2️⃣ Lấy dữ liệu POST cơ bản ====
    $arr = [
        'new'         => !empty($_POST['new']) ? 1 : 0,
        'mostview'        => !empty($_POST['mostview']) ? 1 : 0,
        'active'    => !empty($_POST['active']) ? 1 : 0,
        'hot'         => !empty($_POST['hot']) ? 1 : 0,
        'num'         => $newNum,
        'comp'      => $comp,
        //'type'      => 'article',
        'dated_edit' => $now,
        'dated' => $now,
        'code'       => trim(isset($_POST["code"]) ? $_POST["code"] : ''),
        'link_out'       => trim(isset($_POST["link_out"]) ? $_POST["link_out"] : ''),
    ];
    // 2️⃣ Upload ảnh
    if (!empty($_FILES['img_thumb_vn']['name']) && $_FILES['img_thumb_vn']['error'] === UPLOAD_ERR_OK) {

        // 🔹 Xóa ảnh cũ nếu có (chỉ khi đang ở chế độ edit)
        if ($act === 'editsm' && !empty($id)) {
            $oldImg = $GLOBALS['sp']->getOne("SELECT img_thumb_vn FROM {$GLOBALS['db_sp']}.articlelist WHERE id = " . intval($id));
            if (!empty($oldImg) && file_exists('../' . $oldImg)) {
                @unlink('../' . $oldImg);
            }
        }

        $predix = '../';
        // 🔹 Xác định thư mục upload
        switch ($comp) {
            case 7:
                $uploadDir = $predix . 'hinh-anh/banner/';

                $uploadDir_pre = 'hinh-anh/banner/';
                break;
            case 73;
            case 75;
            case 74:
                $uploadDir = $predix . 'hinh-anh/quang-cao/';
                $uploadDir_pre = 'hinh-anh/quang-cao/';
                break;
            case 2:
                $uploadDir = $predix . 'hinh-anh/san-pham/';
                $uploadDir_pre = 'hinh-anh/san-pham/';
                break;
            case 27:
                $uploadDir = $predix . 'hinh-anh/dich-vu/';
                $uploadDir_pre = 'hinh-anh/dich-vu/';
                break;
            case 10:
                $uploadDir = $predix . 'hinh-anh/san-pham/';
                $uploadDir_pre = 'hinh-anh/san-pham/';
                break;
            case 1:
                $uploadDir = $predix . 'hinh-anh/tin-tuc/';
                $uploadDir_pre = 'hinh-anh/tin-tuc/';
                break;
            case 29:
                $uploadDir = $predix . 'hinh-anh/doi-tac/';
                $uploadDir_pre = 'hinh-anh/doi-tac/';
                break;
            default:
                $uploadDir = $predix . 'hinh-anh/thong-tin-chung/';
                $uploadDir_pre = 'hinh-anh/thong-tin-chung/';
        }

        if (
            isset($_FILES['img_thumb_vn']) &&
            $_FILES['img_thumb_vn']['error'] === 0
        ) {

            $file = $_FILES['img_thumb_vn'];
            $originalName = $file['name'];

            $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $slug = StripUnicode($nameOnly);
            $filename = $slug . '-' . time() . '.' . $ext;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadPath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $arr['img_thumb_vn'] = $uploadDir_pre . $filename;
            } else {
                die('Upload ảnh thất bại');
            }
        } else {
            die('Chưa chọn ảnh hoặc upload lỗi');
        }
    }

    if ($act === 'addsm') {
        vaInsert('articlelist', $arr);
        $id = $GLOBALS['sp']->Insert_ID(); // ✅ Lấy ID mới insert
    } else {
        vaUpdate('articlelist', $arr, "id=$id");
    }

    // ==== Cập nhật num của ảnh cũ nếu kéo thả đổi vị trí ====
    $idsOld = isset($_POST['id_old']) ? $_POST['id_old'] : []; // mảng id ảnh cũ
    $numsOld = isset($_POST['num_old']) ? $_POST['num_old'] : []; // mảng num mới từ JS

    if (!empty($idsOld) && count($idsOld) === count($numsOld)) {
        foreach ($idsOld as $index => $imgId) {
            $imgIdInt = intval($imgId);
            $num = intval($numsOld[$index]);
            $GLOBALS['sp']->query("UPDATE {$GLOBALS['db_sp']}.gallery_sp SET num = $num WHERE id = $imgIdInt");
        }
    }
    ////upload nhieu hinh ko co thuoc tinh
    if (!empty($_FILES['multiimages']['name'][0])) {
        define('UPLOAD_DIR_MULTI', '../hinh-anh/hinh-san-pham/');
        if (!is_dir(UPLOAD_DIR_MULTI)) mkdir(UPLOAD_DIR_MULTI, 0755, true);

        $maxNum = (int)$GLOBALS['sp']->getOne(
            "SELECT MAX(num) FROM {$GLOBALS['db_sp']}.gallery_sp WHERE articlelist_id = $id"
        );

        $files = $_FILES['multiimages'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt)) continue;

            // 🔹 Tạo tên file an toàn
            $originalName = pathinfo($files['name'][$i], PATHINFO_FILENAME);
            $safeName = StripUnicode($originalName);
            if ($safeName === '') $safeName = 'image';

            $fileName = $safeName . '-' . time() . '-' . rand(100, 999) . '.' . $ext;
            $uploadPath = UPLOAD_DIR_MULTI . $fileName;

            if (!move_uploaded_file($files['tmp_name'][$i], $uploadPath)) continue;

            // 👉 Nếu cần watermark thì bật dòng này
            // addLogoWatermarkOpacity($uploadPath, $uploadPath, $logoPath, 20, 0.4, 100);

            $maxNum++;
            $pathForDb = str_replace('../', '', $uploadPath);

            $GLOBALS['sp']->query("
                INSERT INTO {$GLOBALS['db_sp']}.gallery_sp (articlelist_id, img_vn, num)
                VALUES ($id, '$pathForDb', $maxNum)
            ");
        }
    }


    if (!empty($_FILES['images']['name'])) {

        define('UPLOAD_DIR_MULTI', '../hinh-anh/hinh-san-pham/');
        if (!is_dir(UPLOAD_DIR_MULTI)) {
            mkdir(UPLOAD_DIR_MULTI, 0755, true);
        }

        $maxNum = (int)$GLOBALS['sp']->getOne("
            SELECT MAX(num)
            FROM {$GLOBALS['db_sp']}.gallery_sp
            WHERE articlelist_id = $id
        ");

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // ✅ LOOP THEO MÀU
        foreach ($_FILES['images']['name'] as $colorKey => $names) {

            $color_code = '#' . $colorKey;

            // ✅ LOOP THEO ẢNH
            foreach ($names as $i => $name) {

                if ($_FILES['images']['error'][$colorKey][$i] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) continue;

                $originName = pathinfo($name, PATHINFO_FILENAME);
                $safeName = StripUnicode($originName);
                if ($safeName === '') $safeName = 'image';

                $fileName = $safeName . '-' . time() . '-' . rand(100, 999) . '.' . $ext;
                $uploadPath = UPLOAD_DIR_MULTI . $fileName;

                if (!move_uploaded_file(
                    $_FILES['images']['tmp_name'][$colorKey][$i],
                    $uploadPath
                )) {
                    continue;
                }

                $maxNum++;
                $pathForDb = str_replace('../', '', $uploadPath);

                $GLOBALS['sp']->query("
                    INSERT INTO {$GLOBALS['db_sp']}.gallery_sp
                        (articlelist_id, color_code, img_vn, num)
                    VALUES
                        ($id, '$color_code', '$pathForDb', $maxNum)
                ");
            }
        }
    }



    // Lặp qua từng ngôn ngữ để lưu
    $languages = isset($_POST['languages']) ? $_POST['languages'] : array();

    if (empty($languages)) {
        exit('Chưa có dữ liệu ngôn ngữ nào được gửi.');
    }
    foreach ($languages as $language_id => $data) {
        $name    = isset($data['name']) ? trim($data['name']) : '';
        $short   = isset($data['short']) ? trim($data['short']) : '';
        $content = isset($data['content']) ? trim($data['content']) : '';
        $des     = isset($data['des']) ? trim($data['des']) : '';
        // Lấy tags JSON cho ngôn ngữ hiện tại
        $tags = [];
        if (!empty($data['tags'])) {
            $tags = json_decode($data['tags'], true);
            if (!is_array($tags)) $tags = [];
        }

        $tags = array_map('trim', $tags); // loại khoảng trắng
        $tags = array_filter($tags);       // loại tag rỗng

        // Bỏ qua nếu không có tên
        if ($name === '') continue;

        // Tạo unique_key riêng cho từng ngôn ngữ
        $unique_key = isset($data['unique_key']) && trim($data['unique_key']) !== '' ? trim($data['unique_key']) : StripUnicode($name);

        $exists = $GLOBALS["sp"]->getOne(
            "SELECT COUNT(*) FROM {$GLOBALS['db_sp']}.articlelist_detail WHERE unique_key='{$unique_key}'"
                . ($id ? " AND articlelist_id<>$id" : '')
        );
        $unique_key_final = $exists ? $unique_key . "-$id" : $unique_key;
        // Chuẩn bị dữ liệu lưu
        $arrDetail = array(
            'articlelist_id' => $id,
            'languageid'     => $language_id,
            'name'           => $name,
            'unique_key'     => $unique_key_final,
            'short'          => $short,
            'content'        => $content,
            'content_nosign' => remove_vn($content),
            'keyword'        => implode(',', $tags),
            'des'            => $des
        );
        // Kiểm tra đã tồn tại bản ghi cho articlelist_id + languageid
        $detail = $GLOBALS["sp"]->getRow(
            "SELECT * FROM {$GLOBALS['db_sp']}.articlelist_detail WHERE articlelist_id=$id AND languageid=$language_id"
        );

        if ($detail) {
            vaUpdate('articlelist_detail', $arrDetail, "id={$detail['id']}");
        } else {
            vaInsert('articlelist_detail', $arrDetail);
        }
    }

    // ==== 6️⃣ Lưu giá vào bảng articlelist_price ====
    $price     = (int) str_replace('.', '', isset($_POST['price']) ? $_POST['price'] : 0);
    $priceold  = (int) str_replace('.', '', isset($_POST['priceold']) ? $_POST['priceold'] : 0);
    $priceRow = $sp->getRow("SELECT * FROM {$GLOBALS['db_sp']}.articlelist_price WHERE articlelist_id=$id");

    if ($priceRow) {
        // Update
        vaUpdate('articlelist_price', [
            'price' => $price,
            'priceold' => $priceold,
        ], "articlelist_id=$id");
    } else {
        // Insert
        vaInsert('articlelist_price', [
            'articlelist_id' => $id,
            'price' => $price,
            'priceold' => $priceold,
        ]);
    }

    // ============================
    // 💾 Lưu danh mục chọn (tối ưu, insert 1 query)
    // ============================
    $selectedCategories = isset($_POST['parentids']) ? $_POST['parentids'] : [];
    $categoriesToSave = [];

    if (!empty($selectedCategories)) {
        // Lấy quan hệ cha-con từ categories_related
        $relations = $GLOBALS['sp']->getAll("SELECT category_id, related_id FROM {$GLOBALS['db_sp']}.categories_related");
        $parentMap = [];
        foreach ($relations as $rel) {
            $parentMap[$rel['category_id']] = $rel['related_id']; // category_id => cha
        }

        // Hàm lấy tất cả cha của 1 category
        $getAllParents = function ($catId) use (&$parentMap) {
            $parents = [];
            $current = $catId;
            while (isset($parentMap[$current]) && $parentMap[$current] > 0) {
                $parents[$parentMap[$current]] = $parentMap[$current];
                $current = $parentMap[$current];
            }
            return $parents;
        };

        // Duyệt các category được chọn
        foreach ($selectedCategories as $catId) {
            $catId = intval($catId);
            if ($catId <= 0) continue;

            $categoriesToSave[$catId] = $catId;

            // Thêm các cha
            $parents = $getAllParents($catId);
            foreach ($parents as $pid) {
                $categoriesToSave[$pid] = $pid;
            }
        }

        $categoriesToSave = array_values($categoriesToSave); // Chuyển thành mảng số
    }

    // Lưu vào DB
    if ($id > 0) {
        // Xóa các danh mục cũ
        $GLOBALS['sp']->query("DELETE FROM {$GLOBALS['db_sp']}.articlelist_categories WHERE articlelist_id = {$id}");

        // Insert nhiều record cùng lúc
        if (!empty($categoriesToSave)) {
            $values = [];
            foreach ($categoriesToSave as $catId) {
                $catId = intval($catId);
                $values[] = "($id, $catId)";
            }

            $valuesString = implode(',', $values);
            $sql = "INSERT INTO {$GLOBALS['db_sp']}.articlelist_categories (articlelist_id, categories_id) VALUES $valuesString";
            $GLOBALS['sp']->query($sql);
        }
    }
    // Lưu brand
    saveArticleBrand($id, $brand_id);

    /////Lưu màu sắc
    $colorIds = isset($_POST['colorids']) ? $_POST['colorids'] : [];
    // Xóa toàn bộ màu cũ
    $GLOBALS['sp']->Execute("DELETE FROM {$GLOBALS['db_sp']}.articlelist_color WHERE articlelist_id = ?", [$id]);
    // Lưu lại các màu mới
    if (!empty($colorIds)) {
        foreach ($colorIds as $colorId) {
            $GLOBALS['sp']->Execute(
                "INSERT INTO {$GLOBALS['db_sp']}.articlelist_color (articlelist_id, color_id) VALUES (?, ?)",
                [$id, (int)$colorId]
            );
        }
    }
    /////Lưu kich thuoc
    $sizeIds = isset($_POST['sizeids']) ? $_POST['sizeids'] : [];
    // Xóa toàn bộ màu cũ
    $GLOBALS['sp']->Execute("DELETE FROM {$GLOBALS['db_sp']}.articlelist_size WHERE articlelist_id = ?", [$id]);
    // Lưu lại các màu mới
    if (!empty($sizeIds)) {
        foreach ($sizeIds as $sizeId) {
            $GLOBALS['sp']->Execute(
                "INSERT INTO {$GLOBALS['db_sp']}.articlelist_size (articlelist_id, size_id) VALUES (?, ?)",
                [$id, (int)$sizeId]
            );
        }
    }
    // ============================
    // 💾 LƯU MÃ SẢN PHẨM + MÀU + GIÁ
    // ============================
    $products = isset($_POST['products']) ? $_POST['products'] : array();

    $oldColors = $GLOBALS['sp']->getAll("
    SELECT a.color_name, a.color_code
    FROM {$GLOBALS['db_sp']}.articlelist_attributes a
    INNER JOIN {$GLOBALS['db_sp']}.articlelist_codes c
      ON c.id = a.code_id
    WHERE c.articlelist_id = ?
  ", [$id]);

    // 🔥 XÓA DỮ LIỆU CŨ KHI EDIT
    $GLOBALS['sp']->Execute("
    DELETE v FROM {$GLOBALS['db_sp']}.articlelist_attributes v
    INNER JOIN {$GLOBALS['db_sp']}.articlelist_codes c
        ON c.id = v.code_id
    WHERE c.articlelist_id = ?", [$id]);

    $GLOBALS['sp']->Execute(
        "DELETE FROM {$GLOBALS['db_sp']}.articlelist_codes WHERE articlelist_id = ?",
        [$id]
    );

    // 🔥 INSERT LẠI
    if (!empty($products)) {
        foreach ($products as $product) {

            $code = isset($product['code']) ? trim($product['code']) : '';
            if ($code === '') continue;
            $code_sort = isset($product['sort_order'])
                ? (int)$product['sort_order']
                : 0;
            // 1️⃣ LƯU MÃ SẢN PHẨM
            $GLOBALS['sp']->Execute(
                "INSERT INTO {$GLOBALS['db_sp']}.articlelist_codes (articlelist_id, code,sort_order)
             VALUES (?, ?, ?)",
                [$id, $code, $code_sort]
            );

            $code_id = $GLOBALS['sp']->Insert_ID();

            // 2️⃣ LƯU MÀU + GIÁ
            if (!empty($product['variants'])) {
                foreach ($product['variants'] as $variant) {
                    $sort = isset($variant['sort_order'])
                        ? (int)$variant['sort_order']
                        : 0;
                    $color_name = isset($variant['color_name']) ? trim($variant['color_name']) : '';
                    $color_code = isset($variant['color_code']) ? trim($variant['color_code']) : '';
                    $price = isset($variant['price'])
                        ? (int) str_replace('.', '', $variant['price'])
                        : 0;
                    if ($color_name === '') continue;

                    // 👉 TÌM MÀU CŨ CÙNG TÊN
                    foreach ($oldColors as $old) {
                        if (
                            mb_strtolower($old['color_name']) === mb_strtolower($color_name)
                            && $old['color_code'] !== $color_code
                        ) {

                            // 🔥 UPDATE ẢNH THEO MÀU
                            $GLOBALS['sp']->Execute("
                            UPDATE {$GLOBALS['db_sp']}.gallery_sp
                            SET color_code = ?
                            WHERE articlelist_id = ?
                            AND color_code = ?
                        ", [$color_code, $id, $old['color_code']]);
                        }
                    }


                    $GLOBALS['sp']->Execute(
                        "INSERT INTO {$GLOBALS['db_sp']}.articlelist_attributes
                     (code_id, color_name, color_code, price,sort_order)
                     VALUES (?, ?, ?, ?, ?)",
                        [$code_id, $color_name, $color_code, $price, $sort]
                    );
                }
            }
        }
    }
}
