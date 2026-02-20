<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$fin = $_POST['fin'];
$log = $_POST['log'];
$groupno = $_POST['groupno'];
$location = $_POST['location'];

$fin_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE id = '$fin'");
$fetch_fin = mysqli_fetch_assoc($fin_query);
$fin_name = $fetch_fin['fname']. ' ' .$fetch_fin['lname'];

$log_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE id = '$log'");
$fetch_log = mysqli_fetch_assoc($log_query);
$log_name = $fetch_log['fname']. ' ' .$fetch_log['lname'];

if(isset($_POST['sku'])){
    $sku = $_POST['sku'];
    $group_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_group WHERE groupno = '$groupno' AND sku = '$sku' AND location = '$location'");
    $check_group = mysqli_num_rows($group_query);

    if($check_group > 0) {
        mysqli_query($conn,"UPDATE tbl_inventory_group SET fin_id='$fin',fin_name='$fin_name',log_id='$log',log_name='$log_name',assigned_by='$user',assign_date=NOW() WHERE groupno = '$groupno' AND sku = '$sku' AND location = '$location'");
        
    }else{
        mysqli_query($conn,"INSERT INTO tbl_inventory_group VALUES (NULL,'$fin','$fin_name','$log','$log_name','$groupno','$sku','$location','$user',NOW())");

    }

}else{

    $group_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_group WHERE groupno = '$groupno' AND location = '$location'");
    $check_group = mysqli_num_rows($group_query);

    if($check_group > 0) {
        mysqli_query($conn,"UPDATE tbl_inventory_group SET fin_id='$fin',fin_name='$fin_name',log_id='$log',log_name='$log_name',assigned_by='$user',assign_date=NOW() WHERE groupno = '$groupno' AND location = '$location'");
        mysqli_query($conn,"UPDATE tbl_inventory_rack SET fin='1',log='1' WHERE groupno = '$groupno' AND location = '$location'");
    }else{
        mysqli_query($conn,"INSERT INTO tbl_inventory_group VALUES (NULL,'$fin','$fin_name','$log','$log_name','$groupno','','$location','$user',NOW())");
        mysqli_query($conn,"UPDATE tbl_inventory_rack SET fin='1',log='1' WHERE groupno = '$groupno' AND location = '$location'");
    }
}
    
$action = "ASSIGNED COUNTERS FOR GROUP: ".$groupno;
$module = "INVENTORY";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$qstring = '?status=assign';

if(isset($_POST['sku'])){
    header("Location: inventory_validate.php".$qstring);
}else{
    header("Location: inventory_assign.php".$qstring);
}