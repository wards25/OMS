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
$check_query = mysqli_query($conn,"SELECT * FROM tbl_asset_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
$check_submit = mysqli_num_rows($check_query);

if(isset($_SESSION['id']) && $check_submit == 0 && in_array(16, $permission))
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Asset has been added successfully.';
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
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Asset exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Asset Audit Form</h6> 
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
                        <form method="POST" action="asset_submit.php">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Asset ID</th>
                                        <th>Asset Name</th>
                                        <th>Own/Rented</th>
                                        <th>Assigned To</th>
                                        <th colspan="2">Incident Report</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $location = mysqli_real_escape_string($conn, $location);
                                    $shift = mysqli_real_escape_string($conn, $shift);

                                    // Fetch asset inventory
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_type = 'Warehouse' AND location = '$location' AND cond=1");

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        if ($row['report'] >= 1) {
                                            echo '<tr class="table-danger">';
                                        } else {
                                            echo '<tr>';
                                        }
                                        echo '<td class="align-middle">' . $row['asset_id'] . '</td>';
                                        echo '<td class="align-middle">' . $row['asset_name'] . '</td>';
                                        echo '<td class="align-middle"><center>' . ($row['asset_rented'] == 0 ? 'OWNED' : 'RENTED') . '</center></td>';
                                        ?>
                                        <input type="text" name="asset_id[<?php echo $row['id']; ?>]" value="<?php echo $row['asset_id']; ?>" hidden>
                                        <td class="align-middle"><center>
                                            <?php
                                            $query = "SELECT * FROM tbl_employees WHERE location = '$location' AND shift = '$shift' AND department = 'Logistics-WH' ORDER BY employee_name ASC";
                                            $result2 = $conn->query($query);
                                            if ($result2->num_rows > 0) {
                                                $options = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                                                $selected_employee = ''; // Default value
                                                // Fetch previously entered data for auto-fill
                                                $last_input_query = "SELECT * FROM tbl_asset_raw WHERE asset_id = '" . $row['asset_id'] . "' AND location = '$location' AND shift = '$shift' ORDER BY id DESC LIMIT 1";
                                                $last_input_result = mysqli_query($conn, $last_input_query);
                                                if ($last_input = mysqli_fetch_assoc($last_input_result)) {
                                                    $selected_employee = $last_input['assigned_to'];
                                                }
                                                ?>
                                                <select class="form-control form-control-sm" name="employee_name[<?php echo $row['id']; ?>]" id="employee_select_<?php echo $row['id']; ?>">
                                                    <option value="Not In Use"> </option>
                                                    <?php
                                                    foreach ($options as $option) {
                                                        $selected = ($option['employee_name'] == $selected_employee) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $option['employee_name']; ?>" <?php echo $selected; ?>><?php echo $option['employee_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                echo '</select>';

                                                // Check for incidents
                                                $subject = $row['asset_id'];

                                                $incident_query = mysqli_query($conn, "SELECT * FROM tbl_report_raw WHERE location='$location' AND subject='$subject' AND status=0");
                                                $check_incident = mysqli_num_rows($incident_query);

                                                if ($check_incident >= 1) {
                                                    echo '<td>';
                                                    while ($fetch_incident = mysqli_fetch_array($incident_query)) {
                                                        ?>
                                                        <center><a type="button" class="text-dark" <?php if (in_array(19, $permission)) { echo 'href="incident_view.php?ir=' . $fetch_incident['ref_no'] . '"'; } else { echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><small><?php echo $fetch_incident['ref_no']; ?></small></a></center>
                                                        <?php
                                                    }
                                                    echo '</td>';
                                                } else {
                                                    echo '<td class="align-middle"><center><small class="text-dark"><i>No Incident Report Filed</i></small></center></td>';
                                                }
                                                ?>
                                                <td class="align-middle"><center><a type="button" class="btn btn-sm btn-danger" <?php if (in_array(17, $permission)) { echo 'href="incident_form.php?location=' . $location . '&date=' . $date . '&shift=' . $shift . '&shift_type=' . $shift_type . '&table=tbl_asset_inv&tbl_column=asset_id&subject=' . $row['asset_id'] . '&tag=LOGISTICS&module_id=' . $row['id'] . '"'; } else { echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-exclamation-triangle fa-sm"></i> File IR</a></center></td>
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
                        <hr>
                        <center>
                            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark add-btn" data-toggle="modal" <?php if(in_array(6, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Asset</button>
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
                        </center>
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
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add Asset</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="asset_add.php" id="AssetForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Asset ID</label>
                                            <input type="text" class="form-control form-control-sm" name="asset_id" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Asset Name</label>
                                            <?php
                                            $query = "SELECT * FROM tbl_asset_inv GROUP BY asset_name ORDER BY asset_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="asset_name" required>
                                                <?php    
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['asset_name'];?>"><?php echo $option['asset_name']; ?></option>
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
                                            <label>Own/Rented:</label>
                                            <select class="form-control form-control-sm" name="rented">
                                                <option value="0">OWN</option>
                                                <option value="1">RENTED</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Condition:</label>
                                            <select class="form-control form-control-sm" name="cond">
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Asset Type:</label>
                                            <select class="form-control form-control-sm" name="type">
                                                <option value="Admin">Admin</option>
                                                <option value="Warehouse">Warehouse</option>
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
                                <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add Asset</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Function to remove selected option from all selects
            function updateDropdowns() {
                // Get all select elements
                var selects = document.querySelectorAll('select[name^="employee_name"]');
                
                // Get all selected values
                var selectedValues = [];
                selects.forEach(function(select) {
                    if (select.value && select.value !== 'Not In Use') {
                        selectedValues.push(select.value);
                    }
                });

                // Update options for each select
                selects.forEach(function(select) {
                    var options = select.querySelectorAll('option');
                    options.forEach(function(option) {
                        if (option.value !== 'Not In Use' && selectedValues.includes(option.value) && option.value !== select.value) {
                            option.style.display = 'none'; // Hide the selected option
                        } else {
                            option.style.display = ''; // Show the option
                        }
                    });
                });
            }

            // Add change event listener to all selects
            var selects = document.querySelectorAll('select[name^="employee_name"]');
            selects.forEach(function(select) {
                select.addEventListener('change', updateDropdowns);
            });

            // Initial update
            updateDropdowns();
        });

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

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#AssetForm')[0].reset();
            $('#IrForm')[0].reset();
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