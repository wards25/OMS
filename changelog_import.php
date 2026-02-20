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
    mysqli_query($conn,"DELETE FROM tbl_changelog");
    mysqli_query($conn,"ALTER TABLE tbl_changelog AUTO_INCREMENT = 0 ");

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

        // Parse data from CSV file line by line
        while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE)
        {
            // Get row data
            $version = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $getData[0])));
            $action = mysqli_real_escape_string($conn, ucwords(utf8_encode(str_replace(",", '', $getData[1]))));
            $log = mysqli_real_escape_string($conn, ucwords(utf8_encode(str_replace(",", '', $getData[2]))));
            $module = mysqli_real_escape_string($conn, ucwords(utf8_encode(str_replace(",", '', $getData[3]))));
            
            mysqli_query($conn, "INSERT INTO tbl_changelog (id,version,action,log,module) VALUES (NULL,'" .$version. "', '" .$action. "', '" .$log. "', '" .$module. "')");
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." IMPORTED CHANGELOG FILE CSV";
        $module = "CHANGELOG";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=import';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}


header("Location: changelog.php".$qstring);
