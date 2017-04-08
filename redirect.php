<?php
session_start();
$_SESSION['host'] =  $_POST["ip"];
$_SESSION['port'] = (int) $_POST["port"];
$_SESSION['command'] = $_POST['command'];
header("Location: connect.php"); 
exit();
?>