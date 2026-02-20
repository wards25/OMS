<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

mysqli_query($conn,"DELETE FROM tbl_variance_list WHERE user = '$user'");

//get data from checklist
$type = $_GET['type'];

if($type == 'PVF'){
    $array_view = in_array(87, $permission);
}else if($type == 'RVF'){
    $array_view = in_array(91, $permission);
}else if($type == 'LVF'){
    $array_view = in_array(97, $permission);
}else{
    $array_view = in_array(163, $permission);
}

if(isset($_SESSION['id']) && $array_view)
{
include_once("nav_forms.php");

$form_no = $_GET['form_no'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800"><?php echo $form_no; ?></h4>
            <a type="button" href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <?php
                    $details_query = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_no = '$form_no'");
                    $fetch_details = mysqli_fetch_array($details_query);
                    ?>
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light"><?php echo "Filed: ".date("F d, Y", strtotime($fetch_details['date'])); ?></h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="table-primary"><b>PO No:</b></td>
                                        <td><?php echo $fetch_details['po_no'];?></td>
                                        <td class="table-primary"><b>Invoice No:</b></td>
                                        <td><?php echo $fetch_details['invoice_no'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>Picker Name:</b></td>
                                        <td><?php echo $fetch_details['picker_name'];?></td>
                                        <td class="table-primary"><b>Checker Name:</b></td>
                                        <td><?php echo $fetch_details['checker_name'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>Driver Name:</b></td>
                                        <td><?php echo $fetch_details['driver_name'];?></td>
                                        <td class="table-primary"><b>Helper Name:</b></td>
                                        <td><?php echo $fetch_details['helper_name'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>New Driver:</b></td>
                                        <td><?php echo $fetch_details['new_driver'];?></td>
                                        <td class="table-primary"><b>New Helper:</b></td>
                                        <td><?php echo $fetch_details['new_helper'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Location</b></td>
                                        <td><?php echo $fetch_details['location'];?></td>
                                        <td class="table-warning"><b>Status:</b></td>
                                        <?php
                                        if($fetch_details['status'] == 1){
                                            echo '<td class="bg-success text-white"><b><i><center>Done</center></td>';
                                        }else{
                                            echo '<td class="bg-danger text-white"><b><i><center>Cancelled</center></td>';
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success text-center">
                                        <th>Error Type</th>
                                        <th>Invoiced Sku</th>
                                        <th>Invoiced Qty</th>
                                        <th>Actual Sku</th>
                                        <th>Actual Qty</th>
                                        <th>Uom</th>
                                        <th>Picked Qty</th>
                                        <th>Return Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sku_query = mysqli_query($conn,"SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");
                                    while($fetch_sku = mysqli_fetch_array($sku_query)){
                                        echo '<tr>';
                                        echo '<td><center>'.$fetch_sku['error_type'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['invoiced_sku'].' - '.$fetch_sku['invoiced_desc'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['invoiced_qty'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['picked_sku'].' - '.$fetch_sku['picked_desc'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['picked_qty'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['uom'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['qty'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['return_qty'].'</center></td>';
                                    }
                                    ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <center>
                            <?php
                            if($fetch_details['status'] == 1){
                            ?>
                                <a onclick="window.open('variance_print.php?type=<?php echo $type; ?>&form_no=<?php echo $form_no; ?>')" class="d-sm-inline-block btn btn-success btn-sm" disabled><i class="fa fa-sm fa-print"></i> Print Form</a>
                            <?php
                            }else{
                            ?>
                                <button class="d-sm-inline-block btn btn-success btn-sm" disabled><i class="fa fa-sm fa-print" ></i> Print Form</button>
                            <?php
                            }
                            ?>
                        </center>
                    </div>
                </div>
                <!-- End Table -->

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->

        <?php
        include_once("footer.php");
        ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>

<?php
}else{
    header("Location: denied.php");
}