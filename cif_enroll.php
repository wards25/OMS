<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$link = $_POST['link'];
$serial_no = $_POST['serial_no'];

mysqli_query($conn,"UPDATE tbl_customer SET validated='2' WHERE serial_no='$serial_no'");

$action = "ENROLLED: CIF ".$serial_no;
$module = "FORMS";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$qstring = '?status=enrolled';

// Redirect to the listing page
header("Location: ".$link.$qstring);