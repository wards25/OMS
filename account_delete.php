<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$account = $_POST['account'];
$delete_id = $_POST['delete_id'];

mysqli_query($conn,"DELETE FROM tbl_users WHERE id = '$delete_id'");
mysqli_query($conn,"DELETE FROM tbl_system_permissions WHERE user_id = '$delete_id'");
mysqli_query($conn,"DELETE FROM tbl_user_locations WHERE user_id = '$delete_id'");

$action = "DELETED USER: ".$account;
$module = "USER ACCOUNT";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$qstring = '?status=delete';

// Redirect to the listing page
header("Location: account.php".$qstring);