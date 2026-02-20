<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];

$from = date("Y-m-d", strtotime($_POST['from']));
$to = date("Y-m-d", strtotime($_POST['to']));
$form = $_GET['form'];

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="'.$form.' From '.$from.' To '.$to.'.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

fputcsv($output, array('FORM #','PO #','INVOICE #','DATE','ERROR TYPE','INVOICED SKU','INVOICED DESC','INVOICED QTY','PICKED SKU','PICKED DESC','PICKED QTY','UOM','VARIANCE QTY','RETURN QTY','PICKER','CHECKER','DRIVER','HELPER','NEW DRIVER','NEW HELPER','LOCATION','SUBMIT BY','SUBMIT DATE'));

$result = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_type = '$form' AND date BETWEEN '$from' AND '$to' ORDER BY date");

while($fetch_details = mysqli_fetch_array($result))
{
	$form_no = $fetch_details['form_no'];
    $details_query = mysqli_query($conn,"SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");

    while($row = mysqli_fetch_assoc($details_query)){

		fputcsv($output, array($fetch_details['form_no'],$fetch_details['po_no'],$fetch_details['invoice_no'],$fetch_details['date'],$row['error_type'],$row['invoiced_sku'],$row['invoiced_desc'],$row['invoiced_qty'],$row['picked_sku'],$row['picked_desc'],$row['picked_qty'],$row['uom'],$row['qty'],$row['return_qty'],$fetch_details['picker_name'],$fetch_details['checker_name'],$fetch_details['driver_name'],$fetch_details['helper_name'],$fetch_details['new_driver'],$fetch_details['new_helper'],$fetch_details['location'],$fetch_details['submit_by'],$fetch_details['dtr']));
	}
}

$action = $user." EXPORTED ".$form." FILE CSV";
$module = "FORMS";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

fclose($output);

$conn->close();
?>