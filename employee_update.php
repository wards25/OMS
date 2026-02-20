<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['update'])){

  $id = $_POST['id'];
  $name = $_POST['name'];
  $position = $_POST['position'];
  $shift = $_POST['shift'];
  $department = $_POST['department'];
  $location = $_POST['location'];
  $status = $_POST['status'];

  mysqli_query($conn,"UPDATE tbl_employees SET employee_name='$name',position='$position',shift='$shift',department='$department',location='$location',is_active='$status' WHERE id = '$id'");

  // insert login history
  $action = "UPDATED EMPLOYEE: ".$name;
  $module = "EMPLOYEE LIST";
  mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

  $qstring = '?status=update-succ';

}else{
    
}

header("Location: employee.php".$qstring);