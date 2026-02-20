<?php
//error_reporting(0);
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(!isset($_SESSION['id']))
{
  header("Location: login.php");
}
include_once("nav_product.php");

// Initialize variables
$location = $shift = $week_selected = null;

// Handle location
if (!isset($_GET['location'])) {
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT location_id FROM tbl_user_locations WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch_loc = $result->fetch_assoc();
    $tbl_loc = $fetch_loc['location_id'];

    // Get location name
    $stmt = $conn->prepare("SELECT location_name FROM tbl_locations WHERE id = ?");
    $stmt->bind_param("i", $tbl_loc);
    $stmt->execute();
    $result = $stmt->get_result();
    $fetch_locname = $result->fetch_assoc();

    $location = htmlspecialchars($fetch_locname['location_name']);
} else {
    $location = htmlspecialchars($_GET['location']);
}

// Handle shift
$shift = isset($_GET['shift']) ? (int)$_GET['shift'] : 1;

// Handle week selection
$week_selected = isset($_GET['weeks']) ? htmlspecialchars($_GET['weeks']) : null;
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <form method="GET" action="">
        <h1 class="h3 mb-0 text-gray-800 d-flex justify-content-between align-items-center">
        <span class="text-success"><b><?php if($location == 'CDO'){ echo strtoupper($location); } else { echo ucwords(strtolower($location)); } ?> Dashboard</b></span>
            <?php
            // Get the current year and month
            $currentYear = date('Y');
            $currentMonth = date('m');

            // Get the first day of the month
            $firstDayOfMonth = new DateTime("$currentYear-$currentMonth-01");

            // Get the last day of the month
            $lastDayOfMonth = new DateTime($firstDayOfMonth->format('Y-m-t'));

            // Initialize an array to hold Mondays
            $mondayDates = [];

            // Loop through the month to find Mondays
            for ($date = clone $firstDayOfMonth; $date <= $lastDayOfMonth; $date->modify('+1 day')) {
                if ($date->format('N') == 1) { // 1 = Monday
                    $mondayDates[] = $date->format('Y-m-d');
                }
            }

            // Group the Mondays into weeks
            $weeks = [];
            foreach ($mondayDates as $monday) {
                $startOfWeek = new DateTime($monday);
                $startOfWeek->modify('monday this week');
                $endOfWeek = clone $startOfWeek;
                $endOfWeek->modify('+6 days');
                
                // Ensure the end date is within the current month
                if ($endOfWeek > $lastDayOfMonth) {
                    $endOfWeek = clone $lastDayOfMonth;
                }
                
                $weeks[] = [
                    'start' => $startOfWeek->format('Y-m-d'),
                    'end' => $endOfWeek->format('Y-m-d'),
                    'monday' => $monday
                ];
            }

            // Generate the dropdown for weeks
            echo '<select id="weeks" name="weeks" class="form-control form-control-sm shadow-sm" onchange="this.form.submit()" style="width:25%;">';
            $weekno = 0; // Initialize week number
            foreach ($weeks as $week) {
                $weekno++;
                $selected = ($week['monday'] == $week_selected) ? 'selected' : '';
                echo '<option value="' . $week['start'] . '" ' . $selected . '>Week ' . $weekno . '</option>';
            };
            echo '</select>'
            ?>
        </h1>
        <small>Here’s what’s going on at Warehouse right now</small>
        <hr>

        <!-- Content Row -->
        <div class="row">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-primary">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Number of Employees</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $employee_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location='$location' AND is_active = 1");
                                    $count_employee = mysqli_num_rows($employee_query);
                                    echo $count_employee;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-warning">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Active Assets</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $asset_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE location='$location' AND cond = 1");
                                    $count_asset = mysqli_num_rows($asset_query);
                                    echo $count_asset;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-dolly text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-success">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Warehouse Zones</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_location WHERE location='$location' AND is_active = 1");
                                    $count_zone = mysqli_num_rows($zone_query);
                                    echo $count_zone;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-warehouse fa-2x text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-danger">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Unresolved Incidents</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $ir_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE location='$location' AND status = 0");
                                    $count_ir = mysqli_num_rows($ir_query);
                                    echo $count_ir;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></a></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-stamp fa-2x text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="form-group">
            <div class="row">
                <div class="col-6">
                    <select id="locationFilter" name="location" class="form-control form-control-sm shadow-sm" onchange="this.form.submit()">
                        <?php 
                        $stmt = $conn->prepare("SELECT ul.location_id, l.location_name FROM tbl_user_locations ul JOIN tbl_locations l ON ul.location_id = l.id WHERE ul.user_id = ?");
                        $stmt->bind_param("i", $_SESSION['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($fetch_loc = $result->fetch_assoc()) {
                            $selected = ($location == $fetch_loc['location_name']) ? 'selected' : '';
                            echo "<option value=\"{$fetch_loc['location_name']}\" $selected>{$fetch_loc['location_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-6">
                    <select id="shiftFilter" name="shift" class="form-control form-control-sm shadow-sm" onchange="this.form.submit()">
                        <option value="1" <?php if ($shift == 1) echo 'selected'; ?>>1st Shift</option>
                        <option value="2" <?php if ($shift == 2) echo 'selected'; ?>>2nd Shift</option>
                        <!-- <option value="3" <?php if ($shift == 3) echo 'selected'; ?>>3rd Shift</option> -->
                    </select>
                </div>
            </div>
        </div>
    </form>

<?php
date_default_timezone_set("Asia/Manila");
$now = date("Y-m-d");

// Function to get the status cell color
function getStatusCellColor($count, $is_validated, $check_date) {
    $today = date("Y-m-d");
    
    // Check if the date has lapsed and there is no data for that day
    if ($check_date <= $today && $count === 0) {
        return 'table-danger';
    }
    
    if (!empty($is_validated)) {
        return 'table-success';
    } elseif ($count >= 1) {
        return 'table-warning';
    } else {
        return 'bg-gray-400';
    }
}

// Calculate the start of the week (Monday) for the current date
if(isset($_GET['weeks'])){
    $date = $_GET['weeks'];
}else{
    $date = date("Y-m-d");
}

$ts = strtotime($date);
$dow = date('w', $ts);
$offset = $dow - 1;
if ($offset < 0) {
    $offset = 6;
}
$ts -= $offset * 86400;
?>

<div class="row">
    <!-- Opening Checklist -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Opening Checklist:
                    <?php
                    if ($shift == 1) {
                    echo '1st Shift';
                    } elseif ($shift == 2) {
                        echo '2nd Shift';
                    } else {
                        echo '3rd Shift';
                    }
                    ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="text-center bg-primary text-light">
                                <tr>
                                    <th>WH Module</th>
                                    <?php
                                    // Loop through the past 7 days
                                    for ($i = 0; $i < 7; $i++, $ts += 86400) {
                                        echo '<th>' . date("D", $ts) . '</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Array of modules to loop through
                                $modules = [
                                    'Asset Assignment' => 'tbl_asset_raw',
                                    'Office Attendance' => 'tbl_ofattendance_raw',
                                    'Temp Monitoring' => 'tbl_temp_raw',
                                    'WH Attendance' => 'tbl_attendance_raw',
                                    'Zone Assignment' => 'tbl_zone_raw'
                                ];

                                // Recalculate start of the week
                                $ts = strtotime($date);
                                $dow = date('w', $ts);
                                $offset = $dow - 1;
                                if ($offset < 0) {
                                    $offset = 6;
                                }
                                $ts -= $offset * 86400;

                                // Loop through each module and generate rows
                                foreach ($modules as $module => $table) {
                                    echo '<tr>';
                                    echo '<td>' . $module . '</td>';

                                    for ($i = 0; $i < 7; $i++, $ts += 86400) {
                                        $check = date("Y-m-d", $ts);
                                        $query = mysqli_query($conn, "SELECT * FROM $table WHERE date='$check' AND location='$location' AND shift='$shift' AND shift_type='1'");
                                        $fetch = mysqli_fetch_array($query);
                                        $count = mysqli_num_rows($query);
                                        $color = getStatusCellColor($count, $fetch['is_validated'] ?? null, $check);
                                        echo '<td class="' . $color . '"></td>';
                                    }

                                    // Reset timestamp for next module
                                    $ts = strtotime($date);
                                    $dow = date('w', $ts);
                                    $offset = $dow - 1;
                                    if ($offset < 0) {
                                        $offset = 6;
                                    }
                                    $ts -= $offset * 86400;
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Closing Checklist -->
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card shadow h-100 ">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Closing Checklist:
                    <?php
                    if ($shift == 1) {
                    echo '1st Shift';
                    } elseif ($shift == 2) {
                        echo '2nd Shift';
                    } else {
                        echo '3rd Shift';
                    }
                    ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="text-center bg-primary text-light">
                                <tr>
                                    <th>WH Module</th>
                                    <?php
                                    // Recalculate start of the week
                                    $ts = strtotime($date);
                                    $dow = date('w', $ts);
                                    $offset = $dow - 1;
                                    if ($offset < 0) {
                                        $offset = 6;
                                    }
                                    $ts -= $offset * 86400;

                                    // Loop through the past 7 days
                                    for ($i = 0; $i < 7; $i++, $ts += 86400) {
                                        echo '<th>' . date("D", $ts) . '</th>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Array of modules to loop through
                                $modules = [
                                    'WH Attendance' => 'tbl_attendance_raw'
                                ];

                                // Recalculate start of the week
                                $ts = strtotime($date);
                                $dow = date('w', $ts);
                                $offset = $dow - 1;
                                if ($offset < 0) {
                                    $offset = 6;
                                }
                                $ts -= $offset * 86400;

                                // Loop through each module and generate rows
                                foreach ($modules as $module => $table) {
                                    echo '<tr>';
                                    echo '<td>' . $module . '</td>';

                                    for ($i = 0; $i < 7; $i++, $ts += 86400) {
                                        $check = date("Y-m-d", $ts);
                                        $query = mysqli_query($conn, "SELECT * FROM $table WHERE date='$check' AND location='$location' AND shift='$shift' AND shift_type='2'");
                                        $fetch = mysqli_fetch_array($query);
                                        $count = mysqli_num_rows($query);
                                        $color = getStatusCellColor($count, $fetch['is_validated'] ?? null, $check);
                                        echo '<td class="' . $color . '"></td>';
                                    }

                                    // Reset timestamp for next module
                                    $ts = strtotime($date);
                                    $dow = date('w', $ts);
                                    $offset = $dow - 1;
                                    if ($offset < 0) {
                                        $offset = 6;
                                    }
                                    $ts -= $offset * 86400;
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                    <table class="table-responsive table-sm">
                        <tr>
                            <td><small><u><b>Checklist Elements:</u></small></b></u></small></td>
                            <td><span class="badge badge-success">Validated</span></td>
                            <td><span class="badge badge-warning">Unvalidated Data</span></td>
                            <td><span class="badge badge-danger">No Data</span></td>
                        </tr>
                        <tr>
                            <span class="badge badge-primary float-right"><a href="checklist.php" class="text-light"><i class="fa fa-arrow-right fa-sm"></i> Go To Checklist</a></span>
                            <br><br>
                        </tr>
                    </table>
                <hr>
                <small><center><i>Always check your filtered data when checking</i></center></small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="card shadow h-100 w-100" style="background-color:#fceec9;">
            <div class="card-body w-100">
                <h4 class="text-dark"><b>1st Shift Metrics:</b></h4>
                <small style="color:#000000;"><?php echo date("F d, Y") . ' (' . date("l") . ')'; ?></small>
                <hr>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location' AND cond='1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample1" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total Asset Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Asset' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample1">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT asset_name,COUNT(*) as count FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location' AND cond='1' GROUP BY asset_name ORDER BY asset_name");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $asset_name = $row['asset_name'];

                                    $pos_query = mysqli_query($conn,"SELECT asset_name,COUNT(*) as submitcount FROM tbl_asset_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND asset_name='$asset_name'");
                                    $count_goodpos = mysqli_fetch_array($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $count_good = $count_goodpos['submitcount'];
                                    $averagepos = (($count_good / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $asset_name.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'Asset Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND attendance = '1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location='$location' AND shift='1' AND is_active='1' AND department='Logistics-Office'");
                        $count_total = mysqli_num_rows($total_query);
                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample2" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total Office Attendance Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample2">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT position,COUNT(*) as count FROM tbl_employees WHERE location='$location' AND shift='1' AND is_active='1' AND department='Logistics-Office' GROUP BY position ORDER BY position");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $position = $row['position'];

                                    $pos_query = mysqli_query($conn,"SELECT position,attendance FROM tbl_ofattendance_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND attendance='1' AND position='$position'");
                                    $count_goodpos = mysqli_num_rows($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $averagepos = (($count_goodpos / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $position.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND attendance = '1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location='$location' AND shift='1' AND is_active='1' AND department='Logistics-WH'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);;
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample3" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total WH Attendance Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'WH Attendance' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample3">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT position,COUNT(*) as count FROM tbl_employees WHERE location='$location' AND shift='1' AND is_active='1' AND department='Logistics-WH' GROUP BY position ORDER BY position");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $position = $row['position'];

                                    $pos_query = mysqli_query($conn,"SELECT position,attendance FROM tbl_attendance_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND attendance='1' AND position='$position'");
                                    $count_goodpos = mysqli_num_rows($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $averagepos = (($count_goodpos / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $position.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Attendance Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1'");
                        $count_good = mysqli_num_rows($good_query);
                        $count_total = $count_good * 5;
                        $score_query = mysqli_query($conn,"SELECT sum(c1+c2+c3+c4+c5) as totalscore FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1'");
                        $fetch_score = mysqli_fetch_array($score_query);
                        $count_score = $fetch_score['totalscore'];
                        if ($count_total != 0) {
                            $average = (($count_score / $count_total) * 100);
                        } else {
                        }
                        $average = number_format($average,2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample4" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total WH Zone Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'WH Zone' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample4">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_location WHERE location='$location' AND is_active='1' ORDER BY location_name");
                                while($fetch_zone = mysqli_fetch_array($zone_query)){
                                $zone = $fetch_zone['location_name'];
                                $pos_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND location_name = '$zone'");
                                $count_pos = mysqli_num_rows($pos_query);
                                if($count_pos <= 0){
                                    $average = 0.00;
                                }else{
                                    $fetch_pos = mysqli_fetch_array($pos_query);
                                    $count_goodpos = $fetch_pos['c1'] + $fetch_pos['c2'] + $fetch_pos['c3'] + $fetch_pos['c4'] + $fetch_pos['c5'];
                        
                                    $averagepos = (($count_goodpos / 5) * 100);
                                    $averagepos = number_format($averagepos,2);
                                }
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $zone.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Zone Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <?php
                if($location != 'CAINTA'){
                }else{
                ?>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_facility_location WHERE location='$location' AND is_active=1");
                        $count_good = mysqli_num_rows($good_query);
                        $score_query = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND cond=1");
                        $count_score = mysqli_num_rows($score_query);

                        if ($count_good > 0) {
                            $average = (($count_score / $count_good) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse">
                            <div class="d-flex text-dark bg-gray-100 justify-content-between align-items-center">
                                <span><b>General Facility Score (M)</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'General Facilities' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_fireext_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND in_place=1");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_name='Fire Extinguisher' AND location='$location' AND cond='1'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse">
                            <div class="d-flex text-dark bg-gray-100 justify-content-between align-items-center">
                                <span><b>Fire Extinguisher Score (W)</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Fire Extinguisher' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_mousetrap_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1' AND in_place=1");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_name='Mouse Trap' AND location='$location' AND cond='1'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse">
                            <div class="d-flex text-dark bg-gray-100 justify-content-between align-items-center">
                                <span><b>Mouse Trap Score (F)</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Mouse Trap' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <!-- 2nd Shift metrics -->
    <div class="col-6">
        <div class="card shadow h-100 w-100">
            <div class="card-body w-100" style="background-color:#fceec9;">
                <h4 class="text-dark"><b>2nd Shift Metrics:</b></h4>
                <small style="color:#000000;"><?php echo date("F d, Y") . ' (' . date("l") . ')'; ?></small>
                <hr>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location' AND cond='1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn, "SELECT * FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample1" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total Asset Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Asset' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample1">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT asset_name,COUNT(*) as count FROM tbl_asset_inv WHERE asset_type='Warehouse' AND location='$location' AND cond='1' GROUP BY asset_name ORDER BY asset_name");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $asset_name = $row['asset_name'];

                                    $pos_query = mysqli_query($conn,"SELECT asset_name,COUNT(*) as submitcount FROM tbl_asset_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND asset_name='$asset_name'");
                                    $count_goodpos = mysqli_fetch_array($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $count_good = $count_goodpos['submitcount'];
                                    $averagepos = (($count_good / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $asset_name.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'Asset Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND attendance = '1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location='$location' AND shift='2' AND is_active='1' AND department='Logistics-Office'");
                        $count_total = mysqli_num_rows($total_query);
                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample2" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total Office Attendance Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample2">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT position,COUNT(*) as count FROM tbl_employees WHERE location='$location' AND shift='2' AND is_active='1' AND department='Logistics-Office' GROUP BY position ORDER BY position");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $position = $row['position'];

                                    $pos_query = mysqli_query($conn,"SELECT position,attendance FROM tbl_ofattendance_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND attendance='1' AND position='$position'");
                                    $count_goodpos = mysqli_num_rows($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $averagepos = (($count_goodpos / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $position.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_attendance_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND attendance = '1'");
                        $count_good = mysqli_num_rows($good_query);
                        $total_query = mysqli_query($conn,"SELECT * FROM tbl_employees WHERE location='$location' AND shift='2' AND is_active='1' AND department='Logistics-WH'");
                        $count_total = mysqli_num_rows($total_query);

                        if ($count_total > 0) {
                            $average = (($count_good / $count_total) * 100);
                        } else {
                            $average = 0; // or handle this case differently, if needed
                        }
                        $average = number_format($average, 2);;
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample3" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total WH Attendance Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'WH Attendance' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample3">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $totalpos_query = mysqli_query($conn,"SELECT position,COUNT(*) as count FROM tbl_employees WHERE location='$location' AND shift='2' AND is_active='1' AND department='Logistics-WH' GROUP BY position ORDER BY position");
                                while($row = mysqli_fetch_array($totalpos_query)){
                                    $position = $row['position'];

                                    $pos_query = mysqli_query($conn,"SELECT position,attendance FROM tbl_attendance_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND attendance='1' AND position='$position'");
                                    $count_goodpos = mysqli_num_rows($pos_query);
                                    
                                    $count_totalpos = $row['count'];
                                    $averagepos = (($count_goodpos / $count_totalpos) * 100);
                                    $averagepos = number_format($averagepos,2);
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $position.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Attendance Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row no-gutters align-items-center"> 
                    <div class="col-12">
                        <?php
                        //average total
                        $good_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1'");
                        $count_good = mysqli_num_rows($good_query);
                        $count_total = $count_good * 5;
                        $score_query = mysqli_query($conn,"SELECT sum(c1+c2+c3+c4+c5) as totalscore FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='1' AND shift_type='1'");
                        $fetch_score = mysqli_fetch_array($score_query);
                        $count_score = $fetch_score['totalscore'];
                        if ($count_total != 0) {
                            $average = (($count_score / $count_total) * 100);
                        } else {
                        }
                        $average = number_format($average,2);
                        ?>
                        <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#multiCollapseExample4" onclick="toggleIcon(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Total WH Zone Score</b></span>
                                <?php
                                $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'WH Zone' AND location = '$location'");
                                $fetch_kpi = mysqli_fetch_array($kpi_query);

                                if($average == '0.00'){
                                    echo '<span><i>No Data Encoded</i></span>';
                                }else{
                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<span class="text-danger"><i><b>'.$average.'%</i></b></span>';
                                    } else if ($average <= $fetch_kpi['warning']) {
                                        echo '<span class="text-warning"><i><b>'.$average.'%</i></b></span>';
                                    } else {
                                        echo '<span class="text-success"><i><b>'.$average.'%</i></b></span>';
                                    }
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                    <div class="col-12">
                        <div class="card collapse" id="multiCollapseExample4">
                            <div class="card-body w-100">
                                <?php
                                //average per position
                                $zone_query = mysqli_query($conn,"SELECT * FROM tbl_zone_location WHERE location='$location' AND is_active='1' ORDER BY location_name");
                                while($fetch_zone = mysqli_fetch_array($zone_query)){
                                $zone = $fetch_zone['location_name'];
                                $pos_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$now' AND location='$location' AND shift='2' AND shift_type='1' AND location_name = '$zone'");
                                $count_pos = mysqli_num_rows($pos_query);
                                if($count_pos <= 0){
                                    $average = 0.00;
                                }else{
                                    $fetch_pos = mysqli_fetch_array($pos_query);
                                    $count_goodpos = $fetch_pos['c1'] + $fetch_pos['c2'] + $fetch_pos['c3'] + $fetch_pos['c4'] + $fetch_pos['c5'];
                        
                                    $averagepos = (($count_goodpos / 5) * 100);
                                    $averagepos = number_format($averagepos,2);
                                }
                                ?>
                                <div class="d-flex justify-content-between align-items-center" style="font-size:15px;">
                                    <span><?php echo $zone.' Score'; ?></span>
                                    <?php
                                    $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Zone Pos' AND location = '$location'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if($averagepos <= $fetch_kpi['danger']){
                                        echo '<span class="text-danger"><center><i>'.$averagepos.'%</i></span>';
                                    }else if($averagepos <= $fetch_kpi['warning']){
                                        echo '<span class="text-warning"><center><i>'.$averagepos.'%</i></span>';
                                    }else{
                                        echo '<span class="text-success"><center><i>'.$averagepos.'%</i></span>';
                                    }
                                    ?>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <?php
                if($location != 'CAINTA'){
                }else{    
                    echo '<br><center><small style="color:#000000;"><i>No Admin Metrics For 2nd & 3rd Shift</i></small></center>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-12">
        <div class="card shadow h-100 w-100">
            <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Variance Forms Summary:</h6>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-12">
                            <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#varianceCollapseExample1" onclick="toggleIcon(this)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Picking Variance Summary</b></span>
                                </div>
                            </button>
                        </div>
                        <div class="col-12">
                            <div class="card collapse" id="varianceCollapseExample1">
                                <div class="card-body w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><b><i>Top 3 Skus Case Count per Category</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Short Picked</i></span>
                                            <span class="badge badge-success badge-sm"><i>Over Picked</i></span>
                                            <span class="badge badge-info badge-sm"><i>Not In Invoice</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Sku Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Category</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Short Picked' AND form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Over Picked' AND form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Not In Invoice' AND form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><b><i>Top 3 Employee Case Count per Position</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Picker</i></span>
                                            <span class="badge badge-success badge-sm"><i>Checker</i></span>
                                            <span class="badge badge-info badge-sm"><i>Driver</i></span>
                                            <span class="badge badge-warning badge-sm"><i>Helper</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Employee Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Position</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(picker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY picker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['picker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Picker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(checker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY checker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['checker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Checker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(driver_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY driver_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['driver_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Driver</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(helper_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'PVF' AND status = 1 AND location = '$location' GROUP BY helper_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-warning" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['helper_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Helper</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#varianceCollapseExample2" onclick="toggleIcon(this)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Loading Variance Summary</b></span>
                                </div>
                            </button>
                        </div>
                        <div class="col-12">
                            <div class="card collapse" id="varianceCollapseExample2">
                                <div class="card-body w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                    <span><b><i>Top 3 Skus Case Count per Category</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Shortlanded</i></span>
                                            <span class="badge badge-success badge-sm"><i>Overlanded</i></span>
                                            <span class="badge badge-info badge-sm"><i>Not In Invoice</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Sku Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Category</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Shortlanded' AND form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Overlanded' AND form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Not In Invoice' AND form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><b><i>Top 3 Employee Case Count per Position</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Picker</i></span>
                                            <span class="badge badge-success badge-sm"><i>Checker</i></span>
                                            <span class="badge badge-info badge-sm"><i>Driver</i></span>
                                            <span class="badge badge-warning badge-sm"><i>Helper</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Employee Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Position</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(picker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY picker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['picker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Picker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(checker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY checker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['checker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Checker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(driver_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY driver_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['driver_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Driver</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(helper_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'LVF' AND status = 1 AND location = '$location' GROUP BY helper_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-warning" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['helper_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Helper</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn text-dark bg-gray-100 btn-block btn-sm w-100" data-toggle="collapse" href="#varianceCollapseExample3" onclick="toggleIcon(this)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i id="arrow-icon" class="fa fa-arrow-down fa-sm"></i> <b>Redel Variance Summary</b></span>
                                </div>
                            </button>
                        </div>
                        <div class="col-12">
                            <div class="card collapse" id="varianceCollapseExample3">
                                <div class="card-body w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                    <span><b><i>Top 3 Skus Case Count per Category</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Shortlanded</i></span>
                                            <span class="badge badge-success badge-sm"><i>Overlanded</i></span>
                                            <span class="badge badge-info badge-sm"><i>Not In Invoice</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Sku Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Category</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Shortlanded' AND form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Overlanded' AND form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf_query = mysqli_query($conn,"SELECT *,COUNT(picked_sku) AS sku_count FROM tbl_variance_raw WHERE error_type = 'Not In Invoice' AND form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY picked_sku ORDER BY sku_count DESC
                                                ");
                                                while($fetch_pvf = mysqli_fetch_array($pvf_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf['picked_sku'].' - '.$fetch_pvf['picked_desc'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf['sku_count'].'</center></td>';
                                                    echo '<td><center>'.$fetch_pvf['error_type'].'</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><b><i>Top 3 Employee Case Count per Position</i></b></span>
                                        <div>
                                            <span class="badge badge-primary badge-sm"><i>Picker</i></span>
                                            <span class="badge badge-success badge-sm"><i>Checker</i></span>
                                            <span class="badge badge-info badge-sm"><i>Driver</i></span>
                                            <span class="badge badge-warning badge-sm"><i>Helper</i></span>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="bg-primary text-light">
                                                <th class="text-center">Employee Name</th>
                                                <th class="text-center">CC</th>
                                                <th class="text-center">Position</th>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(picker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY picker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-primary" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['picker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Picker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(checker_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY checker_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-success" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['checker_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Checker</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(driver_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY driver_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-info" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['driver_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Driver</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                                <?php
                                                $pvf2_query = mysqli_query($conn,"SELECT *,COUNT(helper_name) AS employee_count FROM tbl_variance_ref WHERE form_type = 'RVF' AND status = 1 AND location = '$location' GROUP BY helper_name ORDER BY employee_count DESC");
                                                while($fetch_pvf2 = mysqli_fetch_array($pvf2_query)){
                                                    echo '<tr class="table-warning" style="color:#000000;">';
                                                    echo '<td>'.$fetch_pvf2['helper_name'].'</td>';
                                                    echo '<td><center>'.$fetch_pvf2['employee_count'].'</center></td>';
                                                    echo '<td><center>Helper</center></td>';
                                                    echo '</tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>


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
        function toggleIcon(button) {
            const icon = button.querySelector('#arrow-icon');
            if (icon.classList.contains('fa-arrow-down')) {
                icon.classList.remove('fa-arrow-down');
                icon.classList.add('fa-arrow-up');
            } else {
                icon.classList.remove('fa-arrow-up');
                icon.classList.add('fa-arrow-down');
            }
        }
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