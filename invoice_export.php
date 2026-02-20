<?php 
session_start();
include('dbconnect.php');

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="Invoice Report as of '.date('m-d-Y').'.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

fputcsv($output, array('SI #','SI DATE','PO #','PO DATE','SO #','SO DATE','DRIVER','PLATE #','TM #','CODE','BRANCH','FRANCHISE','REGION','CLUSTER','COMPANY','LOCATION','DATE UPLOAD','DATE DELIVERED','DATE CLEARED','DATE OKDEL','DATE RECEIVED','DATE COUNTERED','DATE BILLED','CLEARED REMARKS','STATUS'));

$from = date("Y-m-d",strtotime($_POST['from']));
$to = date("Y-m-d",strtotime($_POST['to']));

$result	= mysqli_query($conn,"SELECT * FROM dbdatabase WHERE dateuploaded BETWEEN '$from' AND '$to' ORDER BY invoicenumber ASC ");

while($row = mysqli_fetch_array($result))
{
	fputcsv($output, array($row['invoicenumber'],$row['invoiceddate'],$row['ponumber'],$row['podate'],$row['sonumber'],$row['sodate'],$row['drivername'],$row['platenumber'],$row['tmnumber'],$row['brcode'],$row['branchname'],$row['franchise'],$row['region'],$row['cluster'],$row['company'],$row['location'],$row['dateuploaded'],$row['datedelivered'],$row['datecleared'],$row['dateokdel'],$row['datereceived'],$row['datecountered'],$row['datebilled'],$row['clearedremarks'],$row['status']));
}

fclose($output);

$conn->close();
?>