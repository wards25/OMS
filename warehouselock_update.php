<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$location_name = ucwords($_POST['location_name']);
$cond = $_POST['cond'];
$location = $_POST['location'];
$update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_warehouselock_location SET location_name='$location_name',is_active='$cond',location='$location' WHERE id = '$update_id'");
        
    $action = "UPDATED WAREHOUSE LOCK: ".$asset_id.' - '.$asset_name;
    $module = "WAREHOUSE LOCK";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

// Redirect to the listing page
header("Location: warehouselock.php".$qstring);