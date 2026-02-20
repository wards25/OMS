<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$description = ucwords($_POST['description']);
$facility_id = $_POST['facility_id'];

    $facility_query = mysqli_query($conn,"SELECT * FROM tbl_facility_location WHERE id = '$facility_id'");
    $fetch_facility = mysqli_fetch_array($facility_query);
    $location = $fetch_facility['location'];

    mysqli_query($conn,"INSERT INTO tbl_facility_description VALUES(NULL,'$facility_id','$description',1,'$location')");
        
    $action = "ADDED FACILITY DESCRIPTION: ".$facility_id.' - '.$description;
    $module = "GENERAL FACILITIES";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=description';

// Redirect to the listing page
header("Location: facility.php".$qstring);