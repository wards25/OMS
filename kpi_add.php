<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['submit']))
{
    $location = strtoupper($_POST['location']);

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = '$metricname'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        $success = $_POST['success'];
        $warning = $_POST['warning'];
        $danger = $_POST['danger'];
        $location = $_POST['location'];

        mysqli_query($conn,"INSERT INTO tbl_kpi VALUES (NULL,'$metricname','$success','$warning','$danger','$location')");

        // insert history
        $action = "ADDED KPI: ".$metricname;
        $module = "KPI MAP";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';

    }else{
        $qstring = '?status=err';
    }

}else{

}    

// Redirect to the listing page
header("Location: kpi.php".$qstring);