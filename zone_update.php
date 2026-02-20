<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$location_name = ucwords($_POST['location_name']);
$cond = $_POST['cond'];
$description = ucwords($_POST['description']);
$location = $_POST['location'];
$update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_zone_location SET location_name='$location_name',description='$description',is_active='$cond',location='$location' WHERE id = '$update_id'");
        
    $action = "UPDATED WAREHOUSE ZONE LOCATION: ".$location_name;
    $module = "WAREHOUSE ZONE";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

// Redirect to the listing page
header("Location: zone.php".$qstring);