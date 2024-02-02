<?php
session_start();

$currentDate = new DateTime('now');
$currentDate->setDate($currentDate->format('Y'), $currentDate->format('m'), 1);
$currentDate->modify('-1 month');

$_SESSION['date_begin'] = $currentDate->format('Y-m-d');
$_SESSION['date_end'] = $currentDate->format('Y-m-t');
$_SESSION['title'] = 'Poprzedni miesiąc';

header('Location: balance.php');
exit();

