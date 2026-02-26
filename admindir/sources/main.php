<?php

//trinh duyet
$rows = $GLOBALS['sp']->GetAll("SELECT user_agent FROM {$GLOBALS['db_sp']}.visits");

$browser_counts = [
    'Chrome' => 0,
    'Firefox' => 0,
    'Edge' => 0,
    'Safari' => 0,
    'Opera' => 0,
    'Other' => 0
];

foreach ($rows as $row) {
    $ua = $row['user_agent'];
    if (strpos($ua, 'Chrome') !== false && strpos($ua, 'Edge') === false && strpos($ua, 'OPR') === false) {
        $browser_counts['Chrome']++;
    } elseif (strpos($ua, 'Firefox') !== false) {
        $browser_counts['Firefox']++;
    } elseif (strpos($ua, 'Edge') !== false) {
        $browser_counts['Edge']++;
    } elseif (strpos($ua, 'Safari') !== false && strpos($ua, 'Chrome') === false) {
        $browser_counts['Safari']++;
    } elseif (strpos($ua, 'OPR') !== false || strpos($ua, 'Opera') !== false) {
        $browser_counts['Opera']++;
    } else {
        $browser_counts['Other']++;
    }
}
// Gán biến cho Smarty
$smarty->assign('browser_counts', $browser_counts);
// header('Content-Type: application/json');
// echo json_encode($browser_counts);
// === Thống kê truy cập ===

// Tổng số IP đã truy cập (mỗi IP tính 1 lần)
$total = (int) $GLOBALS['sp']->getOne("
    SELECT COUNT(*) 
    FROM {$GLOBALS['db_sp']}.visits
");

// Đang online (truy cập trong 5 phút gần nhất)
$online = (int) $GLOBALS['sp']->getOne("
    SELECT COUNT(*) 
    FROM {$GLOBALS['db_sp']}.visits
    WHERE last_active >= (NOW() - INTERVAL 5 MINUTE)
");

// Trong tuần hiện tại
$week = (int) $GLOBALS['sp']->getOne("
    SELECT COUNT(*) 
    FROM {$GLOBALS['db_sp']}.visits
    WHERE YEARWEEK(visit_time, 1) = YEARWEEK(NOW(), 1)
");

// Trong tháng hiện tại
$month = (int) $GLOBALS['sp']->getOne("
    SELECT COUNT(*) 
    FROM {$GLOBALS['db_sp']}.visits
    WHERE YEAR(visit_time) = YEAR(NOW())
      AND MONTH(visit_time) = MONTH(NOW())
");


$smarty->assign('total_visits', $total);
$smarty->assign('online_visits', $online);
$smarty->assign('week_visits', $week);
$smarty->assign('month_visits', $month);
////////////Thong ke link truy cap nhieu

///thong ke truy cap theo tháng

$rows = $GLOBALS['sp']->getAll("
    SELECT 
        MONTH(created_at) AS month,
        url,
        COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.visit_logs
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at), url
    ORDER BY month ASC, total DESC
");
/////
$topByMonth = [];

// tạo sẵn tháng 1 → 12
for ($m = 1; $m <= 12; $m++) {
    $topByMonth[$m] = [];
}

foreach ($rows as $row) {
    $month = (int)$row['month'];

    // mỗi tháng chỉ lấy 20 link đầu
    if (count($topByMonth[$month]) < 20) {
        $topByMonth[$month][] = $row;
    }
}

$smarty->assign('topByMonth', $topByMonth);
//$smarty->assign('year', date('Y'));

// === Thống kê truy cập theo tháng (12 tháng) ===
$months = array_fill(0, 12, 0);

$rows = $GLOBALS['sp']->getAll("
    SELECT MONTH(created_at) AS m, COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.visit_logs
    WHERE YEAR(created_at) = YEAR(CURDATE())
    GROUP BY MONTH(created_at)
");

foreach ($rows as $row) {
    $months[$row['m'] - 1] = (int)$row['total'];
}

$smarty->assign('months_json', json_encode($months));

// Năm được chọn (GET), mặc định năm hiện tại
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$years = $GLOBALS['sp']->getAll("
    SELECT DISTINCT YEAR(created_at) AS y
    FROM {$GLOBALS['db_sp']}.visit_logs
    ORDER BY y DESC
");

$smarty->assign('years', $years);
$smarty->assign('year', $year);
/////////
// === Thống kê theo tỉnh/thành phố Việt Nam ===
$region_stats = $GLOBALS['sp']->GetAll("
    SELECT region, COUNT(*) AS total
    FROM {$GLOBALS['db_sp']}.visit_logs
    WHERE country='Vietnam'
    GROUP BY region
    ORDER BY total DESC
");
$smarty->assign('region_stats', $region_stats);


$smarty->display("header.tpl");
$smarty->display("main/main.tpl");
$smarty->display("footer.tpl");
