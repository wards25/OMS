<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if (isset($_POST['submit'])) {
    if (!empty($_POST['selected_sono'])) {

        foreach ($_POST['selected_sono'] as $sono) {
            // Prevent SQL Injection by escaping the input
            $sono = mysqli_real_escape_string($conn, $sono);
            // Execute query to update invoicing status
            mysqli_query($conn,"UPDATE tbl_trips_raw SET invoicing_status = 'INVOICED' WHERE sono = '$sono'");
        }
        // Redirect to invoicing page with success status
        $status = '?status=succ';
    } else {
        // Redirect to invoicing page with error status
        $status = '?status=err';
    }

}else{
    
}

header("Location: invoicing.php".$status);
?>