<?php
require_once(__DIR__ . "/../includes/config.php");
require_once(__DIR__ . "/../includes/get_languages.php");
$keyword = trim(isset($_POST['keyword']) ? $_POST['keyword'] : '(không có keyword)');
$whereDetail = '';
$params = [];
if ($keyword !== '') {
  $whereDetail = " AND d.name LIKE ? ";
  $params[] = "%$keyword%";
  $sql = "SELECT a.id, a.comp, a.num, a.unique_key, a.img_thumb_vn, d.name AS name_detail, d.unique_key
  FROM {$GLOBALS['db_sp']}.articlelist AS a
  LEFT JOIN {$GLOBALS['db_sp']}.articlelist_detail AS d
  ON a.id = d.articlelist_id AND d.languageid = {$langid}
  -- LEFT JOIN {$GLOBALS['db_sp']}.articlelist_price AS p
  -- ON p.articlelist_id = a.id   -- nối với bảng giá
  WHERE a.comp =2 AND a.active = 1 $whereDetail
  ORDER BY a.num DESC";

  $results = $GLOBALS['sp']->getAll($sql, $params);
  // if (!empty($results)) {
  //   foreach ($results as &$item) {
  //     $item['price_formatted']    = isset($item['price']) ? number_format($item['price'], 0, ',', '.') : '';
  //     $item['priceold_formatted'] = isset($item['priceold']) ? number_format($item['priceold'], 0, ',', '.') : '';
  //   }
  //   unset($item);
  // }
  $smarty->assign('keyword', htmlspecialchars($keyword));
  $smarty->assign('suggestions', $results);

  $smarty->display('search/ajax_suggestions.tpl');
}
