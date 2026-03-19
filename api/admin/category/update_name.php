<?php

$id = $_POST["id"];
$lang = $_POST["languageid"];
$name = $_POST["name"];
$slug = slugify($name);
$GLOBALS['sp']->execute("
    UPDATE {$GLOBALS['db_sp']}.categories_detail
    SET name = ?, slug = ?
    WHERE categories_id = ?
    AND languageid = ?
    ", [$name,$slug,$id,$lang]);

echo json_encode([
"status" => true
]);
