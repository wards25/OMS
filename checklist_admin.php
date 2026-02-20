<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$date = date("Y-m-d");
$day = date("l");

if(isset($_SESSION['id']))
{
include_once("nav_admin.php");

if(!isset($_GET['location'])){
    $loc_query = mysqli_query($conn,"SELECT * FROM tbl_user_locations WHERE user_id = ".$_SESSION['id']);
    $fetch_loc = mysqli_fetch_array($loc_query);
    $tbl_loc = $fetch_loc['location_id'];
    $locname_query = mysqli_query($conn,"SELECT * FROM tbl_locations WHERE id = '$tbl_loc'");
    $fetch_locname = mysqli_fetch_array($locname_query);
    $location = $fetch_locname['location_name'];
}else{
    $location=$_GET['location'];
}            
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <form method="GET" action="">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Checklist</h4><!-- 
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-user-btn" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Add User</button> -->
            <div class="input-group shadow-sm" style="width:45%;">
                <select id="locationFilter" name="location" class="form-control form-control-sm" onchange="this.form.submit()">
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
        </div>
        </form>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been submitted successfully.';
                    break;
                case 'validate':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been validated successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Checklist already submitted.';
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

        <!-- Earnings (Monthly) Card Example -->
        <div class="form-row">
            <div class="col-12">
                <div class="card shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tbody>
                                        <tr>
                                            <td class="table-primary"><b>Location</td>
                                            <td><?php echo $location; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="table-primary"><b>Date</td>
                                            <td><?php echo date("F d, Y"); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="text-center bg-primary text-light">
                                        <tr>
                                            <th width="55%">Module</th>
                                            <th width="10%">Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                                <table class="table table-bordered table-sm">
                                    <thead class="text-center bg-warning text-dark">
                                        <tr>
                                            <th colspan="3" class="table-info text-dark">Zone Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:60%;">Warehouse Locks (OPENING)</td>
                                            <?php
                                            $whselock_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_whselock = mysqli_fetch_array($whselock_query);
                                            $count_whselock = mysqli_num_rows($whselock_query);
                                            
                                            if(!empty($fetch_whselock['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(27, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="warehouselock_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_whselock >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(26, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="warehouselock_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(25, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="warehouselock_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        if($day == 'Thursday'){
                                        ?>
                                        <tr>
                                            <td>General Facilities</td>
                                            <?php
                                            $facility_query = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_facility = mysqli_fetch_array($facility_query);
                                            $count_facility = mysqli_num_rows($facility_query);

                                            if(!empty($fetch_facility['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(59, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="facility_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_facility >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(58, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="facility_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(57, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="facility_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        }else{

                                        }  
                                        if($day == 'Thursday'){
                                        ?> 
                                        <tr>
                                            <td>Fire Extinguisher</td>
                                            <?php
                                            $fire_query = mysqli_query($conn,"SELECT * FROM tbl_fireext_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_fire = mysqli_fetch_array($fire_query);
                                            $count_fire = mysqli_num_rows($fire_query);

                                            if(!empty($fetch_fire['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(67, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="fireext_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_fire >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(66, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="fireext_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(65, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="fireext_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        }else{

                                        }  
                                        if($day == 'Thursday'){
                                        ?>
                                        <tr>
                                            <td>Mouse Trap</td>
                                            <?php
                                            $mouse_query = mysqli_query($conn,"SELECT * FROM tbl_mousetrap_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_mouse = mysqli_fetch_array($mouse_query);
                                            $count_mouse = mysqli_num_rows($mouse_query);

                                            if(!empty($fetch_mouse['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(75, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="mousetrap_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_mouse >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(74, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="mousetrap_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(73, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="mousetrap_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                        }else{

                                        }  
                                        ?>
                                        <tr>
                                            <td>Warehouse Locks (CLOSING)</td>
                                            <?php
                                            $whselock_query = mysqli_query($conn,"SELECT * FROM tbl_warehouselock_raw WHERE date = '$date' AND shift = '2' AND shift_type = '2' AND location = '$location'");
                                            $fetch_whselock = mysqli_fetch_array($whselock_query);
                                            $count_whselock = mysqli_num_rows($whselock_query);

                                            if(!empty($fetch_whselock['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(27, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="warehouselock_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_whselock >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(26, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="warehouselock_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(25, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="warehouselock_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                            </div>
                        </div>
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