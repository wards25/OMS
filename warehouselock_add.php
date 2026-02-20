<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];
$url = $_POST['url'];

if (isset($_POST['submit']))
{
    $lock = ucwords($_POST['lockname']);
    $location = $_POST['location'];
    
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_location WHERE location_name = '$lock' AND location = '$location'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        mysqli_query($conn,"INSERT INTO tbl_warehouselock_location VALUES (NULL,'$lock','1','$location','0')");

        // insert history
        $action = "ADDED WAREHOUSE LOCK LOCATION: ".$lock;
        $module = "WAREHOUSE LOCK";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';
        $qstring2 = '&status=add';

    }else{
        $qstring = '?status=err';
        $qstring2 = '&status=err';
    }
}  

    if(empty($_POST['url'])){
        // Redirect to the listing page
        header("Location: warehouselock.php".$qstring);
    }else{
        // Redirect to the listing page
        header("Location:".$url.$qstring2);
    }