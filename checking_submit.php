<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$checker = mysqli_real_escape_string($conn,$_POST['checker']);
$picklistno = mysqli_real_escape_string($conn,$_POST['picklistno']);
$dtr = mysqli_real_escape_string($conn,$_POST['dtr']);

$equalcount = 0;
$sku_values = $_POST['sku'];
foreach ($sku_values as $id => $sku)
{   
    $checkerqty = $_POST['pickerqty'][$id];

    // Sanitize the input values
    $sku_name = mysqli_real_escape_string($conn, $sku);
    $checkerqty = mysqli_real_escape_string($conn, $checkerqty);
    
    $picker_query = mysqli_query($conn,"SELECT sysqty,pickerqty FROM tbl_trips_picking WHERE picklistno = '$picklistno' AND sku = '$sku'");
    $fetch_picker = mysqli_fetch_assoc($picker_query);

    if($fetch_picker['sysqty'] == $fetch_picker['pickerqty'] && $fetch_picker['pickerqty'] == $checkerqty){

    }else{
        $equalcount += 1;
    }

    mysqli_query($conn,"UPDATE tbl_trips_picking SET checker='$checker',checkerqty='$checkerqty' WHERE picklistno='$picklistno' AND sku='$sku' AND checker='$checker' AND dtr='$dtr'");
}

if($equalcount == 0){
    
    $qty = mysqli_query($conn,"SELECT id,checkerqty FROM tbl_trips_picking WHERE picklistno='$picklistno'");
    while($fetch_qty = mysqli_fetch_assoc($qty)){
        $id = $fetch_qty['id'];
        $checked_qty = $fetch_qty['checkerqty'];
        mysqli_query($conn,"UPDATE tbl_trips_picking SET validatorqty = '$checked_qty' WHERE id='$id'");        
    }

    // Fetch all SKUs from tbl_trips_picking
    $picklist_query = mysqli_query($conn, "SELECT sku, picklistno, checkerqty FROM tbl_trips_picking WHERE picklistno='$picklistno'");
    if (!$picklist_query) {
        die("Error fetching picklist: " . mysqli_error($conn));
    }

    while ($picklist_row = mysqli_fetch_assoc($picklist_query)) {
        $picklistno = $picklist_row['picklistno'];
        $sku = $picklist_row['sku'];
        $checked_qty = $picklist_row['checkerqty']; // Quantity to distribute

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

    mysqli_query($conn,"UPDATE tbl_trips_picklist SET checker_end=NOW(),status='FOR SORTING' WHERE picklistno='$picklistno' AND checker='$checker' AND dtr='$dtr'");
    mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR SORTING',invoicing_status='FOR INVOICING' WHERE picklistno='$picklistno'");

}else{

    mysqli_query($conn,"UPDATE tbl_trips_picklist SET checker_end=NOW(),status='FOR VALIDATION' WHERE picklistno='$picklistno' AND checker='$checker' AND dtr='$dtr'");
    mysqli_query($conn,"UPDATE tbl_trips_raw SET status='FOR VALIDATION' WHERE picklistno='$picklistno'");
}

$action = "CHEKED PICKLIST NO: ".$picklistno;
$module = "TRIPS CHECKING";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

$qstring = '?status=succ';

$data = array('status' => 'success');
echo json_encode($data);

// Redirect to the listing page
// header("Location: picking.php".$qstring);