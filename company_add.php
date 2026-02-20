<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['submit']))
{
    $company = strtoupper($_POST['company']);
    $vendorcode = $_POST['vendorcode'];

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_company WHERE company = '$company' OR vendorcode = '$vendorcode'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        $short_name = strtoupper($_POST['shortname']);
        $status = $_POST['status'];

        mysqli_query($conn,"INSERT INTO tbl_company VALUES (NULL,'$company','$short_name','$vendorcode','$status')");

        // insert history
        $action = "ADDED COMPANY: ".$company;
        $module = "COMPANY SETTING";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';

    }else{
        $qstring = '?status=err';
    }

}else{

}    

// Redirect to the listing page
header("Location: company.php".$qstring);