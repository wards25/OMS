<?php
session_start();
// include mysql database configuration file
include_once("dbconnect.php");
$user = $_SESSION['name'];
$date_submit = date("Y-m-d");
$time_submit = date("H:i:s");

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
 
            // Initialize an array to hold summed data
            $summedData = [];

            // Parse data from CSV file line by line
            while (($getData = fgetcsv($csvFile, 10000, ",")) !== FALSE)
            {
                // Get row data
                $ponumber = $getData[0];
                $podate = date("Y-m-d", strtotime($getData[1]));
                $sku = $getData[2];
                $barcode = $getData[3];
                $description = $getData[4];
                $qty = (int)$getData[5]; // Ensure qty is treated as an integer
                $amount = utf8_encode(str_replace(array(","), '', (float)$getData[6])); // Ensure amount is treated as a float
                $rdd = date("Y-m-d", strtotime($getData[7]));
                $principal = $getData[8];
                $location = strtoupper($getData[9]);
                $company = strtoupper($getData[10]);

                $company_query = mysqli_query($conn,"SELECT * FROM tbl_company WHERE short_name = '$company'");
                $check_company = mysqli_num_rows($company_query);

                if($check_company <= 0) {
                    $qstring = '?status=company';
                } else {

                    // Insert into tbl_po_raw
                    mysqli_query($conn, "INSERT INTO tbl_po_raw (id,po_no,po_date,sku,barcode,description,qty,rcvdqty,amount,rdd,po_status,supplier,company,location,date_submit,time_submit) VALUES (NULL,'" .$ponumber. "', '" .$podate. "', '" .$sku. "', '" .$barcode. "', '" .$description. "', '" .$qty. "', '0', '" .$amount. "', '" .$rdd. "', 'PENDING', '" .$principal. "', '" .$company. "', '" .$location. "', '" .$date_submit. "', '" .$time_submit. "')");

                    // Accumulate sums in the array
                    $key = "{$ponumber}|{$podate}|{$rdd}|{$principal}|{$company}|{$location}";
                    if (!isset($summedData[$key])) {
                        $summedData[$key] = ['qty' => 0, 'amount' => 0];
                    }
                    $summedData[$key]['qty'] += $qty;
                    $summedData[$key]['amount'] += $amount;
                }
            }

            // After processing all data, insert into tbl_po_list
            foreach ($summedData as $key => $data) {
                list($ponumber, $podate, $rdd, $principal, $company, $location) = explode('|', $key);
                $qty = $data['qty'];
                $amount = $data['amount'];

                mysqli_query($conn, "INSERT INTO tbl_po_list (id,po_no,po_date,qty,amount,rdd,po_status,supplier,company,location,file_path,file_size,file_type,date_submit,time_submit) VALUES (NULL,'$ponumber','$podate','$qty','$amount','$rdd','PENDING','$principal','$company','$location','','0.00','','$date_submit','$time_submit')");
            }

            // Close opened CSV file
            fclose($csvFile);

            // insert history
            $action = $user." IMPORTED PO FILE CSV";
            $module = "PURCHASE ORDER";
            mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

            $qstring = '?status=import';
        }else{
            $qstring = '?status=err';
        }

    }else{
        $qstring = '?status=invalid_file';
    }

header("Location: po.php".$qstring);
