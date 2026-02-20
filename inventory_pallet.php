<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(143, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_pallets_raw");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_pallets_raw';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Pallet Report</h4>
            <a type="button" href="inventory_pallet_form.php" class="d-sm-inline-block btn btn-sm btn-success shadow-sm"><i class="fa-solid fa-list-check"></i> Count Pallets</a>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Inventory has been submitted successfully.';
                    break;
                case 'validate':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Inventory has been validated successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Already encoded inventory for this location.';
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
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(145, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Month</th>
                                        <th>Counted By</th>
                                        <th>Validated By</th>
                                        <th>Submit Date</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_pallets_raw");
                                        while($row = mysqli_fetch_array($result)) {

                                                echo '<tr>';
                                                echo '<td>'.$row['month'].'</td>';
                                                echo '<td>'.$row['submitted_by'].'</td>';
                                                echo '<td>'.$row['validated_by'].'</td>';
                                                echo '<td>'.$row['submitted_at'].'</td>';
                                                echo '<td><center>'.$row['location'].'</center></td>';
                                                if ($row['is_validated'] == 0 || $row['is_validated'] == '') {
                                                    echo '<td><center><span class="badge badge-danger">Not Validated</span></center></td>';

                                                    if (in_array(142, $permission)) {
                                                        echo '<td><center><a type="button" class="d-sm-inline-block btn btn-sm btn-info" ';
                                                        if (in_array(143, $permission)) {
                                                            echo 'href="inventory_pallet_validate.php?location=' . $row['location'] . '&month=' . $row['month'] . '"';
                                                        } else {
                                                            echo 'data-toggle="modal" data-target="#alertModal"';
                                                        }
                                                        echo '><i class="fa fa-check fa-sm"></i> Validate</a></center></td>';
                                                    } else {
                                                        echo '<td><center><button class="d-sm-inline-block btn btn-sm btn-secondary" disabled><i class="fa fa-check fa-sm"></i> Validate</button></center></td>';
                                                    }
                                                } else {
                                                    echo '<td><center><span class="badge badge-success">Validated</span></center></td>';
                                                    echo '<td><center><a type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark" ';

                                                    if (in_array(143, $permission)) {
                                                        echo 'href="inventory_pallet_view.php?location=' . $row['location'] . '&month=' . $row['month'] . '" target="_blank"';
                                                    } else {
                                                        echo 'data-toggle="modal" data-target="#alertModal"';
                                                    }

                                                    echo '><i class="fa fa-eye fa-sm"></i> View</a></center></td>';
                                                }
                                            ?>
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