<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$id = $_POST['id'];

$list_query = mysqli_query($conn,"SELECT * FROM tbl_export WHERE user = '$user' AND id = '$id'");
$fetch_list = mysqli_fetch_array($list_query);

	$list = $fetch_list['list'];

	if($list == 1){
		
		mysqli_query($conn,"UPDATE tbl_export SET list = 2 WHERE user = '$user' AND id = '$id'");

		$order_query = mysqli_query($conn,"SELECT * FROM tbl_export WHERE order_no > 0 AND user = '$user'");
		$fetch_order = mysqli_num_rows($order_query);

		if($fetch_order == 0){
			mysqli_query($conn,"UPDATE tbl_export SET order_no = 1 WHERE user = '$user' AND id = '$id'");
		}else{
			$max_query = mysqli_query($conn,"SELECT MAX(order_no) FROM tbl_export WHERE user = '$user'");
			$fetch_max = mysqli_fetch_array($max_query);
			$max = $fetch_max['MAX(order_no)'];
			$order_no = $max + 1;
			mysqli_query($conn,"UPDATE tbl_export SET order_no = '$order_no' WHERE user = '$user' AND id = '$id'");
		}

	}else{
		mysqli_query($conn,"UPDATE tbl_export SET list = 1, order_no = 0 WHERE user = '$user' AND id = '$id'");
	}


