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
$check_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
$check_submit = mysqli_num_rows($check_query);

if(isset($_SESSION['id']) && $check_submit == 0 && in_array(33, $permission))
{
include_once("nav_whse.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Encode Checklist</h4>
            <a type="button" href="checklist.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm add-user-btn"><i class="fa fa-arrow-left"></i> Back</a>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Zone has been added successfully.';
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
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Zone name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Zone Audit Form</h6> 
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
                        <form method="POST" action="zone_submit.php" enctype="multipart/form-data">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Zone</th>
                                        <th>Assigned to</th>
                                        <th colspan="2">Segregated Items <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c1Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">FEFO <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c2Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Cleanliness <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c3Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Racks <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c4Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Space <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c5Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_zone_location WHERE location = '$location' AND is_active = '1'");
                                        while($row = mysqli_fetch_assoc($result)) {

                                            if($row['report'] >= 1){
                                            echo '<tr class="table-danger">';
                                            }else{
                                                echo '<tr>';
                                            }
                                            
                                            echo '<td class="align-middle">'.$row['location_name'].'<a href="#" class="text-warning float-right" data-toggle="modal" data-target="#zone'.$row['id'].'"><i class="fa fa-info-circle fa-sm"></i></a></td>'
                                        ?>
                                            <input type="text" name="location_name[<?php echo $row['id']; ?>]" value="<?php echo $row['location_name']; ?>" hidden>
                                            <td class="align-middle"><center>
                                            <?php
                                            $query = "SELECT * FROM tbl_employees WHERE location = '$location' AND shift = '$shift' AND department = 'Logistics-WH' ORDER BY employee_name ASC";
                                            $result2 = $conn->query($query);
                                            if ($result2->num_rows > 0) {
                                                $options = mysqli_fetch_all($result2, MYSQLI_ASSOC);
                                                $selected_employee = ''; // Default value
                                                // Fetch previously entered data for auto-fill
                                                $last_input_query = "SELECT * FROM tbl_zone_raw WHERE location_name = '" . $row['location_name'] . "' AND location = '$location' AND shift = '$shift' ORDER BY id DESC LIMIT 1";
                                                $last_input_result = mysqli_query($conn, $last_input_query);
                                                if ($last_input = mysqli_fetch_assoc($last_input_result)) {
                                                    $selected_employee = $last_input['employee_name'];
                                                }
                                                ?>
                                                <select class="form-control form-control-sm" name="employee_name[<?php echo $row['id']; ?>]" id="employee_select_<?php echo $row['id']; ?>">
                                                    <option value="Not Assigned"> </option>
                                                    <?php
                                                    foreach ($options as $option) {
                                                        $selected = ($option['employee_name'] == $selected_employee) ? 'selected' : '';
                                                        ?>
                                                        <option value="<?php echo $option['employee_name']; ?>" <?php echo $selected; ?>><?php echo $option['employee_name']; ?></option>
                                                        <?php
                                                    }
                                                }
                                                echo '</select>';
                                                ?>
                                            <td class="align-middle"><center><input type="checkbox" name="c1[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'c1_remarks[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="c1_remarks[<?php echo $row['id']; ?>]" required></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="c2[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'c2_remarks[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="c2_remarks[<?php echo $row['id']; ?>]" required></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="c3[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'c3_remarks[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="c3_remarks[<?php echo $row['id']; ?>]" required></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="c4[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'c4_remarks[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="c4_remarks[<?php echo $row['id']; ?>]" required></center></td>
                                            <td class="align-middle"><center><input type="checkbox" name="c5[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'c5_remarks[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="c5_remarks[<?php echo $row['id']; ?>]" required></center></td>
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
                            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark add-btn" data-toggle="modal" <?php if(in_array(49, $permission)){ echo 'data-target="#addWhseLock"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Zone</button>
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
                        </center>
                    </div>
                </div>
                <!-- End Table -->

                <!-- Add Whse Lock Modal-->
                <div class="modal fade" id="addWhseLock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-map-marker fa-sm"></i> &nbsp;Add Zone Location</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <form method="POST" action="zone_add.php" id="WarehouseLockForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Zone Name:</label>
                                            <input type="text" class="form-control form-control-sm" name="zonename" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Description:</label>
                                            <input type="text" class="form-control form-control-sm" name="description" required>
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
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" type="submit" name="submit">Add Location</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                $desc_query = mysqli_query($conn,"SELECT * FROM tbl_zone_location");
                while($fetch_desc = mysqli_fetch_array($desc_query)){
                ?>
                <!-- c1 Modal-->
                <div class="modal fade" id="zone<?php echo $fetch_desc['id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-info fa-sm"></i> <?php echo $fetch_desc['location_name']; ?></h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php echo $fetch_desc['description']; ?>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>

                <!-- c1 Modal-->
                <div class="modal fade" id="c1Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Segregated Items</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Non Conformance Items (Products with Leaks and Damage)<br>
                                2. Break Case
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c2 Modal-->
                <div class="modal fade" id="c2Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> FEFO</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                First Expiration First Out
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c3 Modal-->
                <div class="modal fade" id="c3Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Cleanliness</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. No trash in the area<br>
                                2. Waste bin empty<br>
                                3. Pallet and all equipments stored away<br>
                                4. Items are free from dust<br>
                                5. No leaks on floor & on products
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c4 Modal-->
                <div class="modal fade" id="c4Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Racks</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Intact and Safe to hold stocks<br>
                                2. Free from weather or mechanical damage
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c5 Modal-->
                <div class="modal fade" id="c5Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Space</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Adequate space to maneuver<br>
                                2. Free from weather or mechanical damage<br>
                                3. Lighting sufficient<br>
                                4. Leaks or cracks on the facilities
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
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
        function toggleRemarks(checkbox, remarkName) {
            const remarkInput = document.querySelector(`input[name="${remarkName}"]`);
            if (checkbox.checked) {
                remarkInput.disabled = true;
                remarkInput.value = ""; // Optionally clear the input
            } else {
                remarkInput.disabled = false;
            }
        }

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