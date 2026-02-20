<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']))
{
include_once("nav.php");

$tmno = $_GET['tmno'];
$dtr = $_GET['dtr'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">For Sorting</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(77, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add SKU</button> -->
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
                case 'succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Pick List has been validated successfully.';
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Picklist No</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <?php
                                        // Query to get all unique brcodes
                                        $header_query = mysqli_query($conn,"SELECT * FROM tbl_trips_raw WHERE tmno = '$tmno' GROUP BY brcode");
                                        while($fetch_header = mysqli_fetch_assoc($header_query)){
                                            echo '<th><center>MDC-'.$fetch_header['brcode'].'</center></th>';
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <?php
                                    // Query to select sku and company details
                                    $result = mysqli_query($conn,"SELECT sku, description, barcode, company, picklistno FROM tbl_trips_raw WHERE tmno = '$tmno' GROUP BY sku ORDER BY picklistno");
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td><center>'.$row['picklistno'].'</td>';

                                        echo '<td>'.$row['sku'].' - '.$row['description'].'</td>';
                                        echo '<td><center>'.$row['barcode'].'</td>';

                                        // Get total quantity ordered for each brcode and sku combination
                                        $header_query = mysqli_query($conn, "SELECT * FROM tbl_trips_raw WHERE tmno = '$tmno' GROUP BY brcode");
                                        while($fetch_header = mysqli_fetch_assoc($header_query)){
                                            // Get total quantity for the current brcode and sku
                                            $brcode = $fetch_header['brcode'];
                                            $sku = $row['sku'];
                                            
                                            $brcode_qty_query = mysqli_query($conn, "SELECT SUM(finalqty) as total_qty FROM tbl_trips_raw WHERE tmno = '$tmno' AND brcode = '$brcode' AND sku = '$sku'");
                                            $brcode_qty = mysqli_fetch_assoc($brcode_qty_query);

                                            echo '<td><center>'.$brcode_qty['total_qty'].'</center></td>';
                                        }

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