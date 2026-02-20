<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];

    $location_values = $_POST['location_name'];
    foreach ($location_values as $id => $locations)
    {
        // Sanitize the input values
        $lock_name = mysqli_real_escape_string($conn, $locations);
        
        $exist_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' AND location_name='$lock_name'");
        $fetch_id = mysqli_fetch_array($exist_query);

        mysqli_query($conn,"UPDATE tbl_warehouselock_raw SET is_validated='1',validated_by='$user',validated_at=NOW() WHERE id=".$fetch_id['id']);
    }

    $action = "VALIDATED WAREHOUSELOCK CHECKLIST";
    $module = "WAREHOUSE LOCK";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=validate';
}

// Redirect to the listing page
header("Location: checklist_admin.php".$qstring);