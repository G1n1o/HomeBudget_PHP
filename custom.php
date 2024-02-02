<?php
session_start();




$_SESSION['date_begin'] = $_POST['begin'];
$_SESSION['date_end'] = $_POST['end'];
$_SESSION['title'] = "od " . $_POST['begin'] . " do " . $_POST['end'];

header('Location: balance.php');
exit();

