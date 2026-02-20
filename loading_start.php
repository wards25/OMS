<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if($_GET['start'] == 1){

	$tmno = $_GET['tmno'];
	$dtr = $_GET['dtr'];

	mysqli_query($conn,"UPDATE tbl_trips_tm SET ic = '$user', ic_start = NOW() WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='LOADING STARTED' WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='LOADING STARTED' WHERE tmno = '$tmno'");

	// Redirect to the listing page
	header("Location:loading.php?status=succ");

}else{

	$tmno = $_GET['tmno'];
	$dtr = $_GET['dtr'];

	mysqli_query($conn,"UPDATE tbl_trips_tm SET ic_end = NOW() WHERE ic = '$user' AND tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='FOR DISPATCH' WHERE tmno = '$tmno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR DISPATCH' WHERE tmno = '$tmno'");

	// Redirect to the listing page
	header("Location:loading.php?status=end");
}