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

if(isset($_SESSION['id']) && in_array(38, $permission))
{
include_once("nav_whse.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Validate Checklist</h4>
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
                        <h6 class="m-0 font-weight-bold text-light">Office Attendance Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <?php
                                $details_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' GROUP BY submitted_by");
                                $fetch_details = mysqli_fetch_array($details_query);
                                ?>
                                <tbody>
                                    <tr>
                                        <td class="table-primary"><b>Location</b></td>
                                        <td><?php echo $location; ?></td>
                                        <td class="table-primary"><b>Date</b></td>
                                        <td><?php echo date("F d, Y", strtotime($date)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>Shift</b></td>
                                        <td colspan="3"><?php if($shift == 1){ echo '1st Shift'; }else{ echo '2nd Shift'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Submitted By</b></td>
                                        <td><?php echo $fetch_details['submitted_by']; ?></td>
                                        <td class="table-warning"><b>Submitted At</b></td>
                                        <td><?php echo $fetch_details['submitted_at']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <form method="POST" action="ofattendance_validate_submit.php" enctype="multipart/form-data">
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
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            if($row['reason'] == 'Resigned' || $row['reason'] == 'AWOL'){
                                                echo '<tr class="table-danger">';
                                            }else{
                                                echo '<tr>';
                                            }

                                            echo '<td class="align-middle">' . $row['employee_name'] . '</td>';
                                            echo '<td class="align-middle">' . $row['position'] . '</td>';
                                            ?>

                                            <input type="text" name="employee_name[<?php echo $row['id']; ?>]" value="<?php echo $row['employee_name']; ?>" hidden>

                                            <!-- Hidden fields for unchecked checkboxes -->
                                            <input type="hidden" name="attendance[<?php echo $row['id']; ?>]" value="0">
                                            <td class="align-middle"><center><input type="checkbox" name="attendance[<?php echo $row['id']; ?>]" value="1" <?php echo ($row['attendance'] == 1) ? 'checked' : ''; ?> data-id="<?php echo $row['id']; ?>" class="attendance-checkbox"></center></td>

                                            <td class="align-middle">
                                                <center>
                                                    <select class="form-control form-control-sm" name="remarks[<?php echo $row['id']; ?>]" id="remarks-<?php echo $row['id']; ?>" <?php echo ($row['attendance'] == 1) ? 'disabled' : ''; ?> required>
                                                        <option value="<?php echo $row['reason']; ?>"><?php echo $row['reason']; ?></option>
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
                                                    </select>
                                                </center>
                                            </td>

                                            <td class="align-middle"><center><input type="checkbox" name="uniform[<?php echo $row['id']; ?>]" value="1" <?php echo ($row['uniform'] == 1) ? 'checked' : ''; ?> class="uniform-checkbox"></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="identification[<?php echo $row['id']; ?>]" value="1" <?php echo ($row['identification'] == 1) ? 'checked' : ''; ?> class="identification-checkbox"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="edit[<?php echo $row['id']; ?>]" disabled></center></td>
                                    <?php
                                        }
                                    ?>
                                        </tr>
                                </tbody>
                            </table>
                            <center><small class="text-danger"><i>*Refresh To Reset Changes*</i></small></center>
                        </div>
                        <input type="text" name="location" value="<?php echo $location; ?>" hidden>
                        <input type="text" name="date" value="<?php echo $date; ?>" hidden>
                        <input type="text" name="shift" value="<?php echo $shift; ?>" hidden>
                        <input type="text" name="shift_type" value="<?php echo $shift_type; ?>" hidden>
                        <hr>
                        <center>
                            <?php
                            $deadline_query = mysqli_query($conn, "SELECT * FROM tbl_deadline WHERE shift='$shift' AND shift_type='$shift_type' AND location='$location' AND submit_type='1'");

                            if ($fetch_deadline = mysqli_fetch_array($deadline_query)) {
                                $time_now = date("H:i:s");
                                $time_from = $fetch_deadline['time_from'];
                                $time_to = $fetch_deadline['time_to'];

                                if ($time_now >= $time_from && $time_now <= $time_to) {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-check"></i> Validate</a>';
                                } else {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#DeadlineModal"><i class="fa fa-sm fa-check"></i> Validate</a>';
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
                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check fa-sm"></i> Validate Form</h6>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><small>×</small></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Do you want to validate this form?</div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Validate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit modal end -->
                        </form>
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

        </div>\
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <script>
        $(document).on('change', '.attendance-checkbox', function() {
            var id = $(this).data('id');
            var selectRemarks = $("#remarks-" + id);

            if ($(this).is(':checked')) {
                selectRemarks.prop('disabled', true).val('');
                selectRemarks.prop('required', false);
            } else {
                selectRemarks.prop('disabled', false);
                selectRemarks.prop('required', true);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const attendanceCheckboxes = document.querySelectorAll('.attendance-checkbox');

            attendanceCheckboxes.forEach(function(attendanceCheckbox) {
                attendanceCheckbox.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const isChecked = this.checked;
                    
                    document.querySelector(`input[name="uniform[${id}]"]`).checked = isChecked;
                    document.querySelector(`input[name="identification[${id}]"]`).checked = isChecked;
                });
            });
        });

        $(document).ready(function() {
            // Function to enable and set the required attribute for the edit field
            function enableEditField(id) {
                var editField = $(`input[name="edit[${id}]"]`);
                editField.prop('disabled', false);
                editField.prop('required', true);
            }

            // Function to disable and remove the required attribute for the edit field
            function disableEditField(id) {
                var editField = $(`input[name="edit[${id}]"]`);
                editField.prop('disabled', true);
                editField.prop('required', false);
            }

            // Event listener for attendance checkboxes
            $(document).on('change', '.attendance-checkbox', function() {
                var id = $(this).data('id');
                var selectRemarks = $("#remarks-" + id);

                if ($(this).is(':checked')) {
                    selectRemarks.prop('disabled', true).val('');
                    selectRemarks.prop('required', false);
                } else {
                    selectRemarks.prop('disabled', false);
                    selectRemarks.prop('required', true);
                }

                // Enable edit field if attendance checkbox is changed
                enableEditField(id);
            });

            // Event listener for all other checkboxes
            $(document).on('change', '.uniform-checkbox, .identification-checkbox', function() {
                var id = $(this).attr('name').match(/\d+/)[0];

                // Enable edit field if any checkbox is changed
                enableEditField(id);
            });

            // Event listener for remarks select
            $(document).on('change', 'select[name^="remarks"]', function() {
                var id = $(this).attr('name').match(/\d+/)[0];

                // Enable edit field if remarks select is changed
                enableEditField(id);
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