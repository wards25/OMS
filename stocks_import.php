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
        while (($getData = fgetcsv($csvFile, 0, ",")) !== FALSE)
        {
            // Get row data
            $sku = mysqli_real_escape_string($conn, utf8_encode(str_replace([' ', ','], '', $getData[0])));
            $qty = mysqli_real_escape_string($conn, utf8_encode(str_replace([' ', ','], '', $getData[1])));
            $uom = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[2]))));
            $bbd = mysqli_real_escape_string($conn, utf8_encode(str_replace([' ', ','], '', $getData[3])));
            $bbd = date("Y-m-d", strtotime($bbd)); 
            $year = date("Y", strtotime($bbd));   
            $month = date("m", strtotime($bbd));  
            $day = date("d", strtotime($bbd));  
            $rack = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[4]))));
            $column = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[5]))));
            $level = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[6]))));
            $position = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[7]))));
            $location = mysqli_real_escape_string($conn, strtoupper(utf8_encode(str_replace([' ', ','], '', $getData[8]))));
            
            $rackFormatted = !empty($rack) ? 'R' . $rack : '';
            $columnFormatted = !empty($column) ? 'C' . $column : '';
            $levelFormatted = !empty($level) ? 'L' . $level : '';
            $positionFormatted = !empty($position) ? $position : '';

            $parts = array_filter([$rackFormatted, $columnFormatted, $levelFormatted, $positionFormatted]); // Remove empty values
            $racklocation = implode('-', $parts); // Join remaining parts with '-'

            $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode = '$sku'");
            if (mysqli_num_rows($product_query) > 0) {
                $fetch_product = mysqli_fetch_assoc($product_query);
                $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
                $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal'])));
                $company = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['vendorcode'])));
                $uom_final = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['uom'])));

                if($uom == 'CS'){
                    $totalqty = $qty * $fetch_product['percase'];
                    $cases = $totalqty;
                    $pieces = 0;
                }else{
                    $totalqty = $qty;
                    $cases = 0;
                    $pieces = $totalqty;
                }

            } else {
                $principal = 'NULL';
                $company = 'NULL';
            }

            $check_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_stocks WHERE racklocation = '$racklocation' AND location = '$location'");
            $check_stock = mysqli_num_rows($check_query);

            if($check_stock > 0){
                $fetch_stock = mysqli_fetch_assoc($check_query);
                $update_id = $fetch_stock['id'];
                mysqli_query($conn,"UPDATE tbl_inventory_stocks SET sku = '$sku', description = '$description', principal = '$principal', company = '$company', qty = '$totalqty', uom = '$uom_final', mt = '$month', dt = '$day', yr = '$year', bbd = '$bbd', cases = '$cases', pieces = '$pieces', submit_by = '$user', submit_date = NOW() WHERE id = '$update_id'");
                mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = '$sku', principal = '$principal', company = '$company', inv_status = '1' WHERE racklocation = '$racklocation' AND location = '$location'");
            }
        }

        // Close opened CSV file
        fclose($csvFile);

        // insert history
        $action = $user." UPDATED STOCKS DATA CSV";
        $module = "STOCKS";
        mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

        $qstring = '?status=import';
    }else{
        $qstring = '?status=err';
    }

}else{
    $qstring = '?status=invalid_file';
}

header("Location: stocks.php".$qstring);
