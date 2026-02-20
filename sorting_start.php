<?php
session_start();
include_once('dbconnect.php');

if($_GET['start'] == 1){

	$user = $_GET['sorter'];
	$tmno = $_GET['tmno'];
	$dtr = $_GET['dtr'];

	mysqli_query($conn,"UPDATE tbl_trips_tm SET sorter_start = NOW() WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='SORTING STARTED' WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='SORTING STARTED' WHERE tmno = '$tmno'");

	// Redirect to the listing page
	header("Location:sorting.php?status=start");

}else{

	$user = $_GET['sorter'];
	$tmno = $_GET['tmno'];
	$dtr = $_GET['dtr'];

	mysqli_query($conn,"UPDATE tbl_trips_tm SET sorter_end = NOW() WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='FOR LOADING' WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR LOADING' WHERE tmno = '$tmno'");

	// Redirect to the listing page
	header("Location:sorting.php?status=end");
}