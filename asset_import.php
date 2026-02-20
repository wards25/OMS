<?php
session_start();
// include mysql database configuration file
include_once("dbconnect.php");
$user = $_SESSION['name'];

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");  

if (isset($_POST['submit']) || isset($_POST['location']))
{
    $location = $_POST['location'];
 
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
                $asset_name = ucfirst($getData[0]);
                $asset_id = strtoupper($getData[1]);
                $asset_rented = utf8_encode(str_replace(array("'"), '', $getData[2]));
                $asset_type = ucfirst($_POST['asset_type']);
                $location = strtoupper($_POST['location']);

                $name_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_id = '$asset_id' AND location = '$location'");
                $check_name = mysqli_num_rows($name_query);

                if($check_name >= 1){
                    $fetch_name = mysqli_fetch_array($name_query);
                    $id = $fetch_name['id'];
                    mysqli_query($conn, "UPDATE tbl_asset_inv SET asset_name='$asset_name',asset_rented='$asset_rented' WHERE id='$id'");
                }
                else
                {
                   mysqli_query($conn, "INSERT INTO tbl_asset_inv (id,asset_name,asset_id,asset_rented,asset_type,location,cond,report) VALUES (NULL,'" .$asset_name. "', '" .$asset_id. "', '" .$asset_rented. "', '" .$asset_type. "', '" .$location. "', '1','0')");
                }
            }

            // Close opened CSV file
            fclose($csvFile);

            // insert history
            $action = $user." IMPORTED ASSET FILE CSV: ".$location;
            $module = "ASSET INVENTORY";
            mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

            $qstring = '?status=import';
        }else{
            $qstring = '?status=err';
        }

    }else{
        $qstring = '?status=invalid_file';
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