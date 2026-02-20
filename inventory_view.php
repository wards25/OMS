<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(138, $permission))
{
include_once("nav_inventory.php");

$sku = $_GET['sku'];
$description = $_GET['description'];
$location = $_GET['location'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">
                <?php 
                echo $sku.' - '.$description;
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
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Trip number exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">SKU Summary</h6> 
                    </div>
                    <!-- DataTales Example -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Racklocation</th>
                                        <th>Fin Status</th>
                                        <th>Log Status</th>
                                        <th>Count Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_inventory_count WHERE sku = '$sku' GROUP BY racklocation");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td><center>' . $row['racklocation'] . '</center></td>';
                                        $racklocation = $row['racklocation'];
                                        $rack_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_rack WHERE racklocation = '$racklocation'");
                                        $fetch_rack = mysqli_fetch_assoc($rack_query);

                                        if($fetch_rack['fin_count'] == 1){
                                            echo '<td><span class="badge badge-sm badge-success">COUNTED</span></td>';
                                        }else{
                                            echo '<td><span class="badge badge-sm badge-warning">NOT COUNTED</span></td>';
                                        }

                                        if($fetch_rack['log_count'] == 1){
                                            echo '<td><span class="badge badge-sm badge-success">COUNTED</span></td>';
                                        }else{
                                            echo '<td><span class="badge badge-sm badge-warning">NOT COUNTED</span></td>';
                                        }

                                        if($fetch_rack['status'] == 'MATCH'){
                                            echo '<td><span class="badge badge-sm badge-success">MATCH</span></td>';
                                        }else{
                                            echo '<td><span class="badge badge-sm badge-danger">NOT MATCH</span></td>';
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