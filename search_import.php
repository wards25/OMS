<?php
session_start();
// include mysql database configuration file
include_once("dbconnect.php");
$user = $_SESSION['name'];

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");  

if (isset($_POST['submit']))
{
    mysqli_query($conn,"DELETE FROM tbl_search");
    mysqli_query($conn,"ALTER TABLE tbl_search AUTO_INCREMENT = 0 ");

    // Allowed mime types
    $fileMimes = array(
        'text/x-comma-separated-values',
        'text/comma-separated-values',
        'application/octet-stream',
        'application/vnd.ms-excel',
        'application/x-csv',
        'text/x-csv',
        'text/csv',
        'application/csv',
        'application/excel',
        'application/vnd.msexcel',
        'text/plain'
    );
 
    // Validate whether selected file is a CSV file
    if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $fileMimes))
    {
        
        // Open uploaded CSV file with read-only mode
        $csvFile = fopen($_FILES['file']['tmp_name'], 'r');

        // Skip the first line
        fgetcsv($csvFile);

        $error = '';            
        // Parse data from CSV file line by line
        while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE) {

            // Get row data
            $module = strtolower(mysqli_real_escape_string($conn, $getData[0]));
            $sub1 = strtolower(mysqli_real_escape_string($conn, $getData[1]));
            $sub2 = strtolower(mysqli_real_escape_string($conn, $getData[2]));
            $link = strtolower(mysqli_real_escape_string($conn, $getData[3])).'.php';

            // Insert into tbl_trips_error
            mysqli_query($conn, "INSERT INTO tbl_search (id, module, sub1, sub2, link) VALUES (NULL, '$module', '$sub1', '$sub2', '$link')");
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." IMPORTED SEARCH LIST FILE CSV";
        $module = "SETTINGS";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=succ';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

header("Location: search_raw.php".$qstring);
 