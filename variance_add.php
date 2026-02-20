<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$error_type = $_POST['error_type'];

if($error_type == 'Short Picked' || $error_type == 'Over Picked' || $error_type == 'Shortlanded' || $error_type == 'Overlanded'){
    $invoiced_sku = $_POST['invoiced_sku'];
    $desc1_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$invoiced_sku'");
    $fetch_desc1 = mysqli_fetch_array($desc1_query);
    $invoiced_desc = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_desc1['description'])));
    $uom = $fetch_desc1['uom'];

    $picked_sku = $invoiced_sku;
    $picked_desc = $invoiced_desc;
    $qty1 = $_POST['qty1'];
    $qty2 = $_POST['qty2'];
}else{
    $picked_sku = $_POST['picked_sku'];
    $desc2_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$picked_sku'");
    $fetch_desc2 = mysqli_fetch_array($desc2_query);
    $picked_desc = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_desc2['description'])));
    $uom = $fetch_desc2['uom'];

    $invoiced_sku = '';
    $invoiced_desc = '';
    $qty1 = '0';
    $qty2 = $_POST['qty2'];
}

if($error_type == 'Short Picked' || $error_type == 'Shortlanded'){
    $totalpicked_qty = $qty1 - $qty2;
    $totalreturn_qty = 0;
}else{
    $totalpicked_qty = 0;
    $totalreturn_qty = $qty2 - $qty1;
}

if($totalpicked_qty <= 0 && $error_type == 'Short Picked' || $totalpicked_qty <= 0 && $error_type == 'Shortlanded'){
    echo '<div class="alert alert-danger alert-dismissable fade show alert3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Picked qty must not be less than or equal to zero.
        </div>';
}else if($totalreturn_qty <= 0 && $error_type == 'Overlanded'){
    echo '<div class="alert alert-danger alert-dismissable fade show alert3" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Return qty must not be less than or equal to zero.
        </div>'; 
}else{
    mysqli_query($conn,"INSERT INTO tbl_variance_list (id,db_id,user,error_type,invoiced_sku,invoiced_desc,invoiced_qty,picked_sku,picked_desc,picked_qty,uom,qty,return_qty) VALUES(NULL,'0','$user','$error_type','$invoiced_sku','$invoiced_desc','$qty1','$picked_sku','$picked_desc','$qty2','$uom','$totalpicked_qty','$totalreturn_qty')");
}
?>