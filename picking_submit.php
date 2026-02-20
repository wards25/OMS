<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$picker = mysqli_real_escape_string($conn,$_POST['picker']);
$picklistno = mysqli_real_escape_string($conn,$_POST['picklistno']);
$dtr = mysqli_real_escape_string($conn,$_POST['dtr']);

$sku_values = $_POST['sku'];
foreach ($sku_values as $id => $sku)
{   
    $pickerqty = $_POST['pickerqty'][$id];

    // Sanitize the input values
    $sku_name = mysqli_real_escape_string($conn, $sku);
    $pickerqty = mysqli_real_escape_string($conn, $pickerqty);
    
    mysqli_query($conn,"UPDATE tbl_trips_picking SET picker='$picker',pickerqty='$pickerqty' WHERE picklistno='$picklistno' AND sku='$sku' AND picker='$picker' AND dtr='$dtr'");
}

mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR CHECKING' WHERE picklistno='$picklistno'");
mysqli_query($conn,"UPDATE tbl_trips_picklist SET picker_end=NOW(),status='FOR CHECKING' WHERE picklistno='$picklistno' AND picker='$picker' AND dtr='$dtr'");

$action = "PICKED PICKLIST NO: ".$picklistno;
$module = "TRIPS PICKING";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$qstring = '?status=succ';

$data = array('status' => 'success');
echo json_encode($data);

// Redirect to the listing page
// header("Location: picking.php".$qstring);