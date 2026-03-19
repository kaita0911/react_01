<?php

$rows = $GLOBALS['sp']->getAll("
SELECT 
c.id,
c.parent_id,
c.active,c.home,
c.num,c.img_vn,
cd.name,cd.languageid,cd.slug
FROM {$GLOBALS['db_sp']}.categories c
LEFT JOIN {$GLOBALS['db_sp']}.categories_detail cd
ON cd.categories_id = c.id
WHERE c.comp = ?
ORDER BY c.num ASC
", array($comp));
$data = [];
foreach($rows as $r) {

    $id = $r['id'];

    if(!isset($data[$id])) {
        $data[$id] = [
            "id" => $r["id"],
            "parent_id" => $r["parent_id"],
            "active" => $r["active"],
            "home" => $r["home"],
            "num" => $r["num"],
            "img_vn" => $r["img_vn"],
            "names" => [],
            "slug" => [],

        ];
    }

    if($r["languageid"]) {
        $data[$id]["names"][$r["languageid"]] = $r["name"];
        $data[$id]["slug"][$r["languageid"]] = $r["slug"];
    }
}

$tree = buildTree(array_values($data));

echo json_encode([
    "status" => true,
    "data" => $tree
], JSON_UNESCAPED_UNICODE);

exit;
