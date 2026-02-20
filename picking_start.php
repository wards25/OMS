<?php
session_start();
include_once('dbconnect.php');

$user = $_GET['picker'];
$picklistno = $_GET['picklistno'];
$dtr = $_GET['dtr'];

$start_query = mysqli_query($conn,"SELECT picker_start FROM tbl_trips_picklist WHERE picker = '$user' AND picklistno = '$picklistno' AND dtr = '$dtr'");
$fetch_start = mysqli_fetch_assoc($start_query);

if(empty($fetch_start['picker_start'])){
	mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='PICKING STARTED',picker_start = NOW() WHERE picker = '$user' AND picklistno = '$picklistno' AND dtr = '$dtr'");
	mysqli_query($conn,"UPDATE tbl_trips_raw SET status='PICKING STARTED' WHERE picklistno = '$picklistno'");
}else{

}

// Redirect to the listing page
header("Location:picking_list.php?picker=".$user."&picklistno=".$picklistno."&dtr=".$dtr);