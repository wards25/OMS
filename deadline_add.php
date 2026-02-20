<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['submit']))
{
    $submit_type = $_POST['submit_type'];
    $location = $_POST['deadline_loc'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_deadline WHERE shift = '$shift' AND shift_type = '$shift_type' AND submit_type = '$submit_type' AND location = '$location'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        mysqli_query($conn,"INSERT INTO tbl_deadline VALUES (NULL,'$submit_type','$shift','$shift_type','$time_from','$time_to','$location')");

        // insert history
        $action = "ADDED DEADLINE LOCATION: ".$location;
        $module = "DEADLINE SETTING";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';

    }else{
        $qstring = '?status=err';
    }

}else{

}    

// Redirect to the listing page
header("Location: deadline.php".$qstring);