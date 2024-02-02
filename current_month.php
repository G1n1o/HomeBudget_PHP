<?php
session_start();

$currentDate = new DateTime('now');
$currentDate->setDate($currentDate->format('Y'), $currentDate->format('m'), 1);

$_SESSION['date_begin'] = $currentDate->format('Y-m-d');
$_SESSION['date_end'] = $currentDate->format('Y-m-t');
$_SESSION['title'] = 'Bieżący miesiąc';

header('Location: balance.php');
exit();