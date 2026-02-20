<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$asset_id = strtoupper($_POST['asset_id']);
$asset_name = ucwords($_POST['asset_name']);
$rented = $_POST['rented'];
$cond = $_POST['cond'];
$type = $_POST['type'];
$location = $_POST['location'];
$update_id = $_POST['update_id'];

    mysqli_query($conn,"UPDATE tbl_asset_inv SET asset_name='$asset_name',asset_id='$asset_id',asset_rented='$rented',asset_type='$type',location='$location',cond='$cond' WHERE id = '$update_id'");
        
    $action = "UPDATED ASSET: ".$asset_id.' - '.$asset_name;
    $module = "ASSET INVENTORY";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=update';

// Redirect to the listing page
header("Location: mousetrap.php".$qstring);