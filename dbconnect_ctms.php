<?php
$database = 'ctms_ultra';

date_default_timezone_set("Asia/Manila");

// connect to server
$ctms_conn = mysqli_connect('192.168.1.91', 'rgcit1', '12345', $database);
if (!$ctms_conn){
    die("Database Connection Failed" . mysqli_error($ctms_conn));
}
	// select database
    $select_db = mysqli_select_db($ctms_conn, $database);
    if (!$select_db){
        die("Database Selection Failed" . mysqli_error($ctms_conn));
}
?>