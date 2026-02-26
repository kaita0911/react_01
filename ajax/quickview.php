<?php

include_once(__DIR__ . "/../includes/config.php");
include_once(__DIR__ . "/../includes/get_languages.php");

$id = (int)$_GET['id'];
if (!$id) exit;

$sql = "
SELECT a.id, a.img_thumb_vn,
       d.name, p.price,p.priceold, d.short
FROM {$GLOBALS['db_sp']}.articlelist a
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail d 
    ON d.articlelist_id = a.id AND d.languageid = 1
LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price p
    ON p.articlelist_id = a.id
WHERE a.id = {$id}
LIMIT 1
";

$product = $GLOBALS['sp']->getRow($sql);

if (!$product) exit;

$price = $product['price'] > 0
    ? number_format($product['price'],0,',','.') . '₫'
    : 'Liên hệ';
$priceold = $product['priceold'] > 0
? number_format($product['priceold'],0,',','.') . '₫'
: '';
?>

<div class="quickview-product">
  <div class="quickview-product__img">
    <img src="<?= $product['img_thumb_vn'] ?>" width="300">
  </div>
  <div class="quickview-product__info">
    <h3><?= $product['name'] ?></h3>
    <div class="price"><?= $price ?> <?= $priceold ?></div>
    <div class="quickview-product__info__short"><?= $product['short'] ?></div>
    <button class="btn-cart btn-add-cart"
            data-id="<?= $product['id'] ?>"
            data-name="<?= $product['name'] ?>"
            data-price="<?= $product['price'] ?>"
            data-img="<?= $product['img_thumb_vn'] ?>">
        Thêm vào giỏ
    </button>
  </div>
</div>