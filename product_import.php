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
            $itemcode = mysqli_real_escape_string($conn, str_replace([' ', ','], '', $getData[0]));
            $itembarcode = mysqli_real_escape_string($conn, str_replace([' ', ','], '', $getData[1]));
            $barcode = mysqli_real_escape_string($conn, str_replace([' ', ','], '', $getData[2]));
            $vendorcode = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '',$getData[3])));
            $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $getData[4])));
            $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $getData[5])));
            $percase = mysqli_real_escape_string($conn, str_replace([' ', ','], '', $getData[6]));
            $perserving = mysqli_real_escape_string($conn, str_replace([' ', ','], '', $getData[7]));
            $uom = mysqli_real_escape_string($conn, strtoupper(str_replace([' ', ','], '', $getData[8])));
            $racklocation = mysqli_real_escape_string($conn, strtoupper(str_replace([' ', ','], '', $getData[9])));

            $name_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$itemcode'");
            $check_name = mysqli_num_rows($name_query);

            if($check_name >= 1){
                $fetch_name = mysqli_fetch_array($name_query);
                $id = $fetch_name['id'];
                mysqli_query($conn, "UPDATE tbl_product SET search_code='$itemcode',itembarcode='$itembarcode',barcode='$barcode',vendorcode='$vendorcode',description='$description',principal='$principal',percase='$percase',perserving='$perserving',uom='$uom',racklocation='$racklocation' WHERE id='$id'");
            }else{
                mysqli_query($conn, "INSERT INTO tbl_product (id,search_code,itemcode,itembarcode,barcode,vendorcode,description,principal,percase,perserving,uom,racklocation,is_active) VALUES (NULL,'" .$itemcode. "', '" .$itemcode. "', '" .$itembarcode. "', '" .$barcode. "', '" .$vendorcode. "', '" .$description. "', '" .$principal. "', '" .$percase. "', '" .$perserving. "', '" .$uom. "', '" .$racklocation. "', '1')");
            }
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." IMPORTED PRODUCT FILE CSV";
        $module = "PRODUCT LIST";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=import';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

header("Location: product.php".$qstring);
