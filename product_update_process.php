<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['update'])){

  $update_id = $_POST['update_id'];
  $sku = ucwords(mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $_POST['sku']))));
  $uom = strtoupper($_POST['uom']);
  $itemcode = $_POST['itemcode'];
  $itembarcode = $_POST['itembarcode'];
  $barcode = $_POST['barcode'];
  $vendorcode = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '',$_POST['vendorcode'])));
  $principal = $_POST['principal'];
  $status = $_POST['status'];
  $racklocation = $_POST['racklocation'];
  // $racklocation = '';
  $cs = $_POST['cs'];
  $ib = $_POST['ib'];

  mysqli_query($conn, "UPDATE tbl_product SET search_code='$itemcode', itemcode='$itemcode', itembarcode='$itembarcode', barcode='$barcode', vendorcode='$vendorcode', description='$sku', principal='$principal', percase='$cs', perserving='$ib', uom='$uom', racklocation='$racklocation', is_active='$status' WHERE id = '$update_id'");

  // insert login history
  $action = "UPDATED SKU: ".$itemcode.' - '.$sku;
  $module = "PRODUCT LIST";
  mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

  $qstring = '?status=update';
  header("Location: product.php".$qstring);

}else{
    
}