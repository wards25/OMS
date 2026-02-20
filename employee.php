<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(3, $permission))
{
include_once("nav_employee.php");
include_once("export_modal.php");

// delete tbl_export in db 
mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

$export_query = mysqli_query($conn,"DESCRIBE tbl_employees");
while($fetch_export = mysqli_fetch_array($export_query)) {
    $col_name = $fetch_export['Field'];
    $tbl_name = 'tbl_employees';
    mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
}
    $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
    <form action="employee_import.php" method="post" enctype="multipart/form-data">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Employee List</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(1, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa-solid fa-user-plus"></i> Add Employee</button>
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

        <!-- DataTales Row -->
        <div class="card shadow mb-4">
            <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                        <?php 
                        $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                        $result = $conn->query($query);

                        if ($result && $result->num_rows > 0) {
                            $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        ?>
                            <select class="form-control form-control-sm" name="location">
                                <option value="">Select Location</option>
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
                        &nbsp;&nbsp;&nbsp;
                        <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                    <div class="input-group-prepend">
                        <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(4, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Validate</span>
                        &nbsp;
                        <span><a onclick="window.location.href='IMPORT_EMPLOYEE_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
                    </div>
                </div>
            </div>
        </div>

            <!-- Upload Modal-->
            <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-upload fa-sm"></i> Upload File</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">Are you sure you want to upload this csv file?</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-success btn-sm" name="submit" value="Upload">
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </form>

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-user-plus"></i> Add Employee</h6>
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
                                    <div class="form-row">
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
                                            <?php
                                            $query ="SELECT * FROM tbl_employees GROUP BY department ORDER BY department ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
    
                                                <select class="form-control form-control-sm" name="department" required>
                                                <?php 
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['department'];?>"><?php echo $option['department']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
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
                    <!-- <div class="card-header py-2">
                        <h6 class="m-0 font-weight-bold text-light">Existing Employee List</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="input-group mr-3" style="width:40%;">
                                <label for="shiftFilter" class="mr-2">Filter by Shift:</label>
                                <select id="shiftFilter" class="form-control form-control-sm" onchange="filterByShift()">
                                    <option value="">All Shifts</option>
                                    <option value="1">1st Shift</option>
                                    <option value="2">2nd Shift</option>
                                </select>
                            </div>
                            <a type="button" class="btn btn-sm btn-light ml-auto" <?php if(in_array(39, $permission)){ echo 'href="ofattendance_raw.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-file"></i> Office Report</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(31, $permission)){ echo 'href="attendance_raw.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-file"></i> WH Report</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(5, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Shift</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $shift_filter = isset($_GET['shift']) ? intval($_GET['shift']) : '';

                                        $shift_condition = $shift_filter ? "AND shift = $shift_filter" : '';

                                        $result = mysqli_query($conn, "SELECT * FROM tbl_employees WHERE location IN (" . $location . ") $shift_condition");
                                        while ($row = mysqli_fetch_array($result)) {
                                        ?>
                                            <tr>
                                                <?php 
                                                echo '<td>' . $row['employee_name'] . '</td>';
                                                echo '<td>' . $row['position'] . '</td>';
                                                echo '<td>' . $row['department'] . '</td>';
                                                
                                                echo $row['shift'] == 1 ? '<td><center>1st Shift</center></td>' : '<td><center>2nd Shift</center></td>';

                                                echo '<td><center>' . $row['location'] . '</center></td>';
                                                echo $row['is_active'] == 1 ? '<td><center><span class="badge badge-success">Active</span></center></td>' : '<td><center><span class="badge badge-danger">Inactive</span></center></td>';
                                                ?>
                                                <td><center><button type="button" class="d-sm-inline-block btn btn-sm btn-outline-warning" data-toggle="modal" <?php if (in_array(2, $permission)) { echo 'data-target="#update' . $row['id'] . '"'; } else { echo 'data-target="#alertModal"'; } ?>><i class="fa fa-cog fa-sm fa-fw"></i> Update</button></center></td>
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
                                        <div class="form-row">
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
                                        <div class="form-row">
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
                                    <div class="form-group">
                                        <div class="form-row">
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

        function filterByShift() {
            var shift = document.getElementById('shiftFilter').value;
            window.location.href = window.location.pathname + '?shift=' + shift;
        }

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