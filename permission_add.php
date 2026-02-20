<?php
session_start();
include_once("dbconnect.php");
$update_id = $_SESSION['update_id'];

$id = $_POST['id'];

mysqli_query($conn,"INSERT INTO tbl_system_permissions(id,user_id,permission_id) VALUES(NULL,'$update_id','$id')");