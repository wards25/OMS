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
        $employee_name = $_POST['employee_name'][$id];

        if(empty($_POST['c1'][$id])){
            $c1 = 0;
        }else{
            $c1 = $_POST['c1'][$id];
        }

        if(empty($_POST['c1_remarks'][$id])){
            $c1_remarks = '';
        }else{
            $c1_remarks = ucfirst($_POST['c1_remarks'][$id]);
        }
        
        if(empty($_POST['c2'][$id])){
            $c2 = 0;
        }else{
            $c2 = $_POST['c2'][$id];
        }

        if(empty($_POST['c2_remarks'][$id])){
            $c2_remarks = '';
        }else{
            $c2_remarks = ucfirst($_POST['c2_remarks'][$id]);
        }

        if(empty($_POST['c3'][$id])){
            $c3 = 0;
        }else{
            $c3 = $_POST['c3'][$id];
        }

        if(empty($_POST['c3_remarks'][$id])){
            $c3_remarks = '';
        }else{
            $c3_remarks = ucfirst($_POST['c3_remarks'][$id]);
        }

        if(empty($_POST['c4'][$id])){
            $c4 = 0;
        }else{
            $c4 = $_POST['c4'][$id];
        }

        if(empty($_POST['c4_remarks'][$id])){
            $c4_remarks = '';
        }else{
            $c4_remarks = ucfirst($_POST['c4_remarks'][$id]);
        }

        if(empty($_POST['c5'][$id])){
            $c5 = 0;
        }else{
            $c5 = $_POST['c5'][$id];
        }

        if(empty($_POST['c5_remarks'][$id])){
            $c5_remarks = '';
        }else{
            $c5_remarks = ucfirst($_POST['c5_remarks'][$id]);
        }

        // Sanitize the input values
        $zone_name = mysqli_real_escape_string($conn, $locations);
        $employee_name = mysqli_real_escape_string($conn, $employee_name);
        $c1 = mysqli_real_escape_string($conn, $c1);
        $c1_remarks = mysqli_real_escape_string($conn, $c1_remarks);
        $c2 = mysqli_real_escape_string($conn, $c2);
        $c2_remarks = mysqli_real_escape_string($conn, $c2_remarks);
        $c3 = mysqli_real_escape_string($conn, $c3);
        $c3_remarks = mysqli_real_escape_string($conn, $c3_remarks);
        $c4 = mysqli_real_escape_string($conn, $c4);
        $c4_remarks = mysqli_real_escape_string($conn, $c4_remarks);
        $c5 = mysqli_real_escape_string($conn, $c5);
        $c5_remarks = mysqli_real_escape_string($conn, $c5_remarks);

        if(empty($employee_name)){
            $employee_name = 'Not Assigned';
        }else{
            
        }

        $exist_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' AND location_name='$zone_name'");
        $fetch_id = mysqli_fetch_array($exist_query);
        $count_exist = mysqli_num_rows($exist_query);

        if($count_exist >= 1){
            mysqli_query($conn,"UPDATE tbl_zone_raw SET employee_name='$employee_name',c1='$c1',c1_remarks='$c1_remarks',c2='$c2',c2_remarks='$c2_remarks',c3='$c3',c3_remarks='$c3_remarks',c4='$c4',c4_remarks='$c4_remarks',c5='$c5',c5_remarks='$c5_remarks',is_validated='1',validated_by='$user',validated_at=NOW() WHERE id=".$fetch_id['id']);
        }else{
            $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE location_name = '$zone_name'");
            $fetch_zone = mysqli_fetch_array($zone_query);

            $zone_name = $fetch_zone['location_name'];

            mysqli_query($conn,"INSERT INTO tbl_zone_raw (id,date,location,shift,shift_type,employee_name,location_name,c1,c1_remarks,c2,c2_remarks,c3,c3_remarks,c4,c4_remarks,c5,c5_remarks,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$employee_name','$zone_name','$c1','$c1_remarks','$c2','$c2_remarks','$c3','$c3_remarks','$c4','$c4_remarks','$c5','$c5_remarks','$user',NOW(),'1','$user',NOW()) ");
        }
    }

    $action = "VALIDATED WAREHOUSE ZONE CHECKLIST";
    $module = "WAREHOUSE ZONE";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=validate';
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);