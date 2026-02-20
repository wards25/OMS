<?php
session_start();
include_once('dbconnect.php');

$user = $_GET['checker'];
$picklistno = $_GET['picklistno'];
$dtr = $_GET['dtr'];

$start_query = mysqli_query($conn,"SELECT checker_start FROM tbl_trips_picklist WHERE checker = '$user' AND picklistno = '$picklistno' AND dtr = '$dtr'");
$fetch_start = mysqli_fetch_assoc($start_query);

if(empty($fetch_start['checker_start'])){
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='CHECKING STARTED',checker_start = NOW() WHERE checker = '$user' AND picklistno = '$picklistno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='CHECKING STARTED' WHERE picklistno = '$picklistno'");
}else{

}

// Redirect to the listing page
header("Location:checking_list.php?checker=".$user."&picklistno=".$picklistno."&dtr=".$dtr);