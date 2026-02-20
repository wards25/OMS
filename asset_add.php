<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_POST['url'];

if (isset($_POST['submit']))
{
    $asset_id = strtoupper($_POST['asset_id']);
    $asset_name = ucwords($_POST['asset_name']);
    $rented = $_POST['rented'];
    $cond = $_POST['cond'];
    $type = $_POST['type'];
    $location = $_POST['location'];

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_id = '$asset_id'");
    $check_count = mysqli_fetch_array($check_query);

    if($check_count < 1){

        mysqli_query($conn,"INSERT INTO tbl_asset_inv VALUES (NULL,'$asset_name','$asset_id','$rented','$type','$location','$cond','0')");
            
        $action = "ADDED ASSET: ".$asset_id.' - '.$asset_name;
        $module = "ASSET INVENTORY";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';
        $qstring2 = '&status=add';

    }else{
        $qstring = '?status=err';
        $qstring2 = '&status=err';
    }
}  

    if(empty($_POST['url'])){
        // Redirect to the listing page
        header("Location: asset.php".$qstring);
    }else{
        // Redirect to the listing page
        header("Location:".$url.$qstring2);
    }