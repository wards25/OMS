<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $month = strtoupper($_POST['month']);
    $date = date("Y-m-d");

    // check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_pallets_raw WHERE month='$month' AND location='$location'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {
        $good_pallet = $_POST['good_pallet'];

        if(empty($_POST['for_repair'])){
            $for_repair = 0;
        }else{
            $for_repair = $_POST['for_repair']; 
        }
        
        if(empty($_POST['missing'])){
            $missing = 0;
        }else{
            $missing = $_POST['missing'];
        }

        if(empty($_POST['others'])){
            $others = 0;
        }else{
            $others = $_POST['others'];
        }
        
        if(empty($_POST['remarks'])){
            $remarks = '';
        }else{
            $remarks = $_POST['remarks'];
        }

        mysqli_query($conn,"INSERT INTO tbl_pallets_raw (id,date,location,good_pallets,for_repair,missing,others,remarks,submitted_by,submitted_at,is_validated,validated_by,validated_at,month) VALUES (NULL,'$date','$location','$good_pallet','$for_repair','$missing','$others','$remarks','$user',NOW(),'0','','','$month') ");

        $action = "SUBMITTED INVENTORY PALLET COUNT";
        $module = "INVENTORY";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: inventory_pallet.php".$qstring);