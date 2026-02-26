<?php


// --- B1: Lấy danh sách ngôn ngữ ---
$languages = [];
$rs = $GLOBALS['sp']->Execute("
    SELECT code, id 
    FROM {$GLOBALS['db_sp']}.language 
    WHERE active = 1
");
while (!$rs->EOF) {
    $languages[$rs->fields['code']] = (int)$rs->fields['id'];
    $rs->MoveNext();
}

// --- B2: Lấy ngôn ngữ mặc định từ admin ---
$defaultLangRow = $GLOBALS['sp']->GetRow("
    SELECT code, id 
    FROM {$GLOBALS['db_sp']}.language 
    WHERE is_default = 1 
    LIMIT 1
");
$defaultLangCode = $defaultLangRow ? $defaultLangRow['code'] : 'vi';
$defaultLangId   = $defaultLangRow ? (int)$defaultLangRow['id'] : 1;

// --- B3: Xác định ngôn ngữ từ URL prefix ---
$uri = trim($_SERVER['REQUEST_URI'], '/');
$parts = explode('/', $uri);
if (isset($parts[0]) && isset($languages[$parts[0]])) {
    $lang = $parts[0];
    array_shift($parts); // loại bỏ prefix để lấy cat1
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = $defaultLangCode;
}

// --- Lấy langid ---
$langid = isset($languages[$lang]) ? $languages[$lang] : $defaultLangId;
// --- Cập nhật session ngay ---
$_SESSION['lang']   = $lang;
$_SESSION['langid'] = $langid;

// --- Prefix URL để build link ---
$lang_prefix = ($lang === 'vi') ? '' : $lang . '/';

// --- Nếu đang ở root / và ngôn ngữ mặc định != vi, redirect sang /en/ hoặc /jp/ ---
if (($uri == '' || $uri == '/') && $defaultLangCode != 'vi') {
    header("Location: /{$defaultLangCode}/");
    exit;
}


// --- Text demo đa ngôn ngữ ---
switch ($langid) {
    case 2: // English
        $home    = 'Home';
        $contact = 'Contact';
        break;
    case 1: // Vietnamese
    default:
        $home    = 'Trang chủ';
        $contact = 'Liên hệ';
        break;
}

// --- Gửi vào Smarty ---
$smarty->assign('lang', $lang);
$smarty->assign('langid', $langid);
$smarty->assign('lang_prefix', $lang_prefix);
$smarty->assign('languages', $languages);
$smarty->assign("home", $home);
$smarty->assign("contact", $contact);
