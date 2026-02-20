<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['update']))
{
    $submit_type = $_POST['submit_type'];
    $location = $_POST['deadline_loc'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];
    $update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_deadline SET submit_type='$submit_type',shift='$shift',shift_type='$shift_type',time_from='$time_from',time_to='$time_to',location='$location' WHERE id = '$update_id'");

    // insert history
    $action = "UPDATED DEADLINE LOCATION: ".$location;
    $module = "DEADLINE SETTING";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

}else{
    $qstring = '?status=err';
}  

// Redirect to the listing page
header("Location: deadline.php".$qstring);