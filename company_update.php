<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['update']))
{
    $company = strtoupper($_POST['company']);
    $short_name = strtoupper($_POST['shortname']);
    $vendorcode = $_POST['vendorcode'];
    $status = $_POST['status'];
    $update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_company SET company='$company',short_name='$short_name',vendorcode='$vendorcode',is_active='$status' WHERE id = '$update_id'");

    // insert history
    $action = "UPDATED COMPANY: ".$company;
    $module = "COMPANY SETTING";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

}else{
    $qstring = '?status=err';
}  

// Redirect to the listing page
header("Location: company.php".$qstring);