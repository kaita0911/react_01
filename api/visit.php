<?php

require_once("../includes/config.php");

$ip = $_SERVER['REMOTE_ADDR'];

/* check trùng IP trong ngày */
$sql = "
SELECT id FROM visit_log 
WHERE ip = '$ip' 
AND DATE(created_at) = CURDATE()
LIMIT 1
";

$check = $GLOBALS['sp']->getRow($sql);

if (!$check) {
    // insert log
    $GLOBALS['sp']->execute("
        INSERT INTO visit_log (ip) VALUES ('$ip')
    ");

    // tăng total
    $GLOBALS['sp']->execute("
        UPDATE visit SET total = total + 1
    ");
}
