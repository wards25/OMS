<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(180, $permission))
{
include_once("nav_forms.php");

    if(!isset($_GET['location'])){
        $loc_query = mysqli_query($conn,"SELECT * FROM tbl_user_locations WHERE user_id = ".$_SESSION['id']);
        $fetch_loc = mysqli_fetch_array($loc_query);
        $tbl_loc = $fetch_loc['location_id'];
        $locname_query = mysqli_query($conn,"SELECT * FROM tbl_locations WHERE id = '$tbl_loc'");
        $fetch_locname = mysqli_fetch_array($locname_query);
        $location = $fetch_locname['location_name'];
    }else{
        $location=$_GET['location'];
    } 
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Search Check</h4>
            <!-- <a type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" <?php if(in_array(87, $permission)){ echo 'href="variance_form.php?type=PVF"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Form</a> -->
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
                case 'validate':
                    $style = '';
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Form has been validated successfully.';
                    break;
                case 'enrolled':
                    $style = 'style="background-color:#cc589c; color:#ffffff;"';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Form has been enrolled to Odoo.';
                    break;
                case 'resend':
                    $style = '';
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Email confirmation has been sent successfully.';
                    break;
                case 'reject':
                    $style = '';
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Form has been rejected successfully.';
                    break;
                case 'err':
                    $style = '';
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Invoice number encoded.';
                    break;
                default:
                    $style = '';
                    $statusType = '';
                    $statusMsg = '';
            }
        }
        ?>

        <!-- Display status message -->
        <?php if(!empty($statusMsg)){ ?>
        <div class="alert <?php echo $statusType; ?> alert-dismissable fade show" <?php echo $style; ?> role="alert">
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
                        <div class="d-flex flex-wrap justify-content-between align-items-start" style="gap: 10px;">
                            <div class="flex-grow-1 min-w-0 me-md-3">
                                <div class="table-responsive">
                                    <?php
                                    $notvalidated_count = 0;
                                    $validated_count = 0;
                                    $data_query = mysqli_query($conn,"SELECT status FROM tbl_check_raw");
                                    while($fetch_data = mysqli_fetch_assoc($data_query)){
                                        if($fetch_data['status'] == 0){
                                            $notvalidated_count += 1;
                                        }else if($fetch_data['status'] == 1){
                                            $validated_count += 1;
                                        }else{

                                        }
                                    }
                                    $total_count = $notvalidated_count + $validated_count;
                                    ?>
                                    <table class="table table-bordered table-sm mb-0" style="min-width: 100%; max-width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td class="table-success"><b>Validated:</b></td>
                                                <td class="text-center"><b><?php echo $validated_count; ?></b></td>
                                                <td class="table-danger"><b>Not Validated:</b></td>
                                                <td class="text-center"><b><?php echo $notvalidated_count; ?></b></td>
                                            </tr>
                                            <tr>
                                                <td class="table-info" colspan="3"><b>Total Submitted:</b></td>
                                                <td class="text-center" colspan="3"><b><?php echo $total_count; ?></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Export Button -->
                            <div class="flex-shrink-0">
                                <a type="button" class="btn btn-sm btn-light mt-2 mt-md-0" data-toggle="modal"
                                   <?php if(in_array(182, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>>
                                    <i class="fa fa-download"></i> Export Data
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Ref No</th>
                                        <th>Website</th>
                                        <th>Order No</th>
                                        <th>Deposit Date</th>
                                        <th>Status</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_check_raw");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['serial_no'].'</td>';
                                            echo '<td>'.$row['website'].'</td>';
                                            echo '<td>'.$row['order_no'].'</td>';
                                            echo '<td>'.$row['deposit_date'].'</td>';

                                            if($row['status'] == 1){
                                                echo '<td><span class="badge badge-success">Validated</span></td>';
                                            }else if($row['status'] == 3){
                                                echo '<td><span class="badge badge-secondary text-white">Rejected</span></td>';
                                            }else{
                                                echo '<td><span class="badge badge-danger">Not Validated</span></td>';
                                            }
                                            ?>

                                            <td>
                                                <center>
                                                    <a class="d-sm-inline-block btn btn-sm btn-info" name="view" type="button"
                                                       <?php 
                                                       if (in_array(180, $permission)) { 
                                                           echo 'href="check_view.php?link=check_search.php&order_no='.$row['order_no'].'&deposit_date='.$row['deposit_date'].'"'; 
                                                       } else {  
                                                           echo 'data-toggle="modal" data-target="#alertModal"'; 
                                                       } 
                                                       ?>>
                                                       <i class="fa fa-eye fa-sm"></i>
                                                    </a>
                                                </center>
                                            </td>
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
                            <form method="POST" action="cif_export.php?form=HCP INDIVIDUAL">
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
                    "order": [[3, "asc"]]
                });
            } catch (e) {
                console.warn('DataTable init skipped or failed:', e);
            }
        });

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