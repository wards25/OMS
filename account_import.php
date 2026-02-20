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
            $username = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $getData[0])));
            $fname = mysqli_real_escape_string($conn, ucwords(utf8_encode(str_replace(",", '', $getData[1]))));
            $lname = mysqli_real_escape_string($conn, ucwords(utf8_encode(str_replace(",", '', $getData[2]))));
            $tag = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace(",", '', $getData[3]))));
            $role_id = mysqli_real_escape_string($conn, $getData[4]);
            $hub = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace(",", '', $getData[5]))));
            $password = md5('12345');

            $name_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE username = '$username'");
            $check_name = mysqli_num_rows($name_query);

            if($check_name >= 1){
                $fetch_name = mysqli_fetch_array($name_query);
                $id = $fetch_name['id'];
                mysqli_query($conn, "UPDATE tbl_users SET username='$username',fname='$fname',lname='$lname',tag='$tag',hub='$hub' WHERE id='$id'");
            }else{
                mysqli_query($conn, "INSERT INTO tbl_users (id,username,password,fname,lname,tag,role_id,is_active,hub) VALUES (NULL,'" .$username. "', '" .$password. "', '" .$fname. "', '" .$lname. "', '" .$tag. "', '" .$role_id. "', '1', '" .$hub. "')");
            }
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." IMPORTED USERS FILE CSV";
        $module = "USER ACCOUNT";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=import';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

header("Location: account.php".$qstring);
