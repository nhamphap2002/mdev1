<?php

$datenow = date('m/d/Y');
$date = date_create($datenow);
date_sub($date, date_interval_create_from_date_string("-1 days"));
$datenext = date_format($date, "m/d/Y");

$date = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
$isday = $date->format('l');
$timeend = $date->format($datenext . ' 11:59:59');
$start = $date->format('m/d/Y 11:59:59');
$timecur = $date->format('m/d/Y H:i:s');
echo json_encode(array('isday' => $isday, 'start' => $start, 'timeend' => $timeend, 'timecur' => $timecur));
