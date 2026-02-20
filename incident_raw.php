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

if(isset($_SESSION['id']) && in_array(19, $permission))
{
include_once("nav_forms.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_report_raw");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_report_raw';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Incident Report</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(17, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add IR</button> -->
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'incident':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been filed successfully.';
                    break;
                case 'update-succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Employee updated successfully.';
                    break;
                case 'add-succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Employee has been added successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Employee name exists.';
                    break;
                case 'invalid_file':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-circle fa-sm"></i>&nbsp;<b>Error!</b> Please upload a valid CSV file.';
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
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add Employee</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="employee_add.php" id="EmployeeForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12">
                                            <label>Employee Name</label>
                                            <input type="text" class="form-control form-control-sm" name="name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="exampleInputAddress mb-1">Position: </label>
                                            <?php 
                                            $query ="SELECT * FROM tbl_roles GROUP BY role_name ORDER BY role_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
    
                                                <select class="form-control form-control-sm" name="position" required>
                                                <?php 
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['role_name'];?>"><?php echo $option['role_name']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Department:</label>
                                            <select class="form-control form-control-sm" name="department" required>
                                                <option value="Finance">Finance</option>
                                                <option value="HR">HR</option>
                                                <option value="IT">IT</option>
                                                <option value="Logistics">Logistics</option>
                                                <option value="Sales">Sales</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Shift:</label>
                                            <select class="form-control form-control-sm" name="shift">
                                                <option value="1">1st Shift</option>
                                                <option value="2">2nd Shift</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Location:</label>
                                            <?php 
                                            $query ="SELECT * FROM tbl_locations ORDER BY location_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
    
                                                <select class="form-control form-control-sm" name="location" required>
                                                <?php 
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['location_name'];?>"><?php echo $option['location_name']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add Employee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">IR List</h6> 
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                        <div class="d-flex align-items-center">
                            <div class="input-group mr-3" style="width:40%;">
                                <label for="locationFilter" class="mr-2">Filter by Location:</label>
                                <select id="locationFilter" name="location" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="">Select Location</option>
                                    <?php 
                                    $location_query = mysqli_query($conn,"SELECT * FROM tbl_user_locations WHERE user_id=".$_SESSION['id']);
                                    while ($fetch_loc = mysqli_fetch_assoc($location_query)) { 
                                        $loc_id = $fetch_loc['location_id'];
                                        $locname_query = mysqli_query($conn,"SELECT * FROM tbl_locations WHERE id = '$loc_id'");
                                        $fetch_locname = mysqli_fetch_array($locname_query);
                                    ?>
                                        <option value="<?php echo $fetch_locname['location_name']; ?>" <?php if(isset($_GET['location']) && $_GET['location'] == $fetch_locname['location_name']) echo 'selected'; ?>>
                                            <?php echo $fetch_locname['location_name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            </form>
                            <a type="button" class="btn btn-sm btn-light ml-auto" data-toggle="modal" <?php if(in_array(5, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>No.</th>
                                        <th>Date</th>
                                        <th>Reported By</th>
                                        <th>Location</th>
                                        <th>Ageing</th>
                                        <th>Status</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $now = date('Y-m-d');
                                        $datetime2 = new DateTime($now);
                                        $tag = $_SESSION['tag'];

                                        // Get the selected location from the dropdown
                                        $selected_location = isset($_GET['location']) ? $_GET['location'] : '';

                                        // Modify result query to include selected location filter
                                        $location_filter = !empty($selected_location) ? "AND location = '" . $selected_location . "'" : "";
                                        if($tag == 'ALL'){
                                            $result = mysqli_query($conn, "SELECT * FROM tbl_report_raw WHERE location IN (" . $location . ") " . $location_filter);
                                        }else{
                                            $result = mysqli_query($conn, "SELECT * FROM tbl_report_raw WHERE tag = '$tag' AND location IN (" . $location . ") " . $location_filter);
                                        }
                                        

                                        while ($row = mysqli_fetch_array($result)) {
                                            $datetime1 = new DateTime($row['report_date']);
                                            $difference = $datetime1->diff($datetime2);
                                            $diff = $difference->format('%a');

                                            echo '<tr>';
                                            echo '<td>' . $row['ref_no'] . '</td>';
                                            echo '<td>' . $row['date'] . '</td>';
                                            echo '<td>' . $row['reported_by'] . '</td>';
                                            echo '<td><center>' . $row['location'] . '</center></td>';

                                            if ($row['status'] == 0 || $row['status'] == '') {
                                                echo '<td class="text-danger"><center>' . $diff . ' Day(s)</center></td>';
                                                echo '<td><center><span class="badge badge-danger">Not Resolved</span></center></td>';
                                            } else {
                                                echo '<td><center><span class="badge badge-success">Resolved</span></center></td>';
                                                echo '<td><center><span class="badge badge-success">Resolved</span></center></td>';
                                            }
                                            ?>
                                            <td><center><a type="button" class="btn btn-warning btn-sm text-dark" <?php if (in_array(19, $permission)) { echo 'href="incident_view.php?ir=' . $row['ref_no'] . '"'; } else { echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-eye fa-sm"></i> View IR</a></center></td>
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

                <?php
                $update_query = mysqli_query($conn,"SELECT * FROM tbl_employees");
                while($fetch_update = mysqli_fetch_array($update_query)){
                ?>
                <!-- Update Modal -->
                <div class="modal fade" id="update<?php echo $fetch_update['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-dark">
                                <h6 class="modal-title text-warning" id="exampleModalLabel"><i class="fa fa-cog fa-sm"></i> Update Employee</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <form method="POST" action="employee_update.php">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12">
                                                <label>Employee Name</label>
                                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_update['employee_name']; ?>" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <label for="exampleInputAddress mb-1">Position: </label>
                                                <?php 
                                                $query ="SELECT * FROM tbl_roles GROUP BY role_name ORDER BY role_name ASC";
                                                $result = $conn->query($query);
                                                if($result->num_rows> 0){
                                                    $options= mysqli_fetch_all($result, MYSQLI_ASSOC);
                                                ?>
                                                    <select class="form-control form-control-sm" name="position" required>
                                                        <option value="<?php echo $fetch_update['position']; ?>"><?php echo $fetch_update['position']; ?> (Existing)</option>
                                                    <?php 
                                                    foreach ($options as $option) {
                                                    ?>
                                                        <option value="<?php echo $option['role_name'];?>"><?php echo $option['role_name']; ?> </option>
                                                <?php 
                                                    }
                                                }
                                                ?>
                                                    </select>
                                                </div>
                                            <div class="col-6">
                                                <label>Department:</label>
                                                <select class="form-control form-control-sm" name="department" required>
                                                    <option value="<?php echo $fetch_update['department']; ?>"><?php echo $fetch_update['department']; ?> (Existing)</option>
                                                    <option value="Finance">Finance</option>
                                                    <option value="HR">HR</option>
                                                    <option value="IT">IT</option>
                                                    <option value="Logistics">Logistics</option>
                                                    <option value="Sales">Sales</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6">
                                                <label>Shift:</label>
                                                <select class="form-control form-control-sm" name="shift">
                                                    <?php
                                                    if($fetch_update['shift'] == 1){
                                                        echo '<option value="1">1st Shift (Existing)</option>';
                                                    }else{
                                                        echo '<option value="2">2nd Shift (Existing)</option>';
                                                    }
                                                    ?>
                                                    <option value="1">1st Shift</option>
                                                    <option value="2">2nd Shift</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label>Location:</label>
                                                <select class="form-control form-control-sm" name="location">
                                                    <option value="<?php echo $fetch_update['location']; ?>"><?php echo $fetch_update['location']; ?> (Existing)</option>
                                                    <option value="CAINTA">CAINTA</option>
                                                    <option value="CDO">CDO</option>
                                                    <option value="CEBU">CEBU</option>
                                                    <option value="DAVAO">DAVAO</option>
                                                    <option value="ILOILO">ILOILO</option>
                                                    <option value="PANGASINAN">PANGASINAN</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12">
                                                <label>Employee Status</label>
                                                <select class="form-control form-control-sm" name="status">
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
                                </div>
                                <div class="modal-footer">
                                    <input type="text" name="id" value="<?php echo $fetch_update['id'];?>" hidden>
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
                    "order": [[1, "desc"]]
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
            $('#EmployeeForm')[0].reset();
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