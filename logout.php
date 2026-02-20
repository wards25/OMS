<?php
session_start();
include_once 'dbconnect.php';
$user = $_SESSION['name'];

if(!isset($_SESSION['id']))
{
	header("Location: index.php");
}
else if(isset($_SESSION['id']))
{
	header("Location: dashboard.php");
}

if(isset($_GET['logout']))
{
	$action = $user." HAS LOGGED OUT";
    $module = "IN AND OUT";
    mysqli_query($conn,"INSERT INTO tbl_history VALUES (NULL,'$user','$action','$module','$client_ip','$mac','$device','$model',NOW())");
    mysqli_query($conn,"DELETE FROM tbl_sessions WHERE user_id=".$_SESSION['id']);
    mysqli_query($conn,"DELETE FROM tbl_sessions WHERE ip_address='$client_ip'");

	session_destroy();
	unset($_SESSION['id']);
	unset($_SESSION['username']);
	header("Location: login.php");
}
?>