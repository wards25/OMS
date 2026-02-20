<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $ref_no = $_POST['ref_no'];
    $comment = ucfirst($_POST['comment']);
    $comment_date = date("Y-m-d");

    //insert data
    mysqli_query($conn,"INSERT INTO tbl_report_comment VALUES(NULL,'$ref_no','$comment','$user','$comment_date')");

    $action = "POSTED A COMMENT IN INCIDENT REPORT: ".$ref_no;
    $module = "INCIDENT REPORT";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '&status=comment';
}

// Redirect to that URL
header("Location: ".$_SERVER['HTTP_REFERER'].$qstring);