<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $id = $_POST['id'];
    $resolution = $_POST['resolution'];
    $resolve_date = date("Y-m-d");

    // check if already resolved
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE id = '$id'");
    $row = mysqli_fetch_array($check_query);

    if ($row['status'] == 1)
    {
        $qstring = '?status=err';
    }
    else
    {

        //update selected tables
        mysqli_query($conn,"UPDATE tbl_report_raw SET resolve_date='$resolve_date',resolved_by='$user',resolution='$resolution',status=1 WHERE id = '$id'");

        $table = $row['table_name'];
        $module_id = $row['module_id'];

        //update selected tables
        mysqli_query($conn,"UPDATE $table SET report=report-1 WHERE id = '$module_id'");

        $action = "RESOLVED INCIDENT REPORT: ".$row['ref_no'];
        $module = "INCIDENT REPORT";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?x=1&status=irsucc';
    }
}

// Redirect to the listing page
if (isset($_SESSION['previous_pages'][0])) {
    // Get the URL of the page two steps back
    $url = $_SESSION['previous_pages'][0];

    // Redirect to that URL
    header("Location: ".$url.$qstring);
    exit();
} else {
    // No previous pages stored
    echo "No previous pages to go back to.";
}