<?php

$ids = isset($_POST['ids']) ? $_POST['ids'] : [];

if (empty($ids)) {
    jsonResponse([
        "status" => false,
        "message" => "Thiếu ids"
    ]);
}
function copyImage($oldPath)
{

    if (!$oldPath) {
        return '';
    }

    $fullOldPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $oldPath;

    if (!file_exists($fullOldPath)) {
        return $oldPath;
    }

    $ext = pathinfo($oldPath, PATHINFO_EXTENSION);

    $newName = pathinfo($oldPath, PATHINFO_FILENAME) . '-copy-' . time() . '.' . $ext;

    $newPath = dirname($oldPath) . '/' . $newName;

    $fullNewPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $newPath;

    copy($fullOldPath, $fullNewPath);

    return $newPath;
}
foreach ($ids as $id) {

    $id = intval($id);

    // ===== 1. LẤY BÀI GỐC =====
    $article = $GLOBALS['sp']->GetRow("
        SELECT *
        FROM {$GLOBALS['db_sp']}.articlelist
        WHERE id=?
    ", [$id]);

    if (!$article) {
        continue;
    }

    unset($article['id']);
    $maxNum = $GLOBALS['sp']->getOne("
    SELECT MAX(num) FROM {$GLOBALS['db_sp']}.articlelist");

    $article['num'] = $maxNum + 1;

    // ===== INSERT =====
    $GLOBALS['sp']->Execute("
        INSERT INTO {$GLOBALS['db_sp']}.articlelist
        (comp, active, img_thumb_vn, num, new, hot, mostview)
        VALUES (?,?,?,?,?,?,?)
    ", [
        $article['comp'],
        $article['active'],
        $newImage = copyImage($article['img_thumb_vn']),
        $article['num'],
        $article['new'],
        $article['hot'],
        $article['mostview']
    ]);

    $newId = $GLOBALS['sp']->Insert_ID();

    // ===== COPY CATEGORY =====
    $cats = $GLOBALS['sp']->GetAll("
        SELECT categories_id
        FROM {$GLOBALS['db_sp']}.articlelist_categories
        WHERE articlelist_id=?
    ", [$id]);

    foreach ($cats as $c) {
        $GLOBALS['sp']->Execute("
            INSERT INTO {$GLOBALS['db_sp']}.articlelist_categories
            (articlelist_id, categories_id)
            VALUES (?,?)
        ", [$newId, $c['categories_id']]);
    }

    // ===== COPY DETAIL =====
    $details = $GLOBALS['sp']->GetAll("
        SELECT *
        FROM {$GLOBALS['db_sp']}.articlelist_detail
        WHERE articlelist_id=?
    ", [$id]);

    foreach ($details as $d) {
        $newName = $d['name'] . '-Copy';
        $newSlug = slugify($newName) . '-' . uniqid();

        $GLOBALS['sp']->Execute("
            INSERT INTO {$GLOBALS['db_sp']}.articlelist_detail
            (articlelist_id, languageid, name, slug, short, content, keyword, des)
            VALUES (?,?,?,?,?,?,?,?)
        ", [
            $newId,
            $d['languageid'],
            $newName,
            $newSlug,
            $d['short'],
            $d['content'],
            $d['keyword'],
            $d['des']
        ]);

    }

    // ===== COPY PRICE =====
    $price = $GLOBALS['sp']->GetRow("
        SELECT *
        FROM {$GLOBALS['db_sp']}.articlelist_price
        WHERE articlelist_id=?
    ", [$id]);

    if ($price) {
        $GLOBALS['sp']->Execute("
            INSERT INTO {$GLOBALS['db_sp']}.articlelist_price
            (articlelist_id, price, priceold)
            VALUES (?,?,?)
        ", [
            $newId,
            $price['price'],
            $price['priceold']
        ]);
    }

    // ===== COPY GALLERY =====
    $gallery = $GLOBALS['sp']->GetAll("
        SELECT *
        FROM gallery_sp
        WHERE articlelist_id=?
    ", [$id]);

    if (!empty($gallery)) {
        foreach ($gallery as $g) {

            $newImg = copyImage($g['img_vn']); // 🔥 copy file thật

            $GLOBALS['sp']->Execute("
                INSERT INTO gallery_sp
                (articlelist_id, img_vn, num)
                VALUES (?,?,?)
            ", [
                $newId,
                $newImg,
                $g['num']
            ]);
        }
    }
}

jsonResponse([
    "status" => true
]);
