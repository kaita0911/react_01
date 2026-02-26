<?php

include("../includes/config.php");

$path_url = $config['BASE_URL'];

$config['BASE_DIR']     = $_SERVER['DOCUMENT_ROOT'] . '/admindir';

$config['BASE_URL']     =  "https://" . $_SERVER['SERVER_NAME'] . "/admindir";

$smarty->compile_dir    = $config['BASE_DIR'] . "/templates/template_c/";

$smarty->template_dir    = $config['BASE_DIR'] . "/templates/tpl/";

$smarty->cache_dir     =   $config['BASE_DIR'] . "/templates/cache/";

$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
///
$url_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . $_SERVER['HTTP_HOST'];

$path_admin_url = $config['BASE_URL'];
$path_url = $path_admin_url . '/index.php?do=categories&cid=2&root=1';
$smarty->assign("path_admin_url", $path_admin_url);

$smarty->assign("web_base_url", $url_base);
$smarty->assign("current_url", $current_url);
$smarty->assign("path_url", $path_url);
