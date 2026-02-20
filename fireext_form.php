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
$check_query = mysqli_query($conn,"SELECT * FROM tbl_fireext_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
$check_submit = mysqli_num_rows($check_query);

if(isset($_SESSION['id']) && $check_submit == 0 && in_array(65, $permission))
{
include_once("nav_admin.php");
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
                        <h6 class="m-0 font-weight-bold text-light">Fire Extinguisher Form</h6> 
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
                        <form method="POST" action="fireext_submit.php">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Asset ID</th>
                                        <th>Asset Name</th>
                                        <th>In Place</th>
                                        <th>Condition</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $location = mysqli_real_escape_string($conn, $location);
                                    $shift = mysqli_real_escape_string($conn, $shift);

                                    // Fetch asset inventory
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_name = 'Fire Extinguisher' AND location = '$location' AND cond=1");

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td class="align-middle">' . $row['asset_id'] . '</td>';
                                        echo '<td class="align-middle">' . $row['asset_name'] . '</td>';
                                        ?>
                                        <input type="text" name="asset_id[<?php echo $row['id']; ?>]" value="<?php echo $row['asset_id']; ?>" hidden>
                                        <td class="align-middle"><center><input type="checkbox" name="in_place[<?php echo $row['id']; ?>]" value="1" onchange="toggleSelect(this, '<?php echo $row['id']; ?>')"></center></td>
                                        <td class="align-middle">
                                            <center>
                                                <select class="form-control form-control-sm" name="condition[<?php echo $row['id']; ?>]" 
                                                        id="condition_<?php echo $row['id']; ?>" required>
                                                    <option value=""></option>
                                                    <option value="Expired">Expired</option>
                                                    <option value="Broken">Broken</option>
                                                </select>
                                            </center>
                                        </td>
                                        <td class="align-middle"><center><input type="text" class="form-control form-control-sm" name="remarks[<?php echo $row['id']; ?>]"></center></td>
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
                            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark add-btn" data-toggle="modal" <?php if(in_array(61, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Asset</button>
                            <a class="d-sm-inline-block btn btn-info btn-sm" data-toggle="modal" data-target="#MapModal"><i class="fa fa-sm fa-map"></i> Map</a>
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
                        </center>
                        </form>
                    </div>
                </div>
                <!-- End Table -->

            <!-- Map Modal -->
            <div class="modal fade" id="MapModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-map fa-sm"></i> Area Map</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table-responsive table-sm">
                                <tr>
                                    <td><small><u><b>Area Elements:</u></small></b></u></small></td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-danger">10LBS ABC DRY CHEM</span></td>
                                    <td><span class="badge badge-warning">50KG ABC DRY CHEM TROLLEY TYPE</span></td>
                                    <td><span class="badge badge-primary">50KG AFFF LIQUID FOAM TROLLEY TYPE </span></td>
                                </tr>
                            </table>
                            <hr>
                            <img class="img-fluid" src="img/map_fire.jpg">
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

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
                        <form method="POST" action="fireext_add.php" id="AssetForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6">
                                            <label>Asset ID</label>
                                            <input type="text" class="form-control form-control-sm" name="asset_id" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Asset Name</label>
                                            <input type="text" class="form-control form-control-sm" name="asset_name" autocomplete="off" required>
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
                                            $query = "SELECT * FROM tbl_locations WHERE is_active = '1' ORDER BY location_name ASC";
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
        function toggleSelect(checkbox, id) {
            const selectElement = document.getElementById('condition_' + id);
            selectElement.disabled = checkbox.checked;
        }

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