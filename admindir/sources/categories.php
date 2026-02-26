<?php
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = intval(isset($_GET['comp']) ? $_GET['comp'] : 0);

$tinhnang = $sp->getRow("SELECT * FROM {$GLOBALS['db_sp']}.component WHERE id = {$comp}");
$smarty->assign('tinhnang', $tinhnang);
// ==========================
// Xây cây danh mục dựa vào bảng categories_related
// ==========================
function buildCategoryTree($comp, $level = 0, $excludeId = 0, $selectedIds = [])
{
    //$language_id = isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : '1';
    // Lấy tất cả danh mục của component này
    $allCategories = $GLOBALS['sp']->getAll("
    SELECT * FROM {$GLOBALS['db_sp']}.categories 
    WHERE comp = {$comp} 
    " . ($excludeId ? "AND id <> {$excludeId}" : "") . " 
    ORDER BY num ASC");

    // Map danh mục theo id để dễ tra
    $catMap = [];
    foreach ($allCategories as $cat) {
        $catMap[$cat['id']] = $cat;
    }

    // Lấy toàn bộ quan hệ cha–con từ bảng categories_related
    $relations = $GLOBALS['sp']->getAll("
        SELECT category_id, related_id 
        FROM {$GLOBALS['db_sp']}.categories_related order by category_id
    ");

    $childrenMap = [];
    $parentMap = [];
    foreach ($relations as $rel) {
        $childrenMap[$rel['related_id']][] = $rel['category_id'];
        $parentMap[$rel['category_id']] = $rel['related_id'];
    }
    // Lấy tất cả chi tiết ngôn ngữ cho các category
    $categoryIds = array_column($allCategories, 'id');
    if ($categoryIds) {
        $idsStr = implode(',', $categoryIds);
        $detailsList = $GLOBALS['sp']->getAll("
          SELECT * FROM {$GLOBALS['db_sp']}.categories_detail 
          WHERE categories_id IN ({$idsStr})
      ");

        // Map chi tiết theo category_id và languageid
        $categoryDetails = [];
        foreach ($detailsList as $d) {
            $categoryDetails[$d['categories_id']][$d['languageid']] = $d;
        }
    } else {
        $categoryDetails = [];
    }

    // Hàm dựng cây
    $build = function ($parentIds, $level, $parent_id = 0) use (&$build, &$catMap, &$childrenMap, $categoryDetails) {
        $tree = [];
        foreach ($parentIds as $pid) {
            if (!isset($catMap[$pid])) continue;
            $cat = $catMap[$pid];

            // Lấy tất cả chi tiết ngôn ngữ
            $cat['detailsList'] = isset($categoryDetails[$pid]) ? $categoryDetails[$pid] : [];

            $cat['level'] = $level;
            $cat['parent_id'] = $parent_id;

            // Xử lý con
            if (isset($childrenMap[$pid])) {
                $cat['children'] = $build($childrenMap[$pid], $level + 1, $pid);
            } else {
                $cat['children'] = [];
            }

            $tree[] = $cat;
        }
        return $tree;
    };


    // Xác định danh mục gốc (những cái không phải là category_id trong bảng quan hệ)
    $allIds = array_column($allCategories, 'id');
    $childIds = array_column($relations, 'category_id');
    $rootIds = array_diff($allIds, $childIds);

    // Dựng cây từ danh mục gốc
    return $build($rootIds, 0, 0);
}

// ==========================
// Xử lý action
// ==========================
switch ($act) {
    case 'edit':
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
        $category = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.categories WHERE id={$id}");
        $categoryDetail = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.categories_detail WHERE categories_id={$id}");
        // Chuẩn bị tags JSON cho mỗi ngôn ngữ
        $categoryDetailWithTags = [];
        foreach ($categoryDetail as $detail) {
            $tagsArray = [];
            if (!empty($detail['keyword'])) {
                $tagsArray = array_filter(explode(',', $detail['keyword'])); // tách tag theo dấu phẩy
            }
            $detail['tagsJson'] = json_encode($tagsArray); // JSON để JS parse
            $categoryDetailWithTags[$detail['languageid']] = $detail;
        }
        $categoryDetail = $categoryDetailWithTags;

        // Lấy category liên quan trực tiếp
        $selected = [];
        $directRelated = $GLOBALS['sp']->getCol("
                SELECT related_id 
                FROM {$GLOBALS['db_sp']}.categories_related 
                WHERE category_id = {$id}
            ");

        // Build tất cả parent để checked khi edit
        $relations = $GLOBALS['sp']->getAll("SELECT category_id, related_id FROM {$GLOBALS['db_sp']}.categories_related");
        $parentMap = [];
        foreach ($relations as $rel) {
            $parentMap[$rel['category_id']] = $rel['related_id'];
        }

        $finalSelected = [];
        foreach ($directRelated as $catId) {
            $finalSelected[$catId] = $catId;
            $current = $catId;
            while (isset($parentMap[$current]) && $parentMap[$current] > 0) {
                $pid = $parentMap[$current];
                $finalSelected[$pid] = $pid;
                $current = $pid;
            }
        }

        $selected = array_values($finalSelected);
        $selected = array_map('intval', $selected);


        // 2. Dựng cây
        $categories = buildCategoryTree($comp, 0, $id);

        // 3. Assign tất cả sang Smarty
        $smarty->assign([
            "category" => $category,
            "categoryDetail" => $categoryDetail,
            "categories" => $categories,
            "selected" => $selected
        ]);
        $template = "categories/edit.tpl";
        break;

    case 'add':
        $categories = buildCategoryTree($comp);
        $smarty->assign([
            "categories" => $categories
        ]);
        $template = "categories/create.tpl";
        break;

    case 'addsm':
    case 'editsm':
        saveCategory();
        page_transfer2("index.php?do=categories&comp={$comp}");
        exit;

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));

            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.categories WHERE id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.categories_detail WHERE categories_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.categories_related WHERE category_id IN ($idList)");

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
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
                FROM {$GLOBALS['db_sp']}.categories
            ");
            $maxNum = intval(isset($row['maxnum']) ? $row['maxnum'] : 0);
            $newNum = $maxNum + 1;

            $GLOBALS['sp']->execute("
                UPDATE {$GLOBALS['db_sp']}.categories 
                SET num = {$newNum} 
                WHERE id = {$id}
            ");

            $item = $GLOBALS['sp']->getRow("
                SELECT id, num, active 
                FROM {$GLOBALS['db_sp']}.categories 
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

        //header('Content-Type: application/json');

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
                $sql = "UPDATE {$GLOBALS['db_sp']}.categories 
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

    default:
        $categories = buildCategoryTree($comp);
        $smarty->assign("categories", $categories);
        $template = "categories/list.tpl";
        break;
}

$smarty->assign([
    "comp" => $comp,
    "tabmenu" => 0
]);
$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");

// ==========================
// Hàm lưu category
// ==========================
function saveCategory()
{


    global $act, $languages;
    $language_id = isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : '1';
    $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    $comp = intval(isset($_POST['comp']) ? $_POST['comp'] : 0);
    //$name_vn = trim(isset($_POST["name"]) ? $_POST["name"] : '');

    // 1️⃣ Tính num tự động nếu thêm mới
    if ($act === 'addsm') {
        $maxNum = $GLOBALS['sp']->getOne("SELECT MAX(num) FROM categories");
        $newNum = $maxNum ? $maxNum + 1 : 1;
    } else {
        $newNum = intval(isset($_POST["num"]) ? $_POST["num"] : 0); // cập nhật nếu chỉnh sửa
    }

    $arr = [
        'link'      => trim(isset($_POST["link"]) ? $_POST["link"] : ''),
        'type'      => trim(isset($_POST['type']) ? $_POST['type'] : ''),
        'menutren'  => !empty($_POST['menutren']) ? 1 : 0,
        'menusp'    => !empty($_POST['menusp']) ? 1 : 0,
        'home'      => !empty($_POST['home']) ? 1 : 0,
        'active'    => !empty($_POST['active']) ? 1 : 0,
        'comp'      => $comp,
        'num'       => $newNum,
        //'type'      => 'article',
    ];

    // 2️⃣ Upload ảnh
    if (!empty($_FILES['img_vn']['name']) && $_FILES['img_vn']['error'] === UPLOAD_ERR_OK) {

        // 🔹 Nếu đang edit thì xóa ảnh cũ
        if ($act === 'editsm' && !empty($id)) {
            $oldImg = $GLOBALS['sp']->getOne("SELECT img_vn FROM {$GLOBALS['db_sp']}.categories WHERE id = " . intval($id));
            if (!empty($oldImg) && file_exists('../' . $oldImg)) {
                @unlink('../' . $oldImg);
            }
        }

        $file = $_FILES['img_vn'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uploadDir = "../hinh-anh/cate/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = 'trg-' . time() . rand(1000, 9999) . '.' . $ext;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // ✅ Chuyển sang WebP
            $webpPath = preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $uploadPath);
            // if (convertToWebp($uploadPath, $webpPath, 100)) {
            //     @unlink($uploadPath); // Xóa ảnh gốc (nếu muốn giữ thì bỏ dòng này)
            //     $arr['img_vn'] = str_replace('../', '', $webpPath);
            // } else {
            //     $arr['img_vn'] = str_replace('../', '', $uploadPath);
            // }
            if (!convertToWebp($uploadPath, $webpPath, 100)) {
                // Nếu convert thất bại → dùng file gốc
                $webpPath = $uploadPath;
            } else {
                @unlink($uploadPath); // xóa gốc sau convert thành công
            }
            $cleanPath = str_replace('../', '', $webpPath);
            $arr['img_vn'] = $cleanPath;
        }
    }

    // 3️⃣ Giữ ảnh cũ nếu edit và không chọn file mới
    if ($act !== 'addsm') {
        $currentImg = $GLOBALS["sp"]->getOne("SELECT img_vn FROM categories WHERE id=$id");
        if (!isset($arr['img_vn']) || $arr['img_vn'] === '') {
            $arr['img_vn'] = $currentImg;
        }
    }
    if ($act === 'addsm') {
        vaInsert('categories', $arr);
        $id = $GLOBALS['sp']->Insert_ID(); // ✅ Lấy ID mới insert
    } else {
        vaUpdate('categories', $arr, "id=$id");
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
            "SELECT COUNT(*) FROM {$GLOBALS['db_sp']}.categories_detail WHERE unique_key='{$unique_key}'"
                . ($id ? " AND categories_id<>$id" : '')
        );
        $unique_key_final = $exists ? $unique_key . "-$id" : $unique_key;
        // Chuẩn bị dữ liệu lưu
        $arrDetail = array(
            'categories_id' => $id,
            'languageid'     => $language_id,
            'name'           => $name,
            'unique_key'     => $unique_key_final,
            'short'          => $short,
            'content'        => $content,
            'keyword'        => implode(',', $tags),
            'des'            => $des
        );
        // Kiểm tra đã tồn tại bản ghi cho categories_id + languageid chưa
        $detail = $GLOBALS["sp"]->getRow(
            "SELECT * FROM {$GLOBALS['db_sp']}.categories_detail WHERE categories_id=$id AND languageid=$language_id"
        );
        if ($detail) {
            vaUpdate('categories_detail', $arrDetail, "id={$detail['id']}");
        } else {
            vaInsert('categories_detail', $arrDetail);
        }
    }

    $parentIds = isset($_POST['parentids']) ? $_POST['parentids'] : [];

    // 1️⃣ Xóa toàn bộ quan hệ cũ
    $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.categories_related WHERE category_id = $id");

    // 2️⃣ Chỉ lưu cha mới (người dùng trực tiếp chọn)
    $filteredParents = array_filter($parentIds, function ($pid) use ($id) {
        return $pid != $id;
    });

    // 3️⃣ Insert cha mới
    foreach ($filteredParents as $pid) {
        vaInsert('categories_related', [
            'category_id' => $id,
            'related_id'  => intval($pid),
            'is_parent'   => 0
        ]);
    }
}
