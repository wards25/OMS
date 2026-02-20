<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];

$from = date("Y-m-d", strtotime($_POST['from']));
$to = date("Y-m-d", strtotime($_POST['to']));

$tbl_query = mysqli_query($conn,"SELECT * FROM tbl_export WHERE user = '$user' GROUP BY tbl_name");
$fetch_tbl = mysqli_fetch_array($tbl_query);
$tbl_name = $fetch_tbl['tbl_name'];

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="'.$tbl_name.' Data '.date('m-d-Y').'.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');
$header = array();

$export_query = mysqli_query($conn,"SELECT * FROM tbl_export WHERE order_no > 0 AND user = '$user' ORDER BY order_no ASC");
while($fetch_export = mysqli_fetch_array($export_query)){
	$header[] = $fetch_export['col_name'];
	$col_name = implode(',', $header);
}
	fputcsv($output,$header);
	
	if($from == '' || $to == ''){
		$column_query = mysqli_query($conn,"SELECT $col_name,date FROM $tbl_name WHERE date BETWEEN '$from' AND '$to'");
	}else{
		$column_query = mysqli_query($conn,"SELECT $col_name FROM $tbl_name");
	}
	
	while($fetch_column = mysqli_fetch_assoc($column_query)){
		fputcsv($output,$fetch_column);
	}

fclose($output);
	
	//record history
	$action = "EXPORTED ".$tbl_name;
    $module = "EXPORT";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$conn->close();
?>