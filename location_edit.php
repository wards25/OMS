<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$status = $_POST['status'];

foreach ($status as $id => $loc_status) {
    $db_id = $_POST['id'][$id];

    $db_id = mysqli_real_escape_string($conn, $db_id);
    $location_status = mysqli_real_escape_string($conn, $loc_status);

    mysqli_query($conn,"UPDATE tbl_locations SET is_active = '$location_status' WHERE id = '$db_id'");
}
	echo '<div class="alert alert-success alert-dismissable fade show alert2" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Location has been updated successfully.';

    $action = "EDIT LOCATION SETTING";
    $module = "LOCATION ACCESS";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");