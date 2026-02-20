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
        $asset_id = $_POST['asset_id'][$id];

        if(empty($_POST['condition'])){
            $condition = 0;
        }else{
            $condition = $_POST['condition'][$id]; 
        }

        // Sanitize the input values
        $asset_id = mysqli_real_escape_string($conn, $asset_id);
        $condition = mysqli_real_escape_string($conn, $condition);
        $employee_name = mysqli_real_escape_string($conn, $name);

        if(empty($employee_name)){
            $employee_name = 'Not In Use';
        }else{
            
        }

        //update condition
        mysqli_query($conn,"UPDATE tbl_asset_inv SET cond = '$condition' WHERE asset_id = '$asset_id'");

        if($condition == 0){
            $action = "DEACTIVATED ASSET: ".$asset_id;
            $module = "ASSET INVENTORY";
            mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");
        }else{

        }

        $exist_query = mysqli_query($conn,"SELECT * FROM tbl_asset_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' AND asset_id='$asset_id'");
        $fetch_id = mysqli_fetch_array($exist_query);
        $count_exist = mysqli_num_rows($exist_query);

        if($count_exist >= 1){
            mysqli_query($conn,"UPDATE tbl_asset_raw SET assigned_to='$employee_name',is_validated='1',validated_by='$user',validated_at=NOW() WHERE id=".$fetch_id['id']);
        }else{
            $asset_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_id = '$asset_id'");
            $fetch_asset = mysqli_fetch_array($asset_query);

            $asset_name = $fetch_asset['asset_name'];
            $asset_rented = $fetch_asset['asset_rented'];

            mysqli_query($conn,"INSERT INTO tbl_asset_raw (id,date,location,shift,shift_type,asset_name,asset_id,asset_rented,assigned_to,submitted_by,submitted_at,is_validated,validated_by,validated_at) VALUES (NULL,'$date','$location','$shift','$shift_type','$asset_name','$asset_id','$asset_rented','$employee_name','$user',NOW(),'1','$user',NOW())");
        }
    }

    $action = "VALIDATED ASSET CHECKLIST";
    $module = "ASSET ASSIGNMENT";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=validate';
}

// Redirect to the listing page
header("Location: checklist.php".$qstring);