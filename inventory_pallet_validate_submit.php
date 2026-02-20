<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $month = strtoupper($_POST['month']);
    $date = date("Y-m-d");

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

    mysqli_query($conn,"UPDATE tbl_pallets_raw SET good_pallets='$good_pallet',for_repair='$for_repair',missing='$missing',others='$others',remarks='$remarks',is_validated='1',validated_by='$user',validated_at=NOW() WHERE month='$month' AND location='$location'");

    $action = "VALIDATED INVENTORY PALLET COUNT";
    $module = "INVENTORY";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=validate';
}

// Redirect to the listing page
header("Location: inventory_pallet.php".$qstring);