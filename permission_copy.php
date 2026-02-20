<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(empty($_POST['copy_id'])){
    $qstring = '&status=err';

}else{

    $update_id = $_POST['update_id'];
    $copy_id = $_POST['copy_id'];

    mysqli_query($conn,"DELETE FROM tbl_system_permissions WHERE user_id = '$update_id'");

    $copy_query = mysqli_query($conn,"SELECT * FROM tbl_system_permissions WHERE user_id = '$copy_id'");
    while($fetch_copy = mysqli_fetch_array($copy_query)){

        $permission_id = $fetch_copy['permission_id'];
        mysqli_query($conn,"INSERT INTO tbl_system_permissions VALUES(NULL,'$update_id','$permission_id')");
    }   
    $qstring = '&status=succ';
}

// Redirect to the listing page
if (isset($_SESSION['previous_pages'][0])) {
    // Get the URL of the page two steps back
    $url = $_SESSION['previous_pages'][0];

    // Redirect to that URL
    header("Location: ".$url.$qstring);
    exit();
} else {
    // No previous pages stored
    echo "No previous pages to go back to.";
}