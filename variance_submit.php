<?php
ob_start();
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_POST['submit'])){

    $type = strtoupper($_POST['type']);
    $po_no = $_POST['po_no'];
    $invoice_no = $_POST['invoice_no'];

    if(empty($_POST['picker'])){
        $picker = '';
    }else{
       $picker = $_POST['picker']; 
    }
    
    $checker = $_POST['checker'];
    $location = $_POST['location'];
    $date_processed = date("Y-m-d");

    $invoice_query = mysqli_query($conn,"SELECT invoice_no FROM tbl_variance_ref WHERE invoice_no = '$invoice_no' AND form_type = '$type' AND status = '1'");
    $invoice_count = mysqli_num_rows($invoice_query);

    if($invoice_count >=1){
        $qstring = '?status=err';
    }else{

        // load next reference
        $load_query = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_type = '$type' ORDER BY id DESC LIMIT 1");
        $row = mysqli_num_rows($load_query);

        if($row >= 1){
            $fetch_load = mysqli_fetch_array($load_query);
            $last_number = explode('-', $fetch_load['form_no']);
            $form_no = $type."-".date("my").'-'.($last_number[count($last_number)-1]+1);
        }else{
            $form_no = $type."-".date("my")."-10001";
        }

        $sku_query = mysqli_query($conn,"SELECT * FROM tbl_variance_list WHERE user = '$user'");
        $sku_count = mysqli_num_rows($sku_query);

        if($sku_count > 0){
            while($fetch_sku = mysqli_fetch_array($sku_query)){
                $error_type = $fetch_sku['error_type'];

                if(empty($fetch_sku['invoiced_sku'])){
                    $invoiced_sku = '';
                    $invoiced_desc = '';
                }else{
                    $invoiced_sku = $fetch_sku['invoiced_sku'];
                    $invoiced_desc = $fetch_sku['invoiced_desc'];
                }
                
                $invoiced_qty = $fetch_sku['invoiced_qty'];
                $picked_sku = $fetch_sku['picked_sku'];
                $picked_desc = $fetch_sku['picked_desc'];
                $picked_qty = $fetch_sku['picked_qty'];
                $uom = $fetch_sku['uom'];
                $totalpicked_qty = $fetch_sku['qty'];
                $totalreturn_qty = $fetch_sku['return_qty'];

                mysqli_query($conn,"INSERT INTO tbl_variance_raw (id,form_no,form_type,error_type,invoiced_sku,invoiced_desc,invoiced_qty,picked_sku,picked_desc,picked_qty,uom,qty,return_qty,status,location) VALUES (NULL,'$form_no','$type','$error_type','$invoiced_sku','$invoiced_desc','$invoiced_qty','$picked_sku','$picked_desc','$picked_qty','$uom','$totalpicked_qty','$totalreturn_qty','1','$location')");
            }

            if(empty($_POST['driver'])){
                $driver = ''; 
            }else{
                $driver = ucwords($_POST['driver']);
            }

            if(empty($_POST['helper'])){
                $helper = ''; 
            }else{
                $helper = ucwords($_POST['driver']);
            }
            
            if(empty($_POST['newdriver'])){
                $newdriver = ''; 
            }else{
                $newdriver = ucwords($_POST['newdriver']);
            }

            if(empty($_POST['newhelper'])){
                $newhelper = ''; 
            }else{
                $newhelper = ucwords($_POST['newhelper']);
            }

            mysqli_query($conn,"INSERT INTO tbl_variance_ref (id,form_no,form_type,date,po_no,invoice_no,picker_name,driver_name,helper_name,new_driver,new_helper,checker_name,submit_by,location,status,dtr) VALUES (NULL,'$form_no','$type','$date_processed','$po_no','$invoice_no','$picker','$driver','$helper','$newdriver','$newhelper','$checker','$user','$location','1',NOW()) ");
            

            $action = "SUBMITTED ".$type." FORM: ".$form_no;
            $module = $type." VARIANCE FORM";
            mysqli_query($conn,"INSERT INTO tbl_history VALUES (NULL,'$user','$action','$module',NOW())");

            $qstring = '?status=add';

        }else{
            $qstring = '?status=sku';
        }
    }
}

if ($type == 'PVF') {
    header("Location: pvf.php".$qstring);
} else if($type == 'LVF'){
    header("Location: lvf.php".$qstring);
}else if($type == 'RVF'){
    header("Location: rvf.php".$qstring);
}else{
    header("Location: svf.php".$qstring);
}