<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['update']))
{
    $metricname = ucwords($_POST['metricname']);
    $update_id = $_POST['update_id'];
    $success = $_POST['success'];
    $warning = $_POST['warning'];
    $danger = $_POST['danger'];
    $location = $_POST['location'];

    mysqli_query($conn,"UPDATE tbl_kpi SET metric='$metricname',success='$success',warning='$warning',danger='$danger',location='$location' WHERE id = '$update_id'");

    // insert history
    $action = "UPDATED KPI: ".$metricname;
    $module = "KPI MAP";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

}else{
    $qstring = '?status=err';
}  

// Redirect to the listing page
header("Location: kpi.php".$qstring);