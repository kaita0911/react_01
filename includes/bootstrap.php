<?php

//DB Exceptions
require_once($config['BASE_DIR'] . '/libraries/adodb5/adodb-exceptions.inc.php');
require_once($config['BASE_DIR'] . '/libraries/adodb5/adodb.inc.php');
$conn = ADONewConnection($DBTYPE);
//print_r($conn); die();
$GLOBALS["db_sp"] = $DBNAME;
$GLOBALS["sp"] = $conn;
$db = $GLOBALS["db_sp"];
$GLOBALS['sp']->NConnect($DBHOST, $DBUSER, $DBPASSWORD, $GLOBALS["db_sp"]);
if (!$conn) {
	echo "Could not DB !";
	exit();
}
mysql_query("SET NAMES 'UTF8'");

///
//$path_url=$config['BASE_URL'];
$path_url = $config['BASE_URL'];
$path_dir = $config['BASE_DIR'] . "/";
