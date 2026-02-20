<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$rolename = $_POST['rolename'];

// check if item code exists
$check_query = mysqli_query($conn,"SELECT * FROM tbl_report_involved WHERE person_involved = '$rolename' AND user = '$user'");
$row = mysqli_num_rows($check_query);

if($row >= 1){

	echo '<div class="alert alert-warning alert-dismissable fade show alert3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Person Already Added.
          </div>';
}else{
	
	if($rolename == '3rd Party Trucker'){
		mysqli_query($conn,"INSERT INTO tbl_report_involved VALUES(NULL,'$rolename','Trucker','Others','$user')");
	}else{
		$employee_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE employee_name = '$rolename'");
		$fetch_employee = mysqli_fetch_array($employee_query);

		$position = $fetch_employee['position'];
		$department = $fetch_employee['department'];

		$department_check = mysqli_query($conn,"SELECT * FROM tbl_report_involved WHERE department = '$department'");
		$dept_row = mysqli_num_rows($department_check);

		mysqli_query($conn,"INSERT INTO tbl_report_involved VALUES(NULL,'$rolename','$position','$department','$user')");
	}
}