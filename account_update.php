<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$fname = ucwords($_POST['fname']);
$lname = ucwords($_POST['lname']);
$username = $_POST['username'];
$role = $_POST['role'];
$user_status = $_POST['user_status'];
$tag = $_POST['tag'];
$hub = $_POST['hub'];
$update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_users SET username='$username',fname='$fname',lname='$lname',tag='$tag',role_id='$role',is_active='$user_status',hub='$hub' WHERE id='$update_id'");
    mysqli_query($conn,"DELETE FROM tbl_user_locations WHERE user_id = '$update_id'");

    $status = $_POST['status'];
    foreach ($status as $id => $loc_status) {
        
        if($loc_status >= 1){

            $db_id = $_POST['id'][$id];
            $db_id = mysqli_real_escape_string($conn, $db_id);

            mysqli_query($conn,"INSERT INTO tbl_user_locations VALUES (NULL,'$update_id','$db_id')");
        }
    }
    
    $action = "UPDATED USER: ".$fname.' '.$lname;
    $module = "USER ACCOUNT";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

// Redirect to the listing page
header("Location: account.php".$qstring);