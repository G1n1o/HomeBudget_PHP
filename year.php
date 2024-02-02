<?php
session_start();

$currentDate = new DateTime('now');
$currentDate->setDate($currentDate->format('Y'), 1, 1);


$_SESSION['date_begin'] = $currentDate->format('Y-m-d');

$currentDate->modify('last day of December ' . $currentDate->format('Y'));

$_SESSION['date_end'] = $currentDate->format('Y-m-t');
$_SESSION['title'] = 'Obecny rok';

header('Location: balance.php');
exit();

