<?php
session_start();
include_once('dbconnect.php');
$user = $_SESSION['name'];

if (isset($_POST['submit']))
{
    $modulename = ucfirst($_POST['modulename']);

    $check_query = mysqli_query($conn,"SELECT * FROM tbl_module WHERE module = '$modulename'");
    $fetch_check = mysqli_num_rows($check_query);

    if($fetch_check <= 0){

        mysqli_query($conn,"INSERT INTO tbl_module VALUES (NULL,'$modulename')");

        $getid_query = mysqli_query($conn,"SELECT * FROM tbl_module WHERE module = '$modulename'");
        $fetch_getid = mysqli_fetch_array($getid_query);

        $module_id = $fetch_getid['id'];

        //insert into tbl_permissions
        if (isset($_POST['Add']) && $_POST['Add'] == 1) {
            mysqli_query($conn, "INSERT INTO tbl_permissions VALUES (NULL,'Add','$modulename','$module_id')");
        } else {
            // Do something else or leave it empty
        }
        
        if (isset($_POST['Edit']) && $_POST['Edit'] == 1) {
            mysqli_query($conn, "INSERT INTO tbl_permissions VALUES (NULL,'Edit','$modulename','$module_id')");
        } else {
            // Do something else or leave it empty
        }

        if (isset($_POST['View']) && $_POST['View'] == 1) {
            mysqli_query($conn, "INSERT INTO tbl_permissions VALUES (NULL,'View','$modulename','$module_id')");
        } else {
            // Do something else or leave it empty
        }

        if (isset($_POST['Import']) && $_POST['Import'] == 1) {
            mysqli_query($conn, "INSERT INTO tbl_permissions VALUES (NULL,'Import','$modulename','$module_id')");
        } else {
            // Do something else or leave it empty
        }

        if (isset($_POST['Export']) && $_POST['Export'] == 1) {
            mysqli_query($conn, "INSERT INTO tbl_permissions VALUES (NULL,'Export','$modulename','$module_id')");
        } else {
            // Do something else or leave it empty
        }

        // insert history
        $action = "ADDED MODULE: ".$modulename;
        $module = "PERSMISSION MAP";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=add';

    }else{
        $qstring = '?status=err';
    }

}else{

}    

// Redirect to the listing page
header("Location: permission.php".$qstring);