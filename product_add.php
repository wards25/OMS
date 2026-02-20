<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['submit']))
{
    $sku = ucwords(mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $_POST['sku']))));
    $uom = strtoupper(str_replace([' ', ','], '', $_POST['uom']));
    $itemcode = str_replace([' ', ','], '', $_POST['itemcode']);
    $itembarcode = str_replace([' ', ','], '', $_POST['itembarcode']);
    $barcode = str_replace([' ', ','], '', $_POST['barcode']);
    $vendorcode = $_POST['vendorcode'];
    $principal = $_POST['principal'];
    $itemcode = strtoupper($itemcode);

    if(empty($_POST['racklocation'])){
        $racklocation = '';
    }else{
        $racklocation = str_replace([' ', ','], '', $_POST['racklocation']);
    }

    $status = $_POST['status'];
    $cs = str_replace([' ', ','], '', $_POST['cs']);
    $ib = str_replace([' ', ','], '', $_POST['ib']);

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$itemcode'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        mysqli_query($conn,"INSERT INTO tbl_product VALUES (NULL,'$itemcode','$itemcode','$itembarcode','$barcode','$vendorcode','$sku','$principal','$cs','$ib','$uom','$racklocation','$status')");

        // insert history
        $action = "ADDED SKU: ".$itemcode.' - '.$sku;
        $module = "PRODUCT LIST";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';

    }else{
        $qstring = '?status=err';
    }

}else{

}    

// Redirect to the listing page
header("Location: product.php".$qstring);