<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

mysqli_query($conn,"DELETE FROM tbl_inventory_count");
mysqli_query($conn,"ALTER TABLE tbl_inventory_count AUTO_INCREMENT = 0 ");
mysqli_query($conn,"UPDATE tbl_inventory_rack SET status='NOT ENCODED',fin_count='0',log_count='0'");
mysqli_query($conn,"DELETE FROM tbl_inventory_ending");
mysqli_query($conn,"ALTER TABLE tbl_inventory_ending AUTO_INCREMENT = 0 ");

echo '<div class="alert alert-success alert-dismissable fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Inventory Count has been reset successfully.';

$action = $user." RESET THE INVENTORY COUNT";
$module = "INVENTORY";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");