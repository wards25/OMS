<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && $_SESSION['role'] == 'Admin')
{
include_once("nav_settings.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_kpi");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_kpi';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">KPI Setting</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Add KPI</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> KPI has been added successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Metric exists.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> KPI has been updated successfully.';
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

            <!-- Add Employee Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add KPI</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="kpi_add.php" id="ModuleForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Metric Name</label>
                                            <input type="text" class="form-control form-control-sm" name="metricname" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Location:</label>
                                            <?php 
                                            $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                            $result = $conn->query($query);

                                            if ($result && $result->num_rows > 0) {
                                                $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                            ?>
                                                <select class="form-control form-control-sm" name="location" required>
                                                <?php
                                                    foreach ($options as $option) {
                                                        $location_id = intval($option['location_id']); // Use intval to prevent SQL injection and ensure integer values
                                                        $location_query = $conn->query("SELECT * FROM tbl_locations WHERE id = '$location_id' AND is_active = '1'");
                                                        
                                                        if ($location_query && $location_query->num_rows > 0) {
                                                            $fetch_location = mysqli_fetch_array($location_query, MYSQLI_ASSOC);
                                                            if ($fetch_location) {
                                                                ?>
                                                                <option value="<?php echo htmlspecialchars($fetch_location['location_name']); ?>">
                                                                    <?php echo htmlspecialchars($fetch_location['location_name']); ?>
                                                                </option>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-5">
                                            <label>Success Rate:</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="number" class="form-control form-control-sm" name="success">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-5">
                                            <label>Warning Rate:</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="number" class="form-control form-control-sm" name="warning">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-5">
                                            <label>Danger Rate:</label>
                                        </div>
                                        <div class="col-7">
                                            <input type="number" class="form-control form-control-sm" name="danger">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add KPI</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing KPI List</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Metric Name</th>
                                        <th>Success</th>
                                        <th>Warning</th>
                                        <th>Danger</th>
                                        <th>Location</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_kpi");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['metric'].'</td>';
                                            echo '<td class="text-success"><center><b>'.$row['success'].'</td>';
                                            echo '<td class="text-warning"><center><b>'.$row['warning'].'</td>';
                                            echo '<td class="text-danger"><center><b>'.$row['danger'].'</td>';
                                            echo '<td><center>'.$row['location'].'</td>';
                                            echo '<td><center><button type="button" class="d-sm-inline-block btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#update'.$row['id'].'"><i class="fa fa-cog fa-sm"></i> Update</button></td></center>';
                                        }
                                    ?>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <?php
                $update_query = mysqli_query($conn,"SELECT * FROM tbl_kpi");
                while($fetch_update = mysqli_fetch_array($update_query)){
                ?>
                <!-- Update Modal -->
                <div class="modal fade" id="update<?php echo $fetch_update['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-dark">
                                <h6 class="modal-title text-warning" id="exampleModalLabel"><i class="fa fa-cog fa-sm"></i> Update KPI</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <form method="POST" action="kpi_update.php">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-6">
                                                <label>Metric Name</label>
                                                <input type="text" class="form-control form-control-sm" name="metricname" value="<?php echo $fetch_update['metric']; ?>" autocomplete="off" required>
                                            </div>
                                            <div class="col-6">
                                                <label>Metric Name</label>
                                                <?php 
                                                $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                                $result = $conn->query($query);

                                                if ($result && $result->num_rows > 0) {
                                                    $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                                ?>
                                                    <select class="form-control form-control-sm" name="location" required>
                                                        <option value="<?php echo $fetch_update['location']; ?>"><?php echo $fetch_update['location']; ?> (Existing)</option>
                                                    <?php
                                                        foreach ($options as $option) {
                                                            $location_id = intval($option['location_id']); // Use intval to prevent SQL injection and ensure integer values
                                                            $location_query = $conn->query("SELECT * FROM tbl_locations WHERE id = '$location_id' AND is_active = '1'");
                                                            
                                                            if ($location_query && $location_query->num_rows > 0) {
                                                                $fetch_location = mysqli_fetch_array($location_query, MYSQLI_ASSOC);
                                                                if ($fetch_location) {
                                                                    ?>
                                                                    <option value="<?php echo htmlspecialchars($fetch_location['location_name']); ?>">
                                                                        <?php echo htmlspecialchars($fetch_location['location_name']); ?>
                                                                    </option>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    ?>
                                                    </select>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-5">
                                                <label>Success Rate:</label>
                                            </div>
                                            <div class="col-7">
                                                <input type="number" class="form-control form-control-sm" name="success" value="<?php echo $fetch_update['success']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-5">
                                                <label>Warning Rate:</label>
                                            </div>
                                            <div class="col-7">
                                                <input type="number" class="form-control form-control-sm" name="warning" value="<?php echo $fetch_update['warning']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-row">
                                            <div class="col-5">
                                                <label>Danger Rate:</label>
                                            </div>
                                            <div class="col-7">
                                                <input type="number" class="form-control form-control-sm" name="danger" value="<?php echo $fetch_update['danger']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="text" name="update_id" value="<?php echo $fetch_update['id'];?>" hidden>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                    <button class="btn btn-success btn-sm" name="update" type="submit">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>

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