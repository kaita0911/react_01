<?php
include_once('../includes/config.php');

$city_ID = isset($_POST['city_ID']) ? $_POST['city_ID'] : '';

if ($city_ID != '') {
    $query = "SELECT * FROM {$GLOBALS['db_sp']}.quanhuyen WHERE matp = ?";
    $districts = $GLOBALS['sp']->getAll($query, [$city_ID]);

    echo '<option value="">Quận/Huyện</option>';
    foreach ($districts as $q) {
        echo '<option value="' . $q['maqh'] . '">' . $q['name'] . '</option>';
    }
}
