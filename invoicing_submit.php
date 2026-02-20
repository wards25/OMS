<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$invoice_date = date("Y-m-d");

if (isset($_POST['submit'])) {

    if (!empty($_POST['invoice'])) {
        foreach ($_POST['invoice'] as $sono => $invoice) {
            // Ensure invoice is not empty before updating
            if (!empty($invoice)) {
                $dtr = $_POST['dtr'][$sono];
                $tmno = $_POST['tmno'][$sono];
                $pono = $_POST['pono'][$sono];

                $invoice = mysqli_real_escape_string($conn, $invoice);
                $sono = mysqli_real_escape_string($conn, $sono);
                $dtr = mysqli_real_escape_string($conn, $dtr);
                $tmno = mysqli_real_escape_string($conn, $tmno);
                $pono = mysqli_real_escape_string($conn, $pono);
            
                $invoice_query = mysqli_query($conn,"SELECT * FROM tbl_invoice WHERE sino = '$invoice' AND sono = '$sono'");
                $invoice_check = mysqli_num_rows($invoice_query);

                if($invoice_check > 0){
                    
                }else{
                    mysqli_query($conn,"INSERT INTO tbl_invoice (id, upload_date, tmno, pono, sono, sino, invoice_clerk, invoice_date, clearing_clerk, clearing_date, clearing_status) VALUES(NULL, '$dtr', '$tmno', '$pono', '$sono', '$invoice', '$user', '$invoice_date', '', '', 'TO CLEAR')");

                    // tbl_invoice_trail
                    // dashboard view of invoice

                }

                $updateQuery = "UPDATE tbl_trips_raw SET invoicing_status = 'INVOICED', sino = '$invoice' WHERE sono = '$sono'";
                mysqli_query($conn, $updateQuery);
            }
        }
        $status = '?status=succ';
    } else {
        $status = '?status=err';
    }

}else{
    
}

header("Location: invoicing.php".$status);
?>