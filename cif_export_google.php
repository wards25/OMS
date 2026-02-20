<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="CIF Data as of ' . date('m-d-Y h:i A') . '.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

fputcsv($output, array('SERIAL #','BUSINESS NAME','DELEGATE NAME','ADDRESS','PROVINCE','CITY','STATUS','APPLICATION DATE'));

$result = mysqli_query($conn,"SELECT * FROM tbl_customer ORDER BY application_date");

while($fetch_details = mysqli_fetch_array($result))
{	
	if($fetch_details['validated'] == 0){
		$status = "NOT VALIDATED";
	}else if($fetch_details['validated'] == 1){
		$status = "VALIDATED";
	}else{
		$status = "ENROLLED";
	}

	fputcsv($output, array($fetch_details['serial_no'],$fetch_details['business_name'],$fetch_details['delegate_name'],$fetch_details['address'],$fetch_details['province'],$fetch_details['city'],$status,$fetch_details['application_date']));
}

$action = $user." EXPORTED CUSTOMER INFO FILE CSV";
$module = "FORMS";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

fclose($output);

$conn->close();
?>