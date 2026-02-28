<?php
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
global $db_sp, $sp;

// Lấy properties và languages
$smarty->assign("properties", $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.properties WHERE active=1"));
$smarty->assign("languages", $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.language WHERE active=1"));

$template = null; // Init template

switch ($act) {

    // ==================== EDIT ====================
    case "edit":
        $id = (int)(isset($_GET["id"]) ? $_GET["id"] : 0);
        if ($id > 0) {
            $sql = "SELECT c.*, d.name FROM {$GLOBALS['db_sp']}.component AS c
        LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS d 
        ON c.id = d.component_id 
        WHERE c.id=$id
        ORDER BY c.num ASC";
            $smarty->assign("edit", $GLOBALS['sp']->GetRow($sql));
            //$editDetails = $GLOBALS['sp']->getAll("SELECT * FROM {$GLOBALS['db_sp']}.component_detail WHERE component_id=$id ORDER BY id ASC");
            ///$smarty->assign("edit_name", $editDetails);
            //$smarty->assign("edit", $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.component WHERE id=$id"));
            $template = "component/edit.tpl";
        }
        break;

    // ==================== ADD ====================
    case "add":
        $maxNumComp = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.component WHERE num=(SELECT MAX(num) FROM {$GLOBALS['db_sp']}.component)");
        $smarty->assign("numkai", $maxNumComp);
        $template = "component/create.tpl";
        break;

    // ==================== DELETE ====================
    case "dellist":
        if (!checkPermision($_GET["cid"], 3)) {
            page_permision();
            page_transfer2("index.php");
        } else {
            foreach ($_POST["iddel"] as $id) {
                DeleteCat((int)$id);
            }
            page_transfer2("index.php?do=component");
        }
        break;

    // ==================== SHOW/HIDE ====================
    case "show":
    case "hide":
        $active = $act === "show" ? 1 : 0;
        foreach ($_POST["iddel"] as $id) {
            $db->execute("UPDATE {$GLOBALS['db_sp']}.component SET active=? WHERE id=?", [$active, $id]);
        }
        page_transfer2("index.php?do=component");
        break;

    // ==================== ORDER ====================
    case "order":
        $ids = $_POST["id"];
        $orderings = $_POST["ordering"];
        foreach ($ids as $i => $id) {
            $db->execute("UPDATE {$GLOBALS['db_sp']}.component SET num=? WHERE id=?", [$orderings[$i], $id]);
        }
        page_transfer2("index.php?do=component");
        break;

    // ==================== SAVE ADD/EDIT ====================
    case "addsm":
    case "editsm":
        $id = isset($_GET["id"]) ? $_GET["id"] : null;
        $newId = saveComponent($act);
        //saveComponent($act);
        // Nếu là edit và không phải user đặc biệt → quay lại danh sách bài viết
        if ($act === "editsm" && (isset($_SESSION['admin_artseed_username']) ? $_SESSION['admin_artseed_username'] : '') !== 'kaita') {
            page_transfer2("index.php?do=articlelist&comp=$newId");
        } else {
            page_transfer2("index.php?do=component");
        }
        break;

    // ==================== DEFAULT: LIST ====================
    default:
        $sql = "SELECT c.*, d.name AS detail_name FROM {$GLOBALS['db_sp']}.component AS c
        LEFT JOIN {$GLOBALS['db_sp']}.component_detail AS d 
        ON c.id = d.component_id 
        ORDER BY c.num ASC";
        $smarty->assign("view", $GLOBALS['sp']->GetAll($sql));
        $template = "component/list.tpl";
        break;
}

// Chỉ display template khi $template được set
if ($template) {
    $smarty->assign("tabmenu", 0);
    $smarty->display("header.tpl");
    $smarty->display($template);
    $smarty->display("footer.tpl");
    exit; // Ngăn code phía dưới chạy, tránh form rỗng hiển thị ngoài ý muốn
}

// ==================== FUNCTIONS ====================
function saveComponent($act)
{
    global $db, $GLOBALS;

    $arr = [
        'do' => trim(isset($_POST["do"]) ? $_POST["do"] : ''),
        'phantrang' => trim(isset($_POST["phantrang"]) ? $_POST["phantrang"] : ''),
        'iconfont' => trim(isset($_POST["iconfont"]) ? $_POST["iconfont"] : ''),
        'nhomcon' => isset($_POST['nhomcon']) ? '1' : '0',
        'danhmuchome' => isset($_POST['danhmuchome']) ? '1' : '0',
        'hinhdanhmuc' => isset($_POST['hinhdanhmuc']) ? '1' : '0',
        'motadanhmuc' => isset($_POST['motadanhmuc']) ? '1' : '0',
        'brand' => isset($_POST['brand']) ? '1' : '0',
        'nhieuhinh' => isset($_POST['nhieuhinh']) ? '1' : '0',
        'masp' => isset($_POST['masp']) ? '1' : '0',
        'price' => isset($_POST['price']) ? '1' : '0',
        'priceold' => isset($_POST['priceold']) ? '1' : '0',
        'voucher' => isset($_POST['voucher']) ? '1' : '0',
        'phiship' => isset($_POST['phiship']) ? '1' : '0',
        'mausac' => isset($_POST['mausac']) ? '1' : '0',
        'kichthuoc' => isset($_POST['kichthuoc']) ? '1' : '0',
        'active' => isset($_POST['active']) ? '1' : '0',
        'new' => isset($_POST['new']) ? '1' : '0',
        'hot' => isset($_POST['hot']) ? '1' : '0',
        'mostview' => isset($_POST['mostview']) ? '1' : '0',
        'viewed' => isset($_POST['viewed']) ? '1' : '0',
        'hinhanh' => isset($_POST['hinhanh']) ? '1' : '0',
        'short' => isset($_POST['short']) ? '1' : '0',
        'des' => isset($_POST['des']) ? '1' : '0',
        'metatag' => isset($_POST['metatag']) ? '1' : '0',
        'hinhmodule' => isset($_POST['hinhmodule']) ? '1' : '0',
        'motamodule' => isset($_POST['motamodule']) ? '1' : '0',
        'link_out' => isset($_POST['link_out']) ? '1' : '0',
        'attribute' => isset($_POST['attribute']) ? '1' : '0',
        'num' => isset($_POST["num"]) ? $_POST["num"] : 0,
        'name' => isset($_POST["name_1"]) ? $_POST["name_1"] : '',
        'unique_key' => isset($_POST["unique_key_1"]) ? $_POST["unique_key_1"] : ''
    ];

    // Upload ảnh
    if (!empty($_FILES['img_vn']['name'])) {
        $ext = strtolower(strrchr($_FILES['img_vn']['name'], "."));
        $filename = RenameFile('trg-' . time() . $ext);
        copy($_FILES['img_vn']['tmp_name'], "../hinh-anh/trung-gian/" . $filename);
        $arr['img_vn'] = "hinh-anh/trung-gian/" . $filename;
    }
    // Xử lý insert / update
    if ($act === "addsm") {
        $id_comp = vaInsert('component', $arr);
        saveComponentDetail($id_comp);
    } else {
        $id_comp = (int)(isset($_POST["id"]) ? $_POST["id"] : 0);
        vaUpdate('component', $arr, "id=$id_comp");
        saveComponentDetail($id_comp);
    }

    // Gán thuộc tính
    $GLOBALS['sp']->execute("DELETE FROM {$GLOBALS['db_sp']}.properties_component WHERE comp_id=?", [$id_comp]);
    if (!empty($_POST["properties"])) {
        foreach ($_POST["properties"] as $prop_id) {
            $prop = $GLOBALS['sp']->getRow("SELECT * FROM {$GLOBALS['db_sp']}.properties WHERE id=?", [$prop_id]);
            if ($prop) {
                vaInsert('properties_component', [
                    'properties_id' => $prop_id,
                    'comp_id'       => $id_comp,
                    'name'          => $prop['name_vn']
                ]);
            }
        }
    }
    return $id_comp;
}

function saveComponentDetail($comp_id)
{

    $name = trim(isset($_POST["name"]) ? $_POST["name"] : '');
    $content = trim(isset($_POST["content"]) ? $_POST["content"] : '');

    $exists = $GLOBALS['sp']->getOne("SELECT COUNT(*) FROM {$GLOBALS['db_sp']}.component_detail WHERE component_id=?", [$comp_id]);
    if ($exists) {
        vaUpdate('component_detail', ['name' => $name, 'content' => $content], "component_id=$comp_id");
    } else {
        vaInsert('component_detail', ['component_id' => $comp_id, 'name' => $name, 'content' => $content]);
    }
}


function deleteComponent($id)
{
    $GLOBALS['sp']->execute("DELETE FROM {$GLOBALS['db_sp']}.component WHERE id=?", [$id]);
    $GLOBALS['sp']->execute("DELETE FROM {$GLOBALS['db_sp']}.component_detail WHERE component_id=?", [$id]);
}
