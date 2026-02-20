<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(119, $permission))
{
include_once("nav_trips.php");

$sono = $_GET['sono'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">
                <?php 
                echo $sono;
                ?>
            </h4>
            <button class="input-group-addon btn btn-secondary btn-sm" onclick='window.close()'><i class="fa fa-sm fa-times"></i> Close</button>
        </div>
        <hr>

        <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 2000);
        </script>

        <?php
        // Get status message
        if(!empty($_GET['status'])){
            switch($_GET['status']){
                case 'add':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Picker/Checker has been assigned successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Trips has been imported successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Trip number exists.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> PO has been updated successfully.';
                    break;
                default:
                    $statusType = '';
                    $statusMsg = '';
            }
        }
        ?>

        <!-- Display status message -->
        <?php if(!empty($statusMsg)){ ?>
        <div class="alert <?php echo $statusType; ?> alert-dismissable fade show" role="alert">
             <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $statusMsg; ?>
        </div>
        <?php } ?>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">SO Summary</h6> 
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                            <?php
                                            $details_query = mysqli_query($conn, "SELECT *,sum(finalqty) FROM tbl_trips_raw WHERE sono = '$sono'");
                                            $fetch_details = mysqli_fetch_assoc($details_query);
                                            ?>
                                            <tbody>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Upload Date:</td>
                                                    <td><?php if(empty($fetch_details['dtr'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['dtr']; } ?></td>
                                                    <td style="background: #f2f2f2;"><b>PO #:</td>
                                                    <td><?php if(empty($fetch_details['pono'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['pono']; } ?></td>

                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Invoice Status:</td>
                                                    <?php
                                                    if($fetch_details['invoicing_status'] == 'INVOICED'){
                                                        echo '<td><span class="badge badge-success">' . $fetch_details['invoicing_status'] . '</span></td>';
                                                    }else{
                                                        echo '<td><span class="badge badge-danger">NOT INVOICED</span></td>';
                                                    }
                                                    ?>
                                                    <td style="background: #f2f2f2;"><b>Total Qty:</td>
                                                    <td><?php if(empty($fetch_details['sum(finalqty)'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['sum(finalqty)']; } ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Barcode</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Final Qty</th>
                                        <th>UOM</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_trips_raw WHERE sono = '$sono'");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td><center>' . $row['barcode'] . '</center></td>';
                                        echo '<td><center>' . $row['sku'] . '</center></td>';
                                        echo '<td>' . $row['description'] . '</td>';
                                        echo '<td><center>' . $row['finalqty'] . '</center></td>';
                                        echo '<td><center>' . $row['uom'] . '</center></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
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

    <script>
        // Reset add modal button
        $('.assign-btn').click(function(){
            $('#AssignForm')[0].reset();
        });
    </script>

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