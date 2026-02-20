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
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
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
            $position = $_POST['position'][$id];

            if(empty($_POST['attendance'][$id])){
                $attendance = 0;
            }else{
                $attendance = $_POST['attendance'][$id];
            }

            if(empty($_POST['uniform'][$id])){
                $uniform = 0;
            }else{
                $uniform = $_POST['uniform'][$id];
            }

            if(empty($_POST['identification'][$id])){
                $identification = 0;
            }else{
                $identification = $_POST['identification'][$id];
            }

            // Sanitize the input values
            $employee_name = mysqli_real_escape_string($conn, $name);

            if(empty($_POST['remarks'][$id])){
                $remarks = '';
            }else{
                $remarks = $_POST['remarks'][$id];
                if($remarks == 'Resigned'){
                    mysqli_query($conn,"UPDATE tbl_employees SET is_active='0' WHERE employee_name='$employee_name'");
                }else{

                }
            }

            // Sanitize the input values
            $position = mysqli_real_escape_string($conn, $position);
            $attendance = mysqli_real_escape_string($conn, $attendance);
            $uniform = mysqli_real_escape_string($conn, $uniform);
            $identification = mysqli_real_escape_string($conn, $identification);
            $remarks = mysqli_real_escape_string($conn, $remarks);
            
            mysqli_query($conn,"INSERT INTO tbl_ofattendance_raw (id,date,location,shift,shift_type,employee_name,position,attendance,reason,uniform,identification,remarks,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$employee_name','$position','$attendance','$remarks','$uniform','$identification','','$user',NOW(),'0','','') ");
        }

        $action = "SUBMITTED ATTENDANCE CHECKLIST";
        $module = "OFFICE ATTENDANCE";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);