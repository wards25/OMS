<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];
$location = $_GET['location'];


if($_GET['type'] == 'raw'){
	//mime type
	header('Content-Type: text/csv');
	//tell browser what's the file name
	header('Content-Disposition: attachment; filename="'.$location.' Stock Summary as of ' . date('m-d-Y h:i A') . '.csv"');
	//no cache
	header('Cache-Control: max-age=0');

	$output = fopen('php://output', 'w');

	fputcsv($output, array('LOCATION','RACK','RACK LOCATION','COMPANY','PRINCIPAL','SKU','DESCRIPTION','QTY','UOM','BBD','STATUS','UPDATED BY','UPDATE DATE'));

	$result = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE location = '$location' ORDER BY principal ASC, qty ASC, bbd ASC");

	while($row = mysqli_fetch_array($result))
	{
		fputcsv($output, array($row['location'],$row['rack'],$row['racklocation'],$row['company'],$row['principal'],$row['sku'],$row['description'],$row['qty'],$row['uom'],$row['bbd'],$row['status'],$row['submit_by'],$row['submit_date']));
	}

	$action = $user." EXPORTED STOCK RAW FILE CSV";
	$module = "STOCKS";
	mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

}else if ($_GET['type'] == '3months'){
	//mime type
	header('Content-Type: text/csv');
	//tell browser what's the file name
	header('Content-Disposition: attachment; filename="'.$location.' 3 Months Below Stocks as of ' . date('m-d-Y h:i A') . '.csv"');
	//no cache
	header('Cache-Control: max-age=0');

	$output = fopen('php://output', 'w');

	fputcsv($output, array('LOCATION','RACK LOCATION','COMPANY','PRINCIPAL','SKU','DESCRIPTION','QTY','UOM','REMAINING DAYS','BBD','STATUS','UPDATED BY','UPDATE DATE'));

	$today = date("Y-m-d");
	$three_months_later = date("Y-m-d", strtotime("+3 months"));

	$result = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE location = '$location' AND bbd BETWEEN '$today' AND '$three_months_later' ORDER BY principal ASC, qty ASC, bbd ASC");

	while($row = mysqli_fetch_array($result))
	{
	    $bbd_raw = $row['bbd'];
	    $bbd = date("Y-m-d", strtotime($bbd_raw));
	    
	    // Calculate remaining time
	    $date_today = new DateTime($today);
	    $date_bbd = new DateTime($bbd);
	    $interval = $date_today->diff($date_bbd);

	    // Format as "X months, Y days"
	    $remaining_days = $interval->format('%m Month(s) %d Day(s)');

	    fputcsv($output, array(
	        $row['location'],
	        $row['racklocation'],
	        $row['company'],
	        $row['principal'],
	        $row['sku'],
	        $row['description'],
	        $row['qty'],
	        $row['uom'],
	        $remaining_days,
	        $bbd,
	        $row['status'],
	        $row['submit_by'],
	        $row['submit_date']
	    ));
	}

	$action = $user." EXPORTED 3 MONTHS BELOW RAW FILE CSV";
	$module = "STOCKS";
	mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

}else if ($_GET['type'] == '6months'){
	//mime type
	header('Content-Type: text/csv');
	//tell browser what's the file name
	header('Content-Disposition: attachment; filename="'.$location.' 6 Months Below Stocks as of ' . date('m-d-Y h:i A') . '.csv"');
	//no cache
	header('Cache-Control: max-age=0');

	$output = fopen('php://output', 'w');

	fputcsv($output, array('LOCATION','RACK LOCATION','COMPANY','PRINCIPAL','SKU','DESCRIPTION','QTY','UOM','REMAINING DAYS','BBD','STATUS','UPDATED BY','UPDATE DATE'));

	$today = date("Y-m-d");
	$three_months_later = date("Y-m-d", strtotime("+6 months"));

	$result = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE location = '$location' AND bbd BETWEEN '$today' AND '$three_months_later' ORDER BY principal ASC, qty ASC, bbd ASC");

	while($row = mysqli_fetch_array($result))
	{
	    $bbd_raw = $row['bbd'];
	    $bbd = date("Y-m-d", strtotime($bbd_raw));
	    
	    // Calculate remaining time
	    $date_today = new DateTime($today);
	    $date_bbd = new DateTime($bbd);
	    $interval = $date_today->diff($date_bbd);

	    // Format as "X months, Y days"
	    $remaining_days = $interval->format('%m Month(s) %d Day(s)');

	    fputcsv($output, array(
	        $row['location'],
	        $row['racklocation'],
	        $row['company'],
	        $row['principal'],
	        $row['sku'],
	        $row['description'],
	        $row['qty'],
	        $row['uom'],
	        $remaining_days,
	        $bbd,
	        $row['status'],
	        $row['submit_by'],
	        $row['submit_date']
	    ));
	}

	$action = $user." EXPORTED 6 MONTHS BELOW RAW FILE CSV";
	$module = "STOCKS";
	mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
	
}else if ($_GET['type'] == 'empty'){
	//mime type
	header('Content-Type: text/csv');
	//tell browser what's the file name
	header('Content-Disposition: attachment; filename="'.$location.' Empty Racks as of ' . date('m-d-Y h:i A') . '.csv"');
	//no cache
	header('Cache-Control: max-age=0');

	$output = fopen('php://output', 'w');

	fputcsv($output, array('LOCATION','RACK','COL','LEVEL','POS','RACK LOCATION','STATUS'));

	$result = mysqli_query($conn, "SELECT * FROM tbl_inventory_rack WHERE location = '$location' AND (sku = 'NO SKU' OR sku = '') ORDER BY racklocation ASC");

	while($row = mysqli_fetch_array($result))
	{
		fputcsv($output, array($row['location'],$row['rack'],$row['col'],$row['level'],$row['pos'],$row['racklocation'],'EMPTY'));
	}

	$action = $user." EXPORTED EMPTY RACKS RAW FILE CSV";
	$module = "STOCKS";
	mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
	
}else{

}

fclose($output);

$conn->close();
?>