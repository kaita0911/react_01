<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");
require_once __DIR__ . "/../functions/category-tree.php";

global $db_sp, $sp, $langid;

/////////////////////////////////////
// 1. LẤY CATEGORY TREE (FILE MỚI)
/////////////////////////////////////

$categoryTree = getCategoryTree($sp, $db_sp, $langid);

/////////////////////////////////////
// 2. LẤY MENU CHÍNH
/////////////////////////////////////

$sql = "SELECT m.id, m.comp,
               d.name AS name_detail,
               d.unique_key,
               m.has_sub
        FROM {$db_sp}.menu AS m
        LEFT JOIN {$db_sp}.menu_detail AS d
            ON d.menu_id = m.id
            AND d.languageid = {$langid}
        WHERE m.active = 1
        ORDER BY m.num ASC";

$menus = $sp->getAll($sql);
$menus = normalizeArray($menus);
/////////////////////////////////////
// 3. GẮN CATEGORY VÀO MENU
/////////////////////////////////////

foreach ($menus as &$m) {

    if ((int)$m['has_sub'] === 1) {

        $m['categories'] = array_values(
            array_filter($categoryTree, function ($c) use ($m) {
                return isset($c['comp']) &&
                       $c['comp'] == $m['comp'];
            })
        );

    } else {
        $m['categories'] = [];
    }
}

unset($m);

/////////////////////////////////////
// 4. TRẢ JSON CHO REACT
/////////////////////////////////////

echo json_encode($menus, JSON_UNESCAPED_UNICODE);
