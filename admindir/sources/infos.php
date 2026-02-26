<?php
// Khởi tạo
$act  = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
$comp = isset($_GET['comp']) ? $_GET['comp'] : '';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Switch theo action
switch ($act) {
    // ================== EDIT ==================
    case "edit":
        $id = (int)(isset($_GET["id"]) ? $_GET["id"] : 0);
        if ($id <= 0) {
            die("Invalid ID");
        }

        $sql = "SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id = ?";
        $rs  = $GLOBALS["sp"]->getRow($sql, [$id]);
        $smarty->assign("edit", $rs);

        $template = "infos/edit.tpl";
        break;

    // ================== SAVE (ADD / EDIT) ==================
    case "addsm":
    case "editsm":
        Editsm();
        page_transfer2("index.php?do=infos&comp={$comp}");
        exit;

        // ================== LIST (DEFAULT) ==================
    default:
        // Lọc quyền user
        $sqlBase = ($_SESSION["admin_artseed_username"] == 'kaita')
            ? "SELECT * FROM {$GLOBALS['db_sp']}.infos ORDER BY id ASC"
            : "SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE active = 1 ORDER BY id ASC";

        // Đếm tổng
        $total = $GLOBALS["sp"]->getOne("SELECT COUNT(*) FROM ({$sqlBase}) AS t");
        $num_rows_page = isset($numPageAll) ? $numPageAll : 20;
        $num_page  = ceil($total / $num_rows_page);
        $begin     = ($page - 1) * $num_rows_page;

        $sql = "{$sqlBase} LIMIT {$begin}, {$num_rows_page}";
        $rs  = $GLOBALS["sp"]->getAll($sql);

        // Tạo link phân trang
        $url = "index.php?do=infos";
        $link_url = ($num_page > 1) ? paginator($num_page, $page, 20, $url) : '';

        // Gán sang template
        $smarty->assign([
            "link_url" => $link_url,
            "view"     => $rs,
            "checkPer2" => checkPermision(isset($idpem) ? $idpem : '', 2) ? "true" : "",
        ]);

        $template = "infos/list.tpl";
        break;
}

// Tab menu & hiển thị
$smarty->assign("tabmenu", 2);
$smarty->display("header.tpl");
$smarty->display($template);
$smarty->display("footer.tpl");

// ================== FUNCTION ==================
function Editsm()
{
    global $path_url, $GLOBALS;

    $act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';
    $id  = (int)(isset($_POST['id']) ? $_POST['id'] : 0);

    // Gom dữ liệu
    $arr = [
        'name_vn'        => isset($_POST["name_vn"]) ? $_POST["name_vn"] : '',
        'domain'         => isset($_POST["domain"]) ? $_POST["domain"] : '',
        'plain_text_vn'  => trim(isset($_POST["plain_text_vn"]) ? $_POST["plain_text_vn"] : ''),
        'plain_text_en'  => trim(isset($_POST["plain_text_en"]) ? $_POST["plain_text_en"] : ''),
        'email'          => trim(isset($_POST["email"]) ? $_POST["email"] : ''),
        'phone'          => trim(isset($_POST["phone"]) ? $_POST["phone"] : ''),
        'address_vn'     => trim(isset($_POST["address_vn"]) ? $_POST["address_vn"] : ''),
        'googlemap'      => trim(isset($_POST["googlemap"]) ? $_POST["googlemap"] : ''),
        'content_vn'     => isset($_POST["content_vn"]) ? $_POST["content_vn"] : '',
        'content_en'     => isset($_POST["content_en"]) ? $_POST["content_en"] : '',
        'facebook'       => trim(isset($_POST["facebook"]) ? $_POST["facebook"] : ''),
        'linkedin'         => trim(isset($_POST["linkedin"]) ? $_POST["linkedin"] : ''),
        'tiktok'        => trim(isset($_POST["tiktok"]) ? $_POST["tiktok"] : ''),
        'instagram'      => trim(isset($_POST["instagram"]) ? $_POST["instagram"] : ''),
        'youtube'        => trim(isset($_POST["youtube"]) ? $_POST["youtube"] : ''),
        'printest'       => trim(isset($_POST["printest"]) ? $_POST["printest"] : ''),
        'active'         => isset($_POST['active']) ? 1 : 0,
        'phiship'        => isset($_POST['phiship']) ? 1 : 0,
        'open'           => isset($_POST['open']) ? 1 : 0,
        'ngaythang'      => isset($_POST['ngaythang']) ? 1 : 0,
        'keyword'        => trim(isset($_POST["keyword"]) ? $_POST["keyword"] : ''),
        'desc'           => trim(isset($_POST["desc"]) ? $_POST["desc"] : ''),
    ];

    $arr2 = [
        'email'          => trim(isset($_POST["plain_text_vn"]) ? $_POST["plain_text_vn"] : ''),
    ];

    // // Upload ảnh
    // if (!empty($_FILES['img_thumb_vn']['name']) && $_FILES['img_thumb_vn']['size'] > 0) {
    //     $img = $_FILES['img_thumb_vn']['name'];
    //     $ext = pathinfo($img, PATHINFO_EXTENSION);
    //     $filename = 'trg-' . time() . '.' . strtolower($ext);
    //     $filename = RenameFile($filename);
    //     $target = "../hinh-anh/trung-gian/{$filename}";
    //     copy($_FILES['img_thumb_vn']['tmp_name'], $target);
    //     $arr['img_thumb_vn'] = "hinh-anh/trung-gian/{$filename}";
    // }

    // // Update
    // if ($act === "editsm" && $id > 0) {
    //     vaUpdate('infos', $arr, "id={$id}");
    //     vaUpdate('admin', $arr2, "id=1");
    // }

    // 2️⃣ Upload ảnh
    if (!empty($_FILES['img_thumb_vn']['name']) && $_FILES['img_thumb_vn']['error'] === UPLOAD_ERR_OK) {

        // 🔹 Nếu đang edit thì xóa ảnh cũ
        if ($act === 'editsm' && !empty($id)) {
            $oldImg = $GLOBALS['sp']->getOne("SELECT img_thumb_vn FROM {$GLOBALS['db_sp']}.infos WHERE id = " . intval($id));
            if (!empty($oldImg) && file_exists('../' . $oldImg)) {
                @unlink('../' . $oldImg);
            }
        }

        $file = $_FILES['img_thumb_vn'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uploadDir = "../hinh-anh/trung-gian/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = 'trg-' . time() . rand(1000, 9999) . '.' . $ext;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // ✅ Chuyển sang WebP
            $webpPath = preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $uploadPath);
            if (convertToWebp($uploadPath, $webpPath, 85)) {
                @unlink($uploadPath); // Xóa ảnh gốc (nếu muốn giữ thì bỏ dòng này)
                $arr['img_thumb_vn'] = str_replace('../', '', $webpPath);
            } else {
                $arr['img_thumb_vn'] = str_replace('../', '', $uploadPath);
            }
        }
    }

    // 3️⃣ Giữ ảnh cũ nếu edit và không chọn file mới
    if ($act !== 'addsm') {
        $currentImg = $GLOBALS["sp"]->getOne("SELECT img_thumb_vn FROM infos WHERE id=$id");
        if (!isset($arr['img_thumb_vn']) || $arr['img_thumb_vn'] === '') {
            $arr['img_thumb_vn'] = $currentImg;
        }
    }

    // Update
    if ($act === "editsm" && $id > 0) {
        vaUpdate('infos', $arr, "id={$id}");
        vaUpdate('admin', $arr2, "id=1");
    }
}
