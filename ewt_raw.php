<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(174, $permission))
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
            <h4 class="mb-0 text-gray-800">Validate EWT</h4>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Ref No</th>
                                        <th>Website</th>
                                        <th>Order No</th>
                                        <th>Order Date</th>
                                        <th>Status</th>
                                        <th>Validate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_ewt_raw WHERE status = '0'");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['serial_no'].'</td>';
                                            echo '<td>'.$row['website'].'</td>';
                                            echo '<td>'.$row['order_no'].'</td>';
                                            echo '<td>'.$row['order_date'].'</td>';

                                            if($row['status'] == 1){
                                                echo '<td><span class="badge badge-success">Validated</span></td>';
                                            }else if($row['status'] == 2){
                                                echo '<td><span class="badge badge-primary text-white">Invoiced</span></td>';
                                            }else{
                                                echo '<td><span class="badge badge-danger">Not Validated</span></td>';
                                            }
                                            ?>

                                            <td>
                                                <center>
                                                    <a class="d-sm-inline-block btn btn-sm btn-info" name="view" type="button"
                                                       <?php 
                                                       if (in_array(175, $permission)) { 
                                                           echo 'href="ewt_view.php?link=ewt_raw.php&order_no='.$row['order_no'].'&order_date='.$row['order_date'].'"'; 
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