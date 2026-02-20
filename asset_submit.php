<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];

    // check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_asset_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {
        $employee_values = $_POST['employee_name'];
        foreach ($employee_values as $id => $name)
        {
            $asset_id = $_POST['asset_id'][$id];
            
            // Sanitize the input values
            $asset_id = mysqli_real_escape_string($conn, $asset_id);
            $employee_name = mysqli_real_escape_string($conn, $name);

            $asset_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_id = '$asset_id'");
            $fetch_asset = mysqli_fetch_array($asset_query);

            $asset_name = $fetch_asset['asset_name'];
            $asset_rented = $fetch_asset['asset_rented'];

            mysqli_query($conn,"INSERT INTO tbl_asset_raw (id,date,location,shift,shift_type,asset_name,asset_id,asset_rented,assigned_to,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$asset_name','$asset_id','$asset_rented','$employee_name','$user',NOW(),'0','','') ");
        }

        $action = "SUBMITTED ASSET CHECKLIST";
        $module = "ASSET ASSIGNMENT";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);