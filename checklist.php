<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$date = date("Y-m-d");
$day = date("l");

if(isset($_SESSION['id']))
{
include_once("nav_whse.php");

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
            <h4 class="mb-0 text-gray-800">Daily Checklist</h4><!-- 
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
            <div class="col-xl-6 col-md-6 mb-4">
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
                                        <tr>
                                            <td class="table-primary"><b>Shift</td>
                                            <td>1st Shift</td>
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
                                <table class="table table-bordered table-sm text-sm">
                                    <thead class="text-center bg-primary text-light">
                                        <tr>
                                            <th colspan="3" class="table-info text-dark">Opening Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:60%">WH Attendance</td>
                                            <?php
                                            $attendance_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_attendance = mysqli_fetch_array($attendance_query);
                                            $count_attendance = mysqli_num_rows($attendance_query);

                                            if(!empty($fetch_attendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(31, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="attendance_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_attendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(30, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="attendance_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(29, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="attendance_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Office Attendance</td>
                                            <?php
                                            $ofattendance_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_ofattendance = mysqli_fetch_array($ofattendance_query);
                                            $count_ofattendance = mysqli_num_rows($ofattendance_query);

                                            if(!empty($fetch_ofattendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(39, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="ofattendance_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_ofattendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(38, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="ofattendance_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(37, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="ofattendance_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Asset Audit</td>
                                            <?php
                                            $asset_query = mysqli_query($conn,"SELECT * FROM tbl_asset_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_asset = mysqli_fetch_array($asset_query);
                                            $count_asset = mysqli_num_rows($asset_query);

                                            if(!empty($fetch_asset['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(16, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="asset_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_asset >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(24, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="asset_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(23, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="asset_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Temp Monitoring</td>
                                            <?php
                                            $temp_query = mysqli_query($conn,"SELECT * FROM tbl_temp_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_temp = mysqli_fetch_array($temp_query);
                                            $count_temp = mysqli_num_rows($temp_query);

                                            if(!empty($fetch_temp['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(43, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="temp_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_temp >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(42, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="temp_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(41, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="temp_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Zone Audit</td>
                                            <?php
                                            $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date = '$date' AND shift = '1' AND shift_type = '1' AND location = '$location'");
                                            $fetch_zone = mysqli_fetch_array($zone_query);
                                            $count_zone = mysqli_num_rows($zone_query);

                                            if(!empty($fetch_zone['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(35, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="zone_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_zone >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(34, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="zone_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(33, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="zone_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="text-center bg-primary text-light">
                                        <tr>
                                            <th colspan="3" class="table-info text-dark">End of Shift Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:60%">WH Attendance</td>
                                            <?php
                                            $attendance_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date = '$date' AND shift = '1' AND shift_type = '2' AND location = '$location'");
                                            $fetch_attendance = mysqli_fetch_array($attendance_query);
                                            $count_attendance = mysqli_num_rows($attendance_query);

                                            if(!empty($fetch_attendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(31, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="attendance_view.php?location='.$location.'&date='.$date.'&shift=1&shift_type=2"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_attendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(30, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="attendance_validate.php?location='.$location.'&date='.$date.'&shift=1&shift_type=2"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(29, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="attendance_form.php?location='.$location.'&date='.$date.'&shift=1&shift_type=2"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2nd Shift -->
            <div class="col-xl-6 col-md-6 mb-4">
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
                                        <tr>
                                            <td class="table-primary"><b>Shift</td>
                                            <td>2nd Shift</td>
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
                                    <thead class="text-center bg-primary text-light">
                                        <tr>
                                            <th colspan="3" class="table-info text-dark">Opening Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:60%">WH Attendance</td>
                                            <?php
                                            $attendance_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date = '$date' AND shift = '2' AND shift_type = '1' AND location = '$location'");
                                            $fetch_attendance = mysqli_fetch_array($attendance_query);
                                            $count_attendance = mysqli_num_rows($attendance_query);

                                            if(!empty($fetch_attendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(31, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="attendance_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_attendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(30, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="attendance_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(29, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="attendance_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Office Attendance</td>
                                            <?php
                                            $ofattendance_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date = '$date' AND shift = '2' AND shift_type = '1' AND location = '$location'");
                                            $fetch_ofattendance = mysqli_fetch_array($ofattendance_query);
                                            $count_ofattendance = mysqli_num_rows($ofattendance_query);

                                            if(!empty($fetch_ofattendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(39, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="ofattendance_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_ofattendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(38, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="ofattendance_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(37, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="ofattendance_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Asset Audit</td>
                                            <?php
                                            $asset_query = mysqli_query($conn,"SELECT * FROM tbl_asset_raw WHERE date = '$date' AND shift = '2' AND shift_type = '1' AND location = '$location'");
                                            $fetch_asset = mysqli_fetch_array($asset_query);
                                            $count_asset = mysqli_num_rows($asset_query);

                                            if(!empty($fetch_asset['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(16, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="asset_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_asset >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(24, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="asset_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(23, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="asset_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Temp Monitoring</td>
                                            <?php
                                            $temp_query = mysqli_query($conn,"SELECT * FROM tbl_temp_raw WHERE date = '$date' AND shift = '2' AND shift_type = '1' AND location = '$location'");
                                            $fetch_temp = mysqli_fetch_array($temp_query);
                                            $count_temp = mysqli_num_rows($temp_query);

                                            if(!empty($fetch_temp['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(43, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="temp_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_temp >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(42, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="temp_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(41, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="temp_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                        <tr>
                                            <td>Zone Audit</td>
                                            <?php
                                            $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date = '$date' AND shift = '2' AND shift_type = '1' AND location = '$location'");
                                            $fetch_zone = mysqli_fetch_array($zone_query);
                                            $count_zone = mysqli_num_rows($zone_query);

                                            if(!empty($fetch_zone['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(35, $permission)){ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" href="zone_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_zone >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(34, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="zone_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(33, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="zone_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=1"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="text-center bg-primary text-light">
                                        <tr>
                                            <th colspan="3" class="table-info text-dark">End of Shift Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="width:60%">WH Attendance</td>
                                            <?php
                                            $attendance_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date = '$date' AND shift = '2' AND shift_type = '2' AND location = '$location'");
                                            $fetch_attendance = mysqli_fetch_array($attendance_query);
                                            $count_attendance = mysqli_num_rows($attendance_query);

                                            if(!empty($fetch_attendance['is_validated'])){
                                                echo '<td class="table-success"></td>';
                                                if(in_array(31, $permission)){ 
                                                    echo '<td style="width:10%"><center><a type="button" class="btn btn-sm btn-outline-info" href="attendance_view.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }else{ 
                                                    echo '<td style="width:10%"><center><a type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-eye"></i> Review</a></center></td>';
                                                }
                                            }else{
                                                if($count_attendance >= 1){
                                                    echo '<td class="table-warning"></td>';
                                                    if(in_array(30, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" href="attendance_validate.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-info"></i> Validate</a></center></td>';
                                                    }
                                                }else{
                                                    echo '<td class="table-danger"></td>';
                                                    if(in_array(29, $permission)){ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" href="attendance_form.php?location='.$location.'&date='.$date.'&shift=2&shift_type=2"><i class="fa fa-sm fa-list"></i> Encode</a></center></td>';
                                                    }else{ 
                                                        echo '<td><center><a type="button" class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-sm fa-list"></i>Encode</a></center></td>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </tbody>
                                </table>
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