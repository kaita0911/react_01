<?php

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

// $languages = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1");
// $smarty->assign("languages", $languages);

switch ($act) {
    case "edit":
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $language_id = isset($_SESSION['admin_lang']) && $_SESSION['admin_lang'] != ''
            ? intval($_SESSION['admin_lang'])
            : 1;

        // --- Lấy thông tin chung của footer ---
        $sql_color = "SELECT * FROM {$GLOBALS['db_sp']}.colors WHERE id = ?";
        $footer = $GLOBALS['sp']->getRow($sql_color, [$id]);
        $smarty->assign('edit', $colors);
        $template = "color/edit.tpl";
        break;

    case "add":
        $template = "color/create.tpl";
        break;

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.colors WHERE id IN ($idList)");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;

    case 'updateOrder':
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
                        UPDATE {$GLOBALS['db_sp']}.colors
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
    case "addsm":
    case "editsm":
        saveColor();
        page_transfer2("index.php?do=color");
        exit;

    default:

        $colors = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.colors order by num desc");
        $smarty->assign("view", $colors);
        $template = "color/list.tpl";
        break;
}

$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");

function saveColor()
{
    global $act;
    $sp    = $GLOBALS['sp'];
    $id  = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    // ==== 1️⃣ Xử lý num tự động ====
    $newNum = ($act === 'addsm')
        ? (($sp->getOne("SELECT MAX(num) FROM {$GLOBALS['db_sp']}.colors") ?: 0) + 1)
        : intval(isset($_POST['num']) ? $_POST['num'] : 0);

    // ==== 2️⃣ Lấy dữ liệu POST cơ bản ====
    $arr = [
        'name'       => trim(isset($_POST["name"]) ? $_POST["name"] : ''),
        'code'       => trim(isset($_POST["code"]) ? $_POST["code"] : ''),
        'num'         => $newNum,
        'active'    => !empty($_POST['active']) ? 1 : 0,
    ];
    if ($act === 'addsm') {
        vaInsert('colors', $arr);
        $id = $GLOBALS['sp']->Insert_ID(); // ✅ Lấy ID mới insert
    } else {
        vaUpdate('colors', $arr, "id=$id");
    }
}
