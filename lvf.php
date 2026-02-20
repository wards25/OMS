<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

unset($_SESSION['previous_pages']);

// Store the current page URL
$_SESSION['previous_pages'][] = $_SERVER['REQUEST_URI'];

// Keep only the last two pages in the session
if (count($_SESSION['previous_pages']) > 2) {
    array_shift($_SESSION['previous_pages']);
}

if(isset($_SESSION['id']) && in_array(97, $permission))
{
include_once("nav_forms.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Loading Variance</h4>
            <a type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" <?php if(in_array(95, $permission)){ echo 'href="variance_form.php?type=LVF"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Form</a>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Form has been submitted successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Invoice number encoded.';
                    break;
                case 'sku':
                    $statusType = 'alert-warning';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> No SKU encoded.';
                    break;
                case 'cancel':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Form has been cancelled successfully.';
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
                        <h6 class="m-0 font-weight-bold text-light">Form List</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(98, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Ref No</th>
                                        <th>PO No</th>
                                        <th>Invoice No</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>View</th>
                                        <th>Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_type='LVF'");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['form_no'].'</td>';
                                            echo '<td><center>'.$row['po_no'].'</center></td>';
                                            echo '<td><center>'.$row['invoice_no'].'</center></td>';
                                            echo '<td><center>'.$row['date'].'</center></td>';
                                            echo '<td><center>'.$row['location'].'</center></td>';

                                            if($row['status'] == 1){
                                                echo '<td><center><span class="badge badge-success">Done</span></center></td>';
                                            }else{
                                                echo '<td><center><span class="badge badge-danger">Cancelled</span></center></td>';
                                            }
                                            ?>
                                            <td><center><a class="d-sm-inline-block btn btn-sm btn-info" name="view" type="button" <?php if (in_array(97, $permission)) { echo 'href="variance_view.php?type=LVF&form_no='.$row['form_no'].'"'; } else {  echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-eye fa-sm"></i> View</a></center></td>
                                            <td><center><a class="d-sm-inline-block btn btn-sm btn-danger" type="button" <?php if (in_array(96, $permission)) { echo 'data-toggle="modal" data-target="#cancelModal'.$row['id'].'"'; } else {  echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-times fa-sm"></i> Cancel</a></center></td>

                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-times fa-sm"></i> Cancel Form</h6>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true"><small>×</small></span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">Do you want to cancel this form?</div>
                                                        <form method="POST" action="variance_cancel.php">
                                                        <div class="modal-footer">
                                                            <input type="text" value="<?php echo $row['id'];?>" name="cancel_id" hidden>
                                                            <input type="text" value="<?php echo $row['form_no'];?>" name="form_no" hidden>
                                                            <input type="text" value="<?php echo $row['form_type'];?>" name="type" hidden>
                                                            <input type="text" value="lvf.php" name="url" hidden>
                                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Cancel</button>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Cancel modal end -->
                                    <?php   
                                        }
                                    ?>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <!-- Export Modal -->
                <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-download fa-sm"></i> Export Data</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <form method="POST" action="variance_export.php?form=LVF">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>From: &nbsp;</label>
                                            <input type="date" class="form-control form-control-sm" name="from" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>To: &nbsp;</label>
                                            <input type="date" class="form-control form-control-sm" name="to" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Export</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

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
        // sorting table and removing alert error
        $(document).ready(function () {
            // Suppress all DataTables errors
            $.fn.dataTable.ext.errMode = 'none';

            try {
                const table = $('#dataTable');

                if ($.fn.DataTable.isDataTable(table)) {
                    table.DataTable().clear().destroy();
                }

                table.DataTable({
                    "order": [[3, "desc"]]
                });
            } catch (e) {
                console.warn('DataTable init skipped or failed:', e);
            }
        });

        // Export List 1
        function ExportList1() {
            $.ajax({
                type: "post",
                url: "export_list1.php",
                success: function(data) {
                    $('#export-list1').html(data);
                }
            });
        }
        ExportList1();   

        // Export List 2
        function ExportList2() {
            $.ajax({
                type: "post",
                url: "export_list2.php",
                success: function(data) {
                    $('#export-list2').html(data);
                }
            });
        }
        ExportList2(); 

        // Update Item
        $(document).on('click', '.btn-update', function(){
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "export_update.php",
                data: {id:id},
                success: function() {
                    ExportList1();
                    ExportList2();
                }
            });
        });

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#ModuleForm')[0].reset();
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