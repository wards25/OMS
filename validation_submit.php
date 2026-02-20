<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $picklistno = mysqli_real_escape_string($conn,$_POST['picklistno']);
    $dtr = mysqli_real_escape_string($conn,$_POST['dtr']);
    $date_processed = date("Y-m-d");

    if(empty($_POST['picker'])){
        $picker = '';
    }else{
        $picker = $_POST['picker'];
    }

    if(empty($_POST['checker'])){
        $checker = '';
    }else{
        $checker = $_POST['checker'];
    }

    // load location
    $location_query = mysqli_query($conn,"SELECT location FROM tbl_trips_picklist WHERE picklistno = '$picklistno'");
    $fetch_location = mysqli_fetch_assoc($location_query);
    $location = $fetch_location['location'];

    // load next reference
    $load_query = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_type = 'PVF' ORDER BY id DESC LIMIT 1");
    $row = mysqli_num_rows($load_query);

    if($row >= 1){
        $fetch_load = mysqli_fetch_array($load_query);
        $last_number = explode('-', $fetch_load['form_no']);
        $form_no = "PVF-".date("my").'-'.($last_number[count($last_number)-1]+1);
    }else{
        $form_no = "PVF-".date("my")."-10001";
    }

    // insert equal qty to validator qty
    $equal_query = mysqli_query($conn,"SELECT sku,sysqty,pickerqty,checkerqty FROM tbl_trips_picking WHERE picklistno = '$picklistno'");
    while($fetch_equal = mysqli_fetch_assoc($equal_query)){
        $mdccode = $fetch_equal['sku'];

        if($fetch_equal['sysqty'] == $fetch_equal['pickerqty'] && $fetch_equal['pickerqty'] == $fetch_equal['checkerqty']){
            $sysqty = $fetch_equal['sysqty'];
            mysqli_query($conn,"UPDATE tbl_trips_picking SET validator='$user',validatorqty='$sysqty' WHERE sku = '$mdccode' AND picklistno = '$picklistno'");
        }else{

        }
    }

    // Execute algorithm for updating
    $sku_values = $_POST['sku'];
    foreach ($sku_values as $id => $sku)
    {   
        $description = $_POST['description'][$id];
        $uom = $_POST['uom'][$id];
        $qty = $_POST['sysqty'][$id];
        $pickerqty = $_POST['pickerqty'][$id];
        $checkerqty = $_POST['checkerqty'][$id];
        $error_type = $_POST['error_type'][$id];

        if(empty($_POST['finalqty'][$id])){
            $finalqty = 0;
        }else{
            $finalqty = $_POST['finalqty'][$id];
        }

        if(empty($_POST['accountable'][$id])){
            $accountable = '';
        }else{
            $accountable = $_POST['accountable'][$id];
        }

        // Sanitize the input values
        $sku_name = mysqli_real_escape_string($conn, $sku);
        $description = mysqli_real_escape_string($conn, $description);
        $uom = mysqli_real_escape_string($conn, $uom);
        $qty = mysqli_real_escape_string($conn, $qty);
        $pickerqty = mysqli_real_escape_string($conn, $pickerqty);
        $checkerqty = mysqli_real_escape_string($conn, $checkerqty);
        $finalqty = mysqli_real_escape_string($conn, $finalqty);
        $error_type = mysqli_real_escape_string($conn, $error_type);
        $accountable = mysqli_real_escape_string($conn, $accountable);

        mysqli_query($conn,"UPDATE tbl_trips_picking SET validator='$user',validatorqty='$finalqty',validator_status='$error_type',ref_no='$form_no' WHERE sku='$sku_name' AND picklistno='$picklistno'");

        if($error_type == 'Short Picked'){
            if($accountable == 'Picker'){
                $picked_qty = $finalqty - $pickerqty;
                $return_qty = 0;
                $key = "{$error_type}";
                $summedData[$key] = [
                    'error_type' => $error_type,
                    'sku_name' => $sku_name,
                    'description' => $description,
                    'invoiced_qty' => $finalqty,
                    'picked_qty' => $pickerqty,
                    'qty' => $picked_qty,
                    'uom' => $uom,
                    'return_qty' => $return_qty
                ];
            }else if($accountable == 'Checker'){
                $picked_qty = $finalqty - $checkerqty;
                $return_qty = 0;
                $key = "{$error_type}";
                $summedData[$key] = [
                    'error_type' => $error_type,
                    'sku_name' => $sku_name,
                    'description' => $description,
                    'invoiced_qty' => $finalqty,
                    'picked_qty' => $checkerqty,
                    'qty' => $picked_qty,
                    'uom' => $uom,
                    'return_qty' => $return_qty
                ];
            }else{

            }
        }else if($error_type == 'Over Picked'){
            if($accountable == 'Picker'){
                $picked_qty = 0;
                $return_qty = $pickerqty - $finalqty;
                $key = "{$error_type}";
                $summedData[$key] = [
                    'error_type' => $error_type,
                    'sku_name' => $sku_name,
                    'description' => $description,
                    'invoiced_qty' => $finalqty,
                    'picked_qty' => $pickerqty,
                    'qty' => $picked_qty,
                    'uom' => $uom,
                    'return_qty' => $return_qty
                ];
            }else if($accountable == 'Checker'){
                $picked_qty = 0;
                $return_qty = $checkerqty - $finalqty;
                $key = "{$error_type}";
                $summedData[$key] = [
                    'error_type' => $error_type,
                    'sku_name' => $sku_name,
                    'description' => $description,
                    'invoiced_qty' => $finalqty,
                    'picked_qty' => $checkerqty,
                    'qty' => $picked_qty,
                    'uom' => $uom,
                    'return_qty' => $return_qty
                ];
            }else{

            }
        }else{

        }
        
    }

    // Insert the grouped data to tbl_variance_raw
    foreach ($summedData as $key => $data) {
        mysqli_query($conn,"INSERT INTO tbl_variance_raw (id,form_no,form_type,error_type,invoiced_sku,invoiced_desc,invoiced_qty,picked_sku,picked_desc,picked_qty,uom,qty,return_qty,status,location) VALUES (NULL, '$form_no', 'PVF', '{$data['error_type']}', '{$data['sku_name']}', '{$data['description']}', '{$data['invoiced_qty']}', '{$data['sku_name']}', '{$data['description']}', '{$data['picked_qty']}', '{$data['uom']}', '{$data['qty']}', '{$data['return_qty']}', '1', '$location')");
    }   

    if(empty($picker) && empty($checker)){
    }else{    
        // Insert tbl_variance_ref
        mysqli_query($conn,"INSERT INTO tbl_variance_ref (id,form_no,form_type,date,po_no,invoice_no,picker_name,driver_name,helper_name,new_driver,new_helper,checker_name,submit_by,location,status,dtr) VALUES (NULL,'$form_no','PVF','$date_processed','$picklistno','$picklistno','$picker','','','','','$checker','$user','$location','1',NOW()) ");
    }

    // Fetch all SKUs from tbl_trips_picking
    $picklist_query = mysqli_query($conn, "SELECT sku, picklistno, validatorqty FROM tbl_trips_picking WHERE picklistno='$picklistno'");
    if (!$picklist_query) {
        die("Error fetching picklist: " . mysqli_error($conn));
    }

    while ($picklist_row = mysqli_fetch_assoc($picklist_query)) {
        $picklistno = $picklist_row['picklistno'];
        $sku = $picklist_row['sku'];
        $checked_qty = $picklist_row['validatorqty']; // Quantity to distribute

        // Fetch rows from tbl_trips_raw for this SKU, sorted by lowest sysqty
        $raw_query = mysqli_query($conn, "
            SELECT id, sysqty 
            FROM tbl_trips_raw 
            WHERE picklistno='$picklistno' AND sku='$sku' 
            ORDER BY sysqty ASC
        ");
        if (!$raw_query) {
            die("Error fetching raw data: " . mysqli_error($conn));
        }

        // Distribute the quantity among the rows
        while ($row = mysqli_fetch_assoc($raw_query)) {
            $row_id = $row['id'];
            $row_sysqty = $row['sysqty'];

            if ($checked_qty <= 0) {
                $new_sysqty = 0;
            } elseif ($checked_qty >= $row_sysqty) {
                // Fully consume sysqty from the row
                $checked_qty -= $row_sysqty;
                $new_sysqty = $row_sysqty; // Set finalqty to match sysqty
            } else {
                // Partially consume sysqty
                $new_sysqty = $checked_qty;
                $checked_qty = 0;
            }

            // Update the row with the new finalqty
            $update_query = mysqli_query($conn, "
                UPDATE tbl_trips_raw 
                SET finalqty='$new_sysqty' 
                WHERE id='$row_id'
            ");
            if (!$update_query) {
                die("Error updating row with id $row_id: " . mysqli_error($conn));
            }
        }
    }

    mysqli_query($conn,"UPDATE tbl_trips_picklist SET status='FOR SORTING',validator='$user',validator_dtr=NOW(),ref_no='$form_no' WHERE picklistno='$picklistno' AND dtr='$dtr'");
    mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR SORTING',invoicing_status='FOR INVOICING' WHERE picklistno='$picklistno'");

    $action = "SUBMITTED PVF FORM: ".$form_no;
    $module = "PVF VARIANCE FORM";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=succ';
}

// Redirect to that URL
header("Location: validation.php".$qstring);

