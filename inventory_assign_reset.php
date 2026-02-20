<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

mysqli_query($conn,"DELETE FROM tbl_inventory_group");
mysqli_query($conn,"ALTER TABLE tbl_inventory_group AUTO_INCREMENT = 0 ");
mysqli_query($conn,"UPDATE tbl_inventory_rack SET fin='0',log='0'");

echo '<div class="alert alert-success alert-dismissable fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Group assignment has been reset successfully.';
            
$action = $user." RESET THE RACK ASSIGNMENT";
$module = "INVENTORY";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

