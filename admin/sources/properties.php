<?php
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : "";
$sp = $GLOBALS["sp"];
$db = $GLOBALS['db_sp'];

switch ($act) {

    case "edit":
        $id = intval($_GET["id"]);
        $rs = $sp->getRow("SELECT * FROM $db.properties WHERE id=$id");
        $smarty->assign("edit", $rs);
        $template = "properties/edit.tpl";
        break;

    case "add":
        $rsed = $sp->getRow("SELECT * FROM $db.properties WHERE num = (SELECT MAX(num) FROM $db.properties)");
        $smarty->assign("numkai", $rsed);
        $template = "properties/create.tpl";
        break;

    case "dellist":
    case "show":
    case "hide":
        if (!empty($_POST["iddel"]) && is_array($_POST["iddel"])) {
            $ids = array_map('intval', $_POST["iddel"]);
            $sqlIds = implode(',', $ids);

            if ($act === "dellist") {
                $sp->execute("DELETE FROM $db.properties WHERE id IN ($sqlIds)");
            } else {
                $status = ($act === "show") ? 1 : 0;
                $sp->execute("UPDATE $db.properties SET active=$status WHERE id IN ($sqlIds)");
            }
        }
        page_transfer2("index.php?do=properties");
        break;

    case 'dellistajax':
        ob_clean(); // Xóa mọi thứ đã in ra trước đó
        $ids = isset($_POST['cid']) ? $_POST['cid'] : '';
        if ($ids !== '') {
            $idList = implode(',', array_map('intval', explode(',', $ids)));
            $GLOBALS["sp"]->query("DELETE FROM {$GLOBALS['db_sp']}.properties WHERE id IN ($idList)");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;

    case "order":
        if (!empty($_POST["id"]) && !empty($_POST["ordering"])) {
            foreach ($_POST["id"] as $key => $id) {
                $id = intval($id);
                $order = intval($_POST["ordering"][$key]);
                $sp->execute("UPDATE $db.properties SET num=$order WHERE id=$id");
            }
        }
        page_transfer2("index.php?do=properties");
        break;

    case "addsm":
    case "editsm":
        Editsm($act);
        page_transfer2("index.php?do=properties");
        break;

    default:
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page = max(1, intval($page));
        $sql = "SELECT * FROM $db.properties ORDER BY num ASC, id DESC";
        $total = count($sp->getAll($sql));

        $num_rows_page = isset($numPageAll) ? $numPageAll : 20;
        $num_page = ceil($total / $num_rows_page);
        $begin = ($page - 1) * $num_rows_page;
        $url = "index.php?do=properties";

        $link_url = ($num_page > 1) ? paginator($num_page, $page, 20, $url) : "";

        $rs = $sp->getAll("$sql LIMIT $begin, $num_rows_page");

        $number = ($page != 1) ? $num_rows_page * ($page - 1) : 0;

        $smarty->assign([
            "link_url" => $link_url,
            "view" => $rs,
            "number" => $number
        ]);

        $template = "properties/list.tpl";
        break;
}

$smarty->assign("tabmenu", 0);
$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");


function Editsm($act)
{
    global $db, $GLOBALS;

    $sp = $GLOBALS["sp"];
    $arr = [
        'name_vn' => trim($_POST["name_vn"]),
        'name_en' => trim($_POST["name_en"]),
        'theloai' => isset($_POST["theloai"]) ? $_POST["theloai"] : '',
        'active' => (isset($_POST['active']) ? $_POST['active'] : '') === 'active' ? 1 : 0,
        'num' => intval(isset($_POST["num"]) ? $_POST["num"] : 0)
    ];

    if ($act === "addsm") {
        $arr['num'] += 1;
        vaInsert('properties', $arr);
    } else {
        $id = intval($_POST['id']);
        vaUpdate('properties', $arr, "id=$id");
    }
}
