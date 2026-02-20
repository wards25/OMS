<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];
$url = $_POST['url'];

if (isset($_POST['submit']))
{
    $name = ucfirst($_POST['name']);
    $position = $_POST['position'];
    $shift = $_POST['shift'];
    $department = $_POST['department'];
    $location = $_POST['location'];

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE employee_name = '$name'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        mysqli_query($conn,"INSERT INTO tbl_employees VALUES (NULL,'$name','$position','$shift','$department','$location','1')");

        // insert history
        $action = "ADDED EMPLOYEE: ".$name;
        $module = "EMPLOYEE LIST";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';
        $qstring2 = '&status=add';

    }else{
        $qstring = '?status=err';
        $qstring2 = '&status=err';
    }

}else{

}    
    if(empty($_POST['url'])){
        // Redirect to the listing page
        header("Location: employee.php".$qstring);
    }else{
        // Redirect to the listing page
        header("Location:".$url.$qstring2);
    }
    