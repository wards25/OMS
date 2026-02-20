<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(4, $permission))
{
include_once("nav.php");

$error_query = mysqli_query($conn,"SELECT user,sum(error) FROM tbl_employees_error WHERE user = '$user'");
$fetch_error = mysqli_fetch_array($error_query);
$error_count = $fetch_error['sum(error)'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Validate Upload</h4>
            <a type="button" href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="d-sm-inline-block text-danger">Error Count: <b class="text-danger"><?php echo $error_count; ?></b></h6>
                            <button class="d-sm-inline-block btn btn-success btn-sm" <?php if($error_count > 0){ echo 'disabled'; }else{ echo 'data-toggle="modal" data-target="#SubmitModal"'; } ?>><i class="fa fa-sm fa-upload"></i> Upload</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="bg-primary text-center text-light">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Shift</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sku_query = mysqli_query($conn,"SELECT * FROM tbl_employees_error WHERE user = '$user' ORDER BY error DESC");
                                    while($fetch_sku = mysqli_fetch_array($sku_query)){
                                        if($fetch_sku['error'] == 1){
                                            echo '<tr class="table-danger">';
                                        }else{
                                            echo '<tr>';
                                        }
                                        echo '<td><center>'.$fetch_sku['employee_name'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['position'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['shift'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['department'].'</center></td>';
                                        echo '<td><center>'.$fetch_sku['location'].'</center></td>';
                                    }
                                    ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->
                <!-- Submit Modal -->
                <form method="POST" action="employee_validate.php">
                <div class="modal fade" id="SubmitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content"> 
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-upload fa-sm"></i> Upload Data</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">Do you want to upload this data?</div>
                            <div class="modal-footer">
                                <input type="text" value=<?php echo $_GET['location']; ?> name="location" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" type="submit" name="submit">Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit modal end -->
                </form>

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