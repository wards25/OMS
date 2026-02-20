<?php
session_start();
// include mysql database configuration file
include_once("dbconnect.php");
$user = $_SESSION['name'];

header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");  

$date_now = date("Y-m-d");
$date_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_ending LIMIT 1");
$fetch_date = mysqli_fetch_assoc($date_query);

$date_existing = date("Y-m-d", strtotime($fetch_date['dtr']));

if($date_now == $date_existing){
    $qstring = '?status=err';
}else{

    if (isset($_POST['submit']))
    {
        mysqli_query($conn,"DELETE FROM tbl_inventory_ending");
        mysqli_query($conn,"ALTER TABLE tbl_inventory_ending AUTO_INCREMENT = 0 ");

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
                $sku = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $getData[0])));              
                $active = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace(",", '', $getData[1]))));
                $hold = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace(",", '', $getData[2]))));
                $location = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace(",", '', $getData[3]))));

                $active = !empty($active) ? $active : '0';
                $hold = !empty($hold) ? $hold : '0';

                $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode = '$sku'");

                if (mysqli_num_rows($product_query) > 0) {
                    $fetch_product = mysqli_fetch_assoc($product_query);
                    $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
                    $uom = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['uom'])));
                    $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal']))); // Ensure you're using the correct column name
                    $company = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['vendorcode'])));
                } else {
                    $description = 'NULL';
                    $principal = 'NULL';
                    $company = 'NULL';
                }
                
            mysqli_query($conn, "INSERT INTO tbl_inventory_ending (id,sku,description,principal,company,active,hold,uom,location,status,dtr) VALUES (NULL,'" .$sku. "', '" .$description. "', '" .$principal. "', '" .$company. "', '" .$active. "', '" .$hold. "', '" .$uom. "', '" .$location. "', 'UPLOAD', NOW())");
            }

            // Close opened CSV file
            fclose($csvFile);

            // insert history
            $action = $user." IMPORTED ENDING FILE CSV";
            $module = "INVENTORY";
            mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

            $qstring = '?status=import';
        }else{
            $qstring = '?status=err';
        }

    }else{
        $qstring = '?status=invalid_file';
    }
}

header("Location: inventory.php".$qstring);
