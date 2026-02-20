<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

unset($_SESSION['previous_pages']);

// Store the current page URL
$_SESSION['previous_pages'][] = $_SERVER['REQUEST_URI'];
if (count($_SESSION['previous_pages']) > 2) {

// Keep only the last two pages in the session
    array_shift($_SESSION['previous_pages']);
}

if(isset($_SESSION['id']) && in_array(13, $permission))
{
include_once("nav_admin.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_warehouselock_location");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_warehouselock_location';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Warehouse Lock</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(11, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa-solid fa-shop-lock"></i> Add Location</button>
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

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-shop-lock"></i> Add Warehouse Lock</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                        <form method="POST" action="warehouselock_add.php" id="WarehouseLockForm">
                            <div class="modal-body">
                                <div class="form-row">
                                    <div class="col-12">
                                        <label>Warehouse Lock Name:</label>
                                        <input type="text" class="form-control form-control-sm" name="lockname" required>
                                    </div>
                                </div>
                                <br>
                                <div class="form-row">
                                    <div class="col-12">
                                        <label>Location:</label>
                                        <?php 
                                        $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                        $result = $conn->query($query);

                                        if ($result && $result->num_rows > 0) {
                                            $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                        ?>
                                            <select class="form-control form-control-sm" name="location">
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
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add Location</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
            $update_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_location");
            while($fetch_update = mysqli_fetch_array($update_query)){
            ?>
            <!-- Update Modal -->
            <div class="modal fade" id="update<?php echo $fetch_update['id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <h6 class="modal-title text-warning" id="exampleModalLabel"><i class="fa fa-cog fa-sm"></i> Update Location</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="warehouselock_update.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Location Name:</label>
                                            <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_update['location_name']?>" name="location_name" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Condition:</label>
                                            <select class="form-control form-control-sm" name="cond">
                                                <?php
                                                if($fetch_update['is_active'] == 1){
                                                    echo '<option value="1">ACTIVE (Existing)</option>';
                                                }else{
                                                    echo '<option value="0">INACTIVE (Existing)</option>';
                                                }
                                                ?>
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Location:</label>
                                            <?php 
                                            $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                            $result = $conn->query($query);

                                            if ($result && $result->num_rows > 0) {
                                                $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                            ?>
                                                <select class="form-control form-control-sm" name="location">
                                                    <option value="<?php echo $fetch_update['location'];?>"><?php echo $fetch_update['location']; ?> (Existing)</option>
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
                            </div>
                            <div class="modal-footer">
                                <input type="text" value="<?php echo $fetch_update['id'];?>" name="update_id" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
            
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing Location List</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(15, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(27, $permission)){ echo 'href="warehouselock_raw.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-file"></i> Report Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Location Name</th>
                                        <th>Condition</th>
                                        <th>Location</th>
                                        <th>Update</th>
                                        <th>Incident Report</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_location WHERE location IN (" . $location . ")");
                                        while($row = mysqli_fetch_array($result)){
                                            
                                            if($row['report'] >= 1){
                                                echo '<tr class="table-danger">';
                                            }else{
                                                echo '<tr>';
                                            }

                                            echo '<td class="align-middle">'.$row['location_name'].'</td>';

                                            if($row['is_active'] == 1){
                                                echo '<td class="align-middle"><center><span class="badge badge-success">Active</span></center></td>';
                                            }else{
                                                echo '<td class="align-middle"><center><span class="badge badge-danger">Inactive</span></center></td>';
                                            }

                                            echo '<td class="align-middle"><center>'.$row['location'].'</center></td>';
                                            ?>
                                            <td class="align-middle"><center><button type="button" class="d-sm-inline-block btn btn-sm btn-outline-warning" data-toggle="modal" <?php if(in_array(7, $permission)){ echo "data-target=#update".$row['id']; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-cog fa-sm fa-fw"></i> Update</button></td></center>

                                        <?php
                                            $subject = $row['location_name'];
                                            $location = $row['location'];

                                            $incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE location='$location' AND subject='$subject' AND status=0");
                                            $check_incident = mysqli_num_rows($incident_query);

                                            if($check_incident >= 1){
                                                echo '<td>';
                                                while($fetch_incident = mysqli_fetch_array($incident_query)){
                                                ?>
                                                    <center><a type="button" class="text-dark" <?php if(in_array(19, $permission)){ echo 'href="incident_view.php?ir='.$fetch_incident['ref_no'].'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><small><?php echo $fetch_incident['ref_no']; ?></small></a></center>
                                            <?php
                                                }
                                                echo '</td>';
                                            }else{
                                                echo '<td class="align-middle"><center><small class="text-dark"><i>No Incident Report Filed</i></small></center></td>';
                                            }
                                            ?>
                                            <td class="align-middle"><center><a type="button" class="btn btn-sm btn-danger" <?php if(in_array(17, $permission)){ echo 'href="incident_form.php?location='.$row['location'].'&date='.date("Y-m-d").'&shift=0&shift_type=0&table=tbl_warehouselock_location&tbl_column=location_name&subject='.$row['location_name'].'&tag=LOGISTICS&module_id='.$row['id'].'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-exclamation-triangle fa-sm"></i> File IR</a></center></td>
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

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#WarehouseLockForm')[0].reset();
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