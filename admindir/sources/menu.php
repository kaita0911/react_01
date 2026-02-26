<?php
// ==========================
// MENU CONTROLLER (Smarty 4.3+ Clean Version)
// ==========================

$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = intval(isset($_GET['comp']) ? $_GET['comp'] : 0);

// Lấy danh sách component (liên kết)
$sql_menu = "SELECT * FROM {$GLOBALS['db_sp']}.component_detail ORDER BY id ASC";
$lienket = $GLOBALS["sp"]->getAll($sql_menu);

// $sql_menu = "
//     SELECT c.*, cd.name AS name,
//     FROM {$GLOBALS['db_sp']}.component AS c
//     LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS cd 
//         ON c.id = cd.component_id 
//         AND cd.languageid = {$language_id}
//     ORDER BY c.id ASC
// ";

// Lấy danh sách ngôn ngữ
// $languages = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1 ORDER BY id ASC");
// $smarty->assign("languages", $languages);


// Khởi tạo Smarty
$smarty->assign([
    'lienket' => $lienket,
    'tabmenu' => 0,
]);

switch ($act) {
    // ================
    // CHỈNH SỬA MENU
    // ================
    case 'edit':
        $id = intval(isset($_GET['id']) ? $_GET['id'] : 0);
        $admin_lang = isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : 1;
        $menu = $GLOBALS["sp"]->getRow("SELECT * FROM {$GLOBALS['db_sp']}.menu WHERE id={$id}");
        $menuDetail = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.menu_detail WHERE menu_id = {$id}");
        $smarty->assign([
            'edit'        => $menu,
            'menuDetail'  => $menuDetail,
        ]);
        $template = 'menu/edit.tpl';
        break;

    // ================
    // THÊM MỚI MENU
    // ================
    case 'add':
        $template = 'menu/create.tpl';
        break;

    // =====================
    // XOÁ DANH SÁCH MENU
    // =====================

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));

            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.menu WHERE id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.menu_detail WHERE menu_id IN ($idList)");

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;

        // =====================
        // ẨN / HIỆN MENU
        // =====================
    case 'show':
    case 'hide':
        $ids = isset($_POST['iddel']) ? $_POST['iddel'] : [];
        $active = ($act === 'show') ? 1 : 0;
        foreach ($ids as $id) {
            $id = intval($id);
            $GLOBALS["sp"]->execute("UPDATE {$GLOBALS['db_sp']}.menu SET active={$active} WHERE id={$id}");
        }
        page_transfer2('index.php?do=menu');
        break;

    // =====================
    // SẮP XẾP MENU
    // =====================
    case 'order':
        ob_clean(); // Xóa toàn bộ output trước đó (tránh lỗi JSON)
        header('Content-Type: application/json; charset=utf-8');

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
                $sql = "
                    UPDATE {$GLOBALS['db_sp']}.menu
                    SET num = CASE id {$cases} END
                    WHERE id IN ({$idsString})
                ";

                try {
                    $res = $GLOBALS["sp"]->execute($sql);
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Lỗi SQL: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Danh mục không hợp lệ!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Không có dữ liệu để sắp xếp!']);
        }

        exit;
        // =====================
        // THÊM HOẶC SỬA MENU (SUBMIT)
        // =====================
    case 'addsm':
    case 'editsm':
        saveMenu();
        page_transfer2('index.php?do=menu');
        break;

    // =====================
    // MẶC ĐỊNH: DANH SÁCH MENU
    // =====================
    default:
        //$lang_id = intval(isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : 1);
        $menus = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.menu order by num asc");
        $details = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.menu_detail");

        $menuDetail = [];
        foreach ($details as $d) {
            $menuDetail[$d['menu_id']][$d['languageid']] = $d;
        }

        foreach ($menus as &$item) {
            $id = $item['id'];
            $item['details'] = isset($menuDetail[$id]) ? $menuDetail[$id] : [];
        }
        $smarty->assign('view', $menus);
        $template = 'menu/list.tpl';
        break;
}

// Render template
$smarty->display('header.tpl');
$smarty->display($template);
$smarty->display('footer.tpl');


// ==========================
// HÀM XỬ LÝ THÊM / SỬA MENU
// ==========================
function saveMenu()
{
    global $act;
    $id = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    //$language_id = intval(isset($_SESSION['admin_lang']) ? $_SESSION['admin_lang'] : 1); // ✅ lấy ngôn ngữ hiện tại từ session

    // 1️⃣ Tính num tự động nếu thêm mới
    if ($act === 'addsm') {
        $maxNum = $GLOBALS['sp']->getOne("SELECT MAX(num) FROM {$GLOBALS['db_sp']}.menu");
        $newNum = $maxNum ? $maxNum + 1 : 1;
    } else {
        $newNum = intval(isset($_POST["num"]) ? $_POST["num"] : 0);
    }

    // 2️⃣ Dữ liệu bảng menu
    $arr = [
        'link_out' => trim(isset($_POST['link']) ? $_POST['link'] : ''),
        'choose'   => trim(isset($_POST['choose']) ? $_POST['choose'] : ''),
        'comp'     => intval(isset($_POST['menu']) ? $_POST['menu'] : 0),
        'has_sub'  => !empty($_POST['menucon']) ? 1 : 0,
        'active'   => !empty($_POST['active']) ? 1 : 0,
        'num'      => $newNum,
    ];

    // 3️⃣ Thêm / sửa bảng menu
    if ($act === 'addsm') {
        vaInsert('menu', $arr);
        $id = $GLOBALS['sp']->Insert_ID(); // ✅ lấy ID mới
    } else {
        vaUpdate('menu', $arr, "id={$id}");
    }

    // 4️⃣ Dữ liệu chi tiết theo ngôn ngữ hiện tại
    $languages = isset($_POST['languages']) ? $_POST['languages'] : array();

    if (empty($languages)) {
        exit('Chưa có dữ liệu ngôn ngữ nào được gửi.');
    }
    foreach ($languages as $language_id => $data) {
        $name    = isset($data['name']) ? trim($data['name']) : '';
        // Bỏ qua nếu không có tên
        if ($name === '') continue;

        // Tạo unique_key riêng cho từng ngôn ngữ
        $unique_key = isset($data['unique_key']) && trim($data['unique_key']) !== '' ? trim($data['unique_key']) : StripUnicode($name);

        $exists = $GLOBALS["sp"]->getOne(
            "SELECT COUNT(*) FROM {$GLOBALS['db_sp']}.menu_detail WHERE unique_key='{$unique_key}'"
                . ($id ? " AND menu_id<>$id" : '')
        );
        $unique_key_final = $exists ? $unique_key . "-$id" : $unique_key;
        // Chuẩn bị dữ liệu lưu
        $arrDetail = array(
            'menu_id' => $id,
            'languageid'     => $language_id,
            'name'           => $name,
            'unique_key'     => $unique_key_final,
        );
        // Kiểm tra đã tồn tại bản ghi cho articlelist_id + languageid
        $detail = $GLOBALS["sp"]->getRow(
            "SELECT * FROM {$GLOBALS['db_sp']}.menu_detail WHERE menu_id=$id AND languageid=$language_id"
        );

        if ($detail) {
            vaUpdate('menu_detail', $arrDetail, "id={$detail['id']}");
        } else {
            vaInsert('menu_detail', $arrDetail);
        }
    }
}
