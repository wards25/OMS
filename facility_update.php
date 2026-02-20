<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$location_name = ucwords($_POST['location_name']);
$cond = $_POST['cond'];
$location = $_POST['location'];
$update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_facility_location SET location_name='$location_name',is_active='$cond',location='$location' WHERE id = '$update_id'");
        
    $action = "UPDATED FACILITY LOCATION: ".$asset_id.' - '.$asset_name;
    $module = "GENERAL FACILITIES";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

// Redirect to the listing page
header("Location: facility.php".$qstring);