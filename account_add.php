<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$fname = ucwords($_POST['fname']);
$lname = ucwords($_POST['lname']);
$username = $_POST['username'];
$password = md5($_POST['password']);
$role = $_POST['role'];
$user_status = $_POST['user_status'];
$tag = $_POST['tag'];
$hub = $_POST['hub'];

$check_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE username = '$username'");
$check_count = mysqli_fetch_array($check_query);

if($check_count < 1){

    mysqli_query($conn,"INSERT INTO tbl_users VALUES (NULL,'$username','$password','$fname','$lname','$tag','$role','$user_status','$hub')");

    $status = $_POST['status'];
    foreach ($status as $id => $loc_status) {
        
        if($loc_status >= 1){

            $db_id = $_POST['id'][$id];
            $db_id = mysqli_real_escape_string($conn, $db_id);
            $id_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE username = '$username'");
            $fetch_id = mysqli_fetch_array($id_query);
            $id = $fetch_id['id'];

            mysqli_query($conn,"INSERT INTO tbl_user_locations VALUES (NULL,'$id','$db_id')");
        }
    }
        
        $action = "ADDED USER: ".$fname.' '.$lname;
        $module = "USER ACCOUNT";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
}else{
    $qstring = '?status=err';
}

// Redirect to the listing page
header("Location: account.php".$qstring);