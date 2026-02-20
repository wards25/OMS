<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$id = $_POST['cancel_id'];
$form_no = $_POST['form_no'];
$type = $_POST['type'];
$url = $_POST['url'];

mysqli_query($conn,"UPDATE tbl_variance_ref SET status='0' WHERE id = '$id'");
mysqli_query($conn,"UPDATE tbl_variance_raw SET status='0' WHERE form_no = '$form_no'");

$action = "CANCELLED ".$type." FORM: ".$form_no;
$module = $type." VARIANCE FORM";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

// Redirect to that URL
header("Location: ".$url."?status=cancel");