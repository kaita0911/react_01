<?php

$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

// $languages = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1");
// $smarty->assign("languages", $languages);

switch ($act) {
    case "edit":
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $footerDetail = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.footer_detail WHERE footer_id = {$id}");
        // --- Lấy thông tin chung của footer ---
        $sql_footer = "SELECT * FROM {$GLOBALS['db_sp']}.footer WHERE id = ?";
        $footer = $GLOBALS['sp']->getRow($sql_footer, [$id]);
        $smarty->assign([
            "footerDetail" => $footerDetail,
            "footer" => $footer,
        ]);

        $template = "footer/edit.tpl";
        break;

    case "add":
        $template = "footer/create.tpl";
        break;

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));

            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.footer_detail WHERE footer_id IN ($idList)");
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.footer WHERE id IN ($idList)");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;


    case "addsm":
    case "editsm":
        saveFooter();
        page_transfer2("index.php?do=footer");
        exit;

    default:

        $footers = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.footer");
        $details = $GLOBALS["sp"]->getAll("SELECT * FROM {$GLOBALS['db_sp']}.footer_detail");

        $footerDetail = [];
        foreach ($details as $d) {
            $footerDetail[$d['footer_id']][$d['languageid']] = $d;
        }

        foreach ($footers as &$item) {
            $id = $item['id'];
            $item['details'] = isset($footerDetail[$id]) ? $footerDetail[$id] : [];
        }
        $smarty->assign("view", $footers);
        $template = "footer/list.tpl";
        break;
}

$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");

function saveFooter()
{
    global $act;
    $id  = intval(isset($_POST['id']) ? $_POST['id'] : 0);
    // ==== 2️⃣ Lấy dữ liệu POST cơ bản ====
    $arr = [
        'hotline'       => trim(isset($_POST["hotline"]) ? $_POST["hotline"] : ''),
        'email'       => trim(isset($_POST["email"]) ? $_POST["email"] : ''),
        'map'       => trim(isset($_POST["map"]) ? $_POST["map"] : ''),
    ];
    if ($act === 'addsm') {
        vaInsert('footer', $arr);
        $id = $GLOBALS['sp']->Insert_ID(); // ✅ Lấy ID mới insert
    } else {
        vaUpdate('footer', $arr, "id=$id");
    }
    // Lặp qua từng ngôn ngữ để lưu
    $languages = isset($_POST['languages']) ? $_POST['languages'] : array();

    if (empty($languages)) {
        exit('Chưa có dữ liệu ngôn ngữ nào được gửi.');
    }
    foreach ($languages as $language_id => $data) {
        $name    = isset($data['name']) ? trim($data['name']) : '';
        $address   = isset($data['address']) ? trim($data['address']) : '';
        $content = isset($data['content']) ? trim($data['content']) : '';
        // Chuẩn bị dữ liệu lưu
        $arrDetail = array(
            'footer_id' => $id,
            'languageid'     => $language_id,
            'name'           => $name,
            'content'        => $content,
            'address'        => $address
        );
        // Kiểm tra đã tồn tại bản ghi cho articlelist_id + languageid
        $detail = $GLOBALS["sp"]->getRow(
            "SELECT * FROM {$GLOBALS['db_sp']}.footer_detail WHERE footer_id=$id AND languageid=$language_id"
        );

        if ($detail) {
            vaUpdate('footer_detail', $arrDetail, "id={$detail['id']}");
        } else {
            vaInsert('footer_detail', $arrDetail);
        }
    }
}
