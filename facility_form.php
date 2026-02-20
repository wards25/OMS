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
$check_query = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
$check_submit = mysqli_num_rows($check_query);

if(isset($_SESSION['id']) && $check_submit == 0 && in_array(57, $permission))
{
include_once("nav_admin.php");
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Facility has been added successfully.';
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
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Facility name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">General Facilities Form</h6> 
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
                        <form method="POST" action="facility_submit.php" enctype="multipart/form-data">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Facility</th>
                                        <th>Last Findings</th>
                                        <th>Condition</th>
                                        <th>Findings</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_facility_location WHERE location = '$location' AND is_active = '1'");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td class="align-middle">'.$row['location_name'].'<a href="#" class="text-warning float-right" data-toggle="modal" data-target="#zone'.$row['id'].'"><i class="fa fa-info-circle fa-sm"></i></a></td>';
                                        ?>
                                            <input type="text" name="location_name[<?php echo $row['id']; ?>]" value="<?php echo $row['location_name']; ?>" hidden>
                                            <?php
                                            // Fetch previously entered data for auto-fill
                                            $last_input_query = "SELECT * FROM tbl_facility_raw WHERE location_name = '" . $row['location_name'] . "' AND location = '$location' AND shift = '$shift' ORDER BY id DESC LIMIT 1";
                                            $last_input_result = mysqli_query($conn, $last_input_query);
                                            if($last_input = mysqli_fetch_assoc($last_input_result)){
                                                echo '<td class="align-middle"><center><input type="text" class="form-control form-control-sm" value="'.$last_input['findings'].'" disabled></center></td>';
                                            }else{
                                                echo '<td class="align-middle"><center><input type="text" class="form-control form-control-sm" value="" disabled></center></td>';
                                            }
                                            ?>
                                            <td class="align-middle"><center><input type="checkbox" name="condition[<?php echo $row['id']; ?>]" value="1" onchange="toggleRemarks(this, 'findings[<?php echo $row['id']; ?>]', 'image[<?php echo $row['id']; ?>]')"></center></td>
                                            <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="findings[<?php echo $row['id']; ?>]" required></center></td>
                                            <td class="align-middle"><center><input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="image[<?php echo $row['id']; ?>]" required/></center></td>
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
                            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark add-btn" data-toggle="modal" <?php if(in_array(53, $permission)){ echo 'data-target="#addWhseLock"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Facility</button>
                            <?php
                                $time_now = date("H:i:s");

                                if ($time_now >= '06:00:00' && $time_now <= '18:00:00') {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-check"></i> Submit</a>';
                                } else {
                                    echo '<a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#DeadlineModal"><i class="fa fa-sm fa-check"></i> Submit</a>';
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

                <!-- Add Zone Modal-->
                <div class="modal fade" id="addWhseLock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-map-marker fa-sm"></i> &nbsp;Add Facility Location</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <form method="POST" action="facility_add.php" id="WarehouseLockForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <label>Facility Name:</label>
                                        <input type="text" class="form-control form-control-sm" name="facilityname" required>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
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
                                <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" type="submit" name="submit">Add Location</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                $desc_query = mysqli_query($conn,"SELECT * FROM tbl_facility_location");
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
                                <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                    <tbody>
                                        <?php
                                        $location_id = $fetch_desc['id'];
                                        $number = 0;
                                        $info_query = mysqli_query($conn,"SELECT * FROM tbl_facility_description WHERE location_id = '$location_id'");
                                        while($fetch_info = mysqli_fetch_array($info_query)){
                                            $number ++;
                                            echo '<tr>';
                                            echo '<td>'.$number.'. '.$fetch_info['description'].'</td>';
                                        }
                                        ?>
                                        </tr>
                                    </tbody>
                                </table>
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
        function toggleRemarks(checkbox, findingsName, imageName) {
            // Get the findings and image input elements
            const findingsInput = document.getElementsByName(findingsName)[0];
            const imageInput = document.getElementsByName(imageName)[0];

            // Disable or enable inputs based on checkbox state
            if (checkbox.checked) {
                findingsInput.disabled = true;
                imageInput.disabled = true;
            } else {
                findingsInput.disabled = false;
                imageInput.disabled = false;
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