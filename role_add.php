<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$rolename = ucfirst($_POST['rolename']);

// check if item code exists
$check_query = mysqli_query($conn,"SELECT * FROM tbl_roles WHERE role_name = '$rolename'");
$row = mysqli_num_rows($check_query);

if($row >= 1){

	echo '<div class="alert alert-danger alert-dismissable fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Role Already Added.
          </div>';
}else{

	mysqli_query($conn,"INSERT INTO tbl_roles (id,role_name) VALUES(NULL,'$rolename')");

	echo '<div class="alert alert-success alert-dismissable fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Role has been added successfully.';

    $action = "ADDED ROLE: ".$rolename;
    $module = "POSITION MAP";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
}