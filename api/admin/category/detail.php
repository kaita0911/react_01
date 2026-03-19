<?php

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$row = $GLOBALS['sp']->getRow("
SELECT *
FROM {$GLOBALS['db_sp']}.categories
WHERE id=?
", array($id));

$langs = $GLOBALS['sp']->getAll("
SELECT *
FROM {$GLOBALS['db_sp']}.categories_detail
WHERE categories_id=?
", array($id));

echo json_encode(array(
    "status" => true,
    "category" => $row,
    "languages" => $langs
), JSON_UNESCAPED_UNICODE);
