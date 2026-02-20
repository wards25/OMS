<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $location = $_POST['location'];
    $date = $_POST['date'];
    $shift = $_POST['shift'];
    $shift_type = $_POST['shift_type'];

    $employee_values = $_POST['employee_name'];
    foreach ($employee_values as $id => $name)
    {

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
        
        if(empty($_POST['edit'][$id])){
            $edit = '';
        }else{
            $edit = ucwords($_POST['edit'][$id]);
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
                mysqli_query($conn,"UPDATE tbl_employees SET is_active='1' WHERE employee_name='$employee_name'");
            }
        }

        // Sanitize the input values
        $attendance = mysqli_real_escape_string($conn, $attendance);
        $uniform = mysqli_real_escape_string($conn, $uniform);
        $identification = mysqli_real_escape_string($conn, $identification);
        $remarks = mysqli_real_escape_string($conn, $remarks);
        $edit = mysqli_real_escape_string($conn, $edit);
        $edit = ucfirst($edit);
        
        $exist_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' AND employee_name='$employee_name'");
        $fetch_id = mysqli_fetch_array($exist_query);

        mysqli_query($conn,"UPDATE tbl_ofattendance_raw SET attendance='$attendance',reason='$remarks',uniform='$uniform',identification='$identification',remarks='$edit',is_validated='1',validated_by='$user',validated_at=NOW() WHERE id=".$fetch_id['id']);

    }
    
    $action = "VALIDATED ATTENDANCE CHECKLIST";
    $module = "OFFICE ATTENDANCE";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=validate';
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);