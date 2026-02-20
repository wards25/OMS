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

fputcsv($output, array('SERIAL #','BUSINESS NAME','DELEGATE NAME','ADDRESS','CONTACT NO','TIN NO','BLDG NAME','STREET','PROVINCE','CITY','BRGY','ZIP CODE','EMAIL','TELEPHONE','ADDRESS 2','ADDRESS 3','DELIVERY SCHED','HOURS FROM','HOURS TO','AUTHORIZED NAME','AUTHORIZED POSITION','AUTHORIZED CONTACT','SIGNATORY','SIGNATORY POSITION','SIGNATORY CONTACT','IS VALIDATED','APPLICATION DATE'));

$result = mysqli_query($conn,"SELECT * FROM tbl_customer WHERE tag = '$form' AND application_date BETWEEN '$from' AND '$to' ORDER BY application_date");

while($fetch_details = mysqli_fetch_array($result))
{
	fputcsv($output, array($fetch_details['serial_no'],$fetch_details['business_name'],$fetch_details['delegate_name'],$fetch_details['address'],$fetch_details['contact'],$fetch_details['tin_no'],$fetch_details['bldg_name'],$fetch_details['street'],$fetch_details['province'],$fetch_details['city'],$fetch_details['brgy'],$fetch_details['zip_code'],$fetch_details['email'],$fetch_details['telephone'],$fetch_details['del_address2'],$fetch_details['del_address3'],$fetch_details['delivery_sched'],$fetch_details['hours_from'],$fetch_details['hours_to'],$fetch_details['authorized_name'],$fetch_details['authorized_pos'],$fetch_details['authorized_contact'],$fetch_details['signatory1_name'],$fetch_details['signatory1_pos'],$fetch_details['signatory1_contact'],$fetch_details['validated'],$fetch_details['application_date']));
}

$action = $user." EXPORTED ".$form." FILE CSV";
$module = "FORMS";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

fclose($output);

$conn->close();
?>