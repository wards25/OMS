<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_SERVER['REQUEST_URI'];

unset($_SESSION['previous_pages']);

// Store the current page URL
$_SESSION['previous_pages'][] = $_SERVER['REQUEST_URI'];

// Keep only the last two pages in the session
if (count($_SESSION['previous_pages']) > 2) {
    array_shift($_SESSION['previous_pages']);
}

//get data from checklist
$location = $_GET['location'];
$date = $_GET['date'];
$shift = $_GET['shift'];
$shift_type = $_GET['shift_type'];

// check if already submitted
$check_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
$check_submit = mysqli_num_rows($check_query);

if(isset($_SESSION['id']) && $check_submit == 0 && in_array(29, $permission))
{
include_once("nav_whse.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Encode Checklist</h4>
            <a type="button" href="checklist.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Employee has been added successfully.';
                    break;
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'incident':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been filed successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Employee name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">WH Attendance Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="table-primary"><b>Location</b></td>
                                        <td><?php echo $location; ?></td>
                                        <td class="table-primary"><b>Date</b></td>
                                        <td><?php echo date("F d, Y", strtotime($date)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>Shift</b></td>
                                        <td><?php if($shift == 1){ echo '1st Shift'; }else{ echo '2nd Shift'; } ?></td>
                                        <td class="table-primary"><b>Shift Type</b></td>
                                        <td><?php if($shift_type == 1){ echo 'OPENING'; }else{ echo 'CLOSING'; } ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <form method="POST" action="attendance_submit.php" enctype="multipart/form-data">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Attendance</th>
                                        <th>Reason</th>
                                        <th>Uniform</th>
                                        <th>ID</th>
                                        <th>Vest</th>
                                        <th>S.Shoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location = '$location' AND shift = '$shift' AND is_active = '1' AND department = 'Logistics-WH' ORDER BY employee_name");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            $employeeId = $row['id'];
                                            echo '<tr>';
                                            echo '<td class="align-middle">'.$row['employee_name'].'</td>';
                                            echo '<td class="align-middle">'.$row['position'].'</td>';
                                            ?>
                                            <input type="text" name="employee_name[<?php echo $employeeId; ?>]" value="<?php echo $row['employee_name']; ?>" hidden>
                                            <input type="text" name="position[<?php echo $employeeId; ?>]" value="<?php echo $row['position']; ?>" hidden>
                                            <td class="align-middle"><center><input type="checkbox" name="attendance[<?php echo $employeeId; ?>]" value="1" data-id="<?php echo $employeeId; ?>" id="attendance-<?php echo $employeeId; ?>" class="attendance-checkbox"></center></td>
                                            <td class="align-middle"><center><select class="form-control form-control-sm" name="remarks[<?php echo $employeeId; ?>]" id="remarks-<?php echo $employeeId; ?>" required>
                                                <option value=""></option>
                                                <option value="AWOL">AWOL</option>
                                                <option value="Halfday">Halfday</option>
                                                <option value="Late">Late</option>
                                                <option value="Resigned">Resigned</option>
                                                <option value="Sick Leave">Sick Leave</option>
                                                <option value="Suspended">Suspended</option>
                                                <option value="Unauthorized Leave">Unauthorized Leave</option>
                                                <option value="Undertime">Undertime</option>
                                                <option value="Vacation Leave">Vacation Leave</option>
                                            </select></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="uniform[<?php echo $employeeId; ?>]" value="1" id="uniform-<?php echo $employeeId; ?>"></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="identification[<?php echo $employeeId; ?>]" value="1" id="identification-<?php echo $employeeId; ?>"></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="vest[<?php echo $employeeId; ?>]" value="1" id="vest-<?php echo $employeeId; ?>"></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="safety_shoes[<?php echo $employeeId; ?>]" value="1" id="safety_shoes-<?php echo $employeeId; ?>"></center></td>
                                        <?php
                                        }
                                        ?>
                                        </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="text" name="location" value="<?php echo $location; ?>" hidden>
                        <input type="text" name="date" value="<?php echo $date; ?>" hidden>
                        <input type="text" name="shift" value="<?php echo $shift; ?>" hidden>
                        <input type="text" name="shift_type" value="<?php echo $shift_type; ?>" hidden>
                        <?php
                        if($shift_type == 2){
                        }else{
                        ?>
                        <div class="card mb-4">
                            <div class="d-sm-flex card-header justify-content-between py-1 bg-primary">
                                <h6 class="m-0 font-weight-bold text-light">Attach Selfie Attendance</h6>
                            </div>
                            <div class="card-body">
                                <div class="input-group mb-3">
                                    <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="image" required/>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                        <hr>
                        <center>
                            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark add-btn" data-toggle="modal" <?php if(in_array(1, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa-solid fa-user-plus"></i> Add Employee</button>
                            <?php
                            $deadline_query = mysqli_query($conn, "SELECT * FROM tbl_deadline WHERE shift='$shift' AND shift_type='$shift_type' AND location='$location' AND submit_type='0'");

                            if ($fetch_deadline = mysqli_fetch_array($deadline_query)) {
                                $time_now = date("H:i:s");
                                $time_from = $fetch_deadline['time_from'];
                                $time_to = $fetch_deadline['time_to'];

                                if ($time_now >= $time_from && $time_now <= $time_to) {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-check"></i> Submit</a>';
                                } else {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#DeadlineModal"><i class="fa fa-sm fa-check"></i> Submit</a>';
                                }
                            } else {
                                // Handle the case where no deadlines are found
                                echo '<small><i>No cut-off found for the specified criteria.<i></small>';
                            }
                            ?>
                        </center>
                            <!-- Submit Modal -->
                            <div class="modal fade" id="SubmitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success">
                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check fa-sm"></i> Submit Form</h6>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><small>×</small></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Do you want to submit this form?</div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit modal end -->
                        </form>
                    </div>
                </div>
                <!-- End Table -->

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
                                    <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url; ?>" hidden>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                    <button class="btn btn-success btn-sm" name="submit" type="submit">Add Employee</button>
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

        </div>\
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <script>
        $(document).on('change','.attendance-checkbox',function(){
            var id = $(this).data('id');
            // var selectRemarks = $('#remarks-' + id);
            if($(this).is(':checked')) {
                $("#remarks-" + id).prop('disabled',true).val('');
                // $("#remarks" + id).val('');
                $("#remarks-" + id).prop('required',false);
            } else {
                $("#remarks-" + id).prop('disabled',false);
                $("#remarks-" + id).prop('required',true);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Get all attendance checkboxes
            const attendanceCheckboxes = document.querySelectorAll('.attendance-checkbox');

            attendanceCheckboxes.forEach(function(attendanceCheckbox) {
                const employeeId = attendanceCheckbox.getAttribute('data-id');

                // Get related checkboxes
                const uniformCheckbox = document.getElementById(`uniform-${employeeId}`);
                const identificationCheckbox = document.getElementById(`identification-${employeeId}`);
                const vestCheckbox = document.getElementById(`vest-${employeeId}`);
                const safetyShoesCheckbox = document.getElementById(`safety_shoes-${employeeId}`);

                // Function to toggle checkboxes based on attendance checkbox
                function toggleRelatedCheckboxes() {
                    const isChecked = attendanceCheckbox.checked;
                    uniformCheckbox.disabled = !isChecked;
                    identificationCheckbox.disabled = !isChecked;
                    vestCheckbox.disabled = !isChecked;
                    safetyShoesCheckbox.disabled = !isChecked;

                    // Set related checkboxes checked status based on attendance checkbox
                    uniformCheckbox.checked = isChecked;
                    identificationCheckbox.checked = isChecked;
                    vestCheckbox.checked = isChecked;
                    safetyShoesCheckbox.checked = isChecked;
                }

                // Initialize state on page load
                toggleRelatedCheckboxes();

                // Add change event listener to the attendance checkbox
                attendanceCheckbox.addEventListener('change', toggleRelatedCheckboxes);
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