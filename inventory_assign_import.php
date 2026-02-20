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
    // mysqli_query($conn,"DELETE FROM tbl_inventory_rack");
    // mysqli_query($conn,"ALTER TABLE tbl_inventory_rack AUTO_INCREMENT = 0 ");

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
            // $sku = mysqli_real_escape_string($conn, utf8_encode(str_replace([' ', ','], '', $getData[0])));
            $rack = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[0]))));
            $column = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[1]))));
            $level = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[2]))));
            $position = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[3]))));
            $location = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[4]))));
            $groupno = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[5]))));
            $groupno = !empty($groupno) ? $groupno : '0';
            // $status = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[6]))));
            // $status = !empty($status) ? $status : '0';
            
            // $sku = !empty($sku) ? $sku : 'NO SKU';
            $rackFormatted = !empty($rack) ? 'R' . $rack : '';
            $columnFormatted = !empty($column) ? 'C' . $column : '';
            $levelFormatted = !empty($level) ? 'L' . $level : '';
            $positionFormatted = !empty($position) ? $position : '';

            $parts = array_filter([$rackFormatted, $columnFormatted, $levelFormatted, $positionFormatted]); // Remove empty values
            $racklocation = implode('-', $parts); // Join remaining parts with '-'

            // $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode = '$sku'");
            // if (mysqli_num_rows($product_query) > 0) {
            //     $fetch_product = mysqli_fetch_assoc($product_query);
            //     $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal']))); // Ensure you're using the correct column name
            //     $company = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['vendorcode'])));
            // } else {
            //     $principal = 'NULL';
            //     $company = 'NULL';
            // }
            
            $sku = 'NO SKU';
            $principal = 'NULL';
            $company = 'NULL';

            $check_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_rack WHERE racklocation = '$racklocation' AND location = '$location'");
            $check = mysqli_num_rows($check_query);

            if($check > 0){
                $fetch_check = mysqli_fetch_assoc($check_query);
                $update_id = $fetch_check['id'];
                mysqli_query($conn,"UPDATE tbl_inventory_rack SET groupno = '$groupno' WHERE id = '$update_id'");
            }else{
                mysqli_query($conn, "INSERT INTO tbl_inventory_rack (id,sku,rack,col,level,pos,racklocation,status,location,principal,company,fin,fin_count,log,log_count,groupno,type,dtr,inv_status) VALUES (NULL,'NO SKU', '" .$rack. "', '" .$column. "', '" .$level. "', '" .$position. "', '" .$racklocation. "', 'NOT ENCODED', '" .$location. "', 'NULL', 'NULL', '0', '0', '0', '0', '" .$groupno. "', 'UPLOAD', NOW(), '0')");
            }
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." IMPORTED RACK FILE CSV";
        $module = "INVENTORY";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=import';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

header("Location: inventory_assign.php".$qstring);
