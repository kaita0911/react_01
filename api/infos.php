<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$infos_ids = [
    1 => 'logoHome',
    2 => 'domain',
    3 => 'map',
    4 => 'banknd',
    5 => 'hotline',
    6 => 'email',
    7 => 'faceShare',
    11 => 'introfooter',
    12 => 'showcart',
    14 => 'formdangky',
    15 => 'seo',
    17 => 'headerscript',
    18 => 'bodyscript',
    19 => 'filter_product',
    20 => 'searchengine',
    21 => 'quickview',
    22 => 'makm'
];

$data = [];

foreach ($infos_ids as $id => $varname) {
    $rs = $GLOBALS['sp']->getRow(
        "SELECT * FROM {$GLOBALS['db_sp']}.infos WHERE id=$id"
    );

    $data[$varname] = $rs ?: [];
}

// ===== Lấy footer (địa chỉ) =====
$rs_footer = $GLOBALS['sp']->getAll("
    SELECT f.*, fd.name, fd.content, fd.languageid, fd.address
    FROM {$GLOBALS['db_sp']}.footer AS f
    LEFT JOIN {$GLOBALS['db_sp']}.footer_detail AS fd 
        ON f.id = fd.footer_id
    WHERE fd.languageid = {$langid}
    ORDER BY f.id ASC
");

$data['footer'] = $rs_footer ?: [];

echo json_encode($data);