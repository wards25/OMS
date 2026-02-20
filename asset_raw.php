<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(16, $permission))
{
include_once("nav_whse.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_asset_raw");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_asset_raw';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Asset Report</h4>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Location has been added successfully.';
                    break;
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'incident':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been filed successfully.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Location has been updated successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Location name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Report List</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(22, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(8, $permission)){ echo 'href="asset.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa-solid fa-dolly"></i> Asset List</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Date</th>
                                        <th>Reported By</th>
                                        <th>Shift</th>
                                        <th>Shift Type</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT * FROM tbl_asset_raw WHERE location IN ($location) GROUP BY date, location, shift, shift_type");

                                        while($row = mysqli_fetch_array($result)) {

                                                echo '<tr>';
                                                echo '<td>'.$row['date'].'</td>';
                                                echo '<td>'.$row['submitted_by'].'</td>';

                                                if($row['shift'] == 1){
                                                    echo '<td><center>1st Shift</center></td>';
                                                }else{
                                                    echo '<td><center>2nd Shift</center></td>';
                                                }

                                                if($row['shift_type'] == 1){
                                                    echo '<td><center>OPENING</center></td>';
                                                }else{
                                                    echo '<td><center>CLOSING</center></td>';
                                                }

                                                echo '<td><center>'.$row['location'].'</center></td>';
                                                if($row['is_validated'] == 0 || $row['is_validated'] == ''){
                                                    echo '<td><center><span class="badge badge-danger">Not Validated</span></center></td>';
                                                }else{
                                                    echo '<td><center><span class="badge badge-success">Validated</span></center></td>';
                                                }
                                            ?>
                                                <td><center><a type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark" <?php if(in_array(19, $permission)){ echo 'href="asset_view.php?location='.$row['location'].'&date='.$row['date'].'&shift='.$row['shift'].'&shift_type='.$row['shift_type'].'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-eye fa-sm"></i> View Report</a></center></td>
                                        </tr>
                                    <?php
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
                    "order": [[0, "desc"]]
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