<?php
session_start();
include_once("dbconnect.php");

$id = $_POST['id'];

mysqli_query($conn,"DELETE FROM tbl_report_involved WHERE id = '$id'");