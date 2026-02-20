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
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_temp_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {

        $location_values = $_POST['location_name'];
        foreach ($location_values as $id => $locations)
        {   
            $temperature = $_POST['temperature'][$id];

            // Sanitize the input values
            $temp_name = mysqli_real_escape_string($conn, $locations);
            $temperature = mysqli_real_escape_string($conn, $temperature);
            
            mysqli_query($conn,"INSERT INTO tbl_temp_raw (id,date,location,shift,shift_type,location_name,temp,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$temp_name','$temperature','$user',NOW(),'1','$user',NOW())");
        }

        $action = "SUBMITTED TEMP MONITORING CHECKLIST";
        $module = "TEMP MONITORING";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);