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

// --- B2: Lấy ngôn ngữ mặc định ---
$defaultLangRow = $GLOBALS['sp']->GetRow("
    SELECT code, id 
    FROM {$GLOBALS['db_sp']}.language 
    WHERE is_default = 1 
    LIMIT 1
");

$defaultLangCode = $defaultLangRow ? $defaultLangRow['code'] : 'vi';
$defaultLangId   = $defaultLangRow ? (int)$defaultLangRow['id'] : 1;


// --- B3: Lấy URL ---
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

$parts = explode('/', $uri);


// --- B4: Detect language ---
if (isset($parts[0]) && isset($languages[$parts[0]])) {

    $lang = $parts[0];
    array_shift($parts);

} elseif (isset($_SESSION['lang'])) {

    $lang = $_SESSION['lang'];

} else {

    $lang = $defaultLangCode;

}


// --- B5: Lang ID ---
$langid = isset($languages[$lang]) ? $languages[$lang] : $defaultLangId;


// --- B6: Session ---
$_SESSION['lang']   = $lang;
$_SESSION['langid'] = $langid;


// --- B7: Prefix URL ---
$lang_prefix = ($lang === $defaultLangCode) ? '' : $lang . '/';


// --- B8: Redirect root ---
if ($uri == '' && $defaultLangCode != 'vi') {

    header("Location: /{$defaultLangCode}/");
    exit;

}