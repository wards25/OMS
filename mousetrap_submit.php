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
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_mousetrap_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1)
    {
        $qstring = '?status=err';
    }
    else
    {
        $asset_id = $_POST['asset_id'];
        foreach ($asset_id as $id => $name)
        {
            if(empty($_POST['in_place'][$id])){
                $in_place = 0;
            }else{
                $in_place = $_POST['in_place'][$id];
            }

            if(empty($_POST['condition'][$id])){
                $condition = '';
            }else{
                $condition = $_POST['condition'][$id];
            }

            if(empty($_POST['remarks'][$id])){
                $remarks = '';
            }else{
                $remarks = $_POST['remarks'][$id];
            }
            
            // Sanitize the input values
            $asset_name = mysqli_real_escape_string($conn, $name);
            $in_place = mysqli_real_escape_string($conn, $in_place);
            $condition = mysqli_real_escape_string($conn, $condition);
            $remarks = mysqli_real_escape_string($conn, $remarks);

            mysqli_query($conn,"INSERT INTO tbl_mousetrap_raw (id,date,location,shift,shift_type,asset_name,asset_id,in_place,cond,remarks,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','Mouse Trap','$asset_name','$in_place','$condition','$remarks','$user',NOW(),'1','$user',NOW()) ");
        }

        $action = "SUBMITTED MOUSE TRAP CHECKLIST";
        $module = "MOUSE TRAP";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }
}

// Redirect to the listing page
header("Location: checklist_admin.php".$qstring);