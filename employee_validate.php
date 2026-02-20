<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

  $location = $_POST['location'];
  mysqli_query($conn,"DELETE FROM tbl_employees WHERE location = '$location'");

  $import_query = mysqli_query($conn,"SELECT * FROM tbl_employees_error WHERE user = '$user'");
  while($fetch_import = mysqli_fetch_array($import_query)){
    $employee_name = $fetch_import['employee_name'];
    $position = $fetch_import['position'];
    $shift = $fetch_import['shift'];
    $department = $fetch_import['department'];

    mysqli_query($conn,"INSERT INTO tbl_employees (id,employee_name,position,shift,department,location,is_active) VALUES (NULL,'$employee_name','$position','$shift','$department','$location','1')");
  }

  // insert history
  $action = $user." IMPORTED EMPLOYEE FILE CSV";
  $module = "EMPLOYEE LIST";
  mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

  $qstring = '?status=succ';

}else{
    $qstring = '?status=invalid_file';
}

header("Location: employee.php".$qstring);

