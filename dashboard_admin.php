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
include_once("nav_admin.php");

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
        <small>Here’s what’s going on at Admin right now</small>
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
                <div class="col-12">
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
            </div>
        </div>
    </form>

<?php
date_default_timezone_set("Asia/Manila");
$now = date("Y-m-d");

// Function to get the status cell color
function getStatusCellColor($count, $is_validated, $check_date) {
    $today = date("Y-m-d");

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

// Determine current week's Monday
$date = isset($_GET['weeks']) ? $_GET['weeks'] : date("Y-m-d");
$ts = strtotime($date);
$dow = date('w', $ts);
$offset = $dow - 1;
if ($offset < 0) {
    $offset = 6;
}
$ts -= $offset * 86400;
$startDate = date("Y-m-d", $ts);
$endDate = date("Y-m-d", $ts + (6 * 86400));
?>

<div class="row">
    <!-- Closing Checklist -->
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card shadow h-100 ">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Admin Checklist
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
                                    'Warehouse Lock' => 'tbl_warehouselock_raw'
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="text-center bg-info text-light">
                                <tr>
                                    <th>Admin Modules</th>
                                    <th>Weekly</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Array of modules
                                $modules = [
                                    'General Facilities' => 'tbl_facility_raw',
                                    'Fire Extinguisher' => 'tbl_fireext_raw',
                                    'Mouse Trap' => 'tbl_mousetrap_raw'
                                ];

                                foreach ($modules as $module => $table) {
                                    echo '<tr>';
                                    echo '<td>' . $module . '</td>';

                                    $query = mysqli_query($conn, "SELECT * FROM $table WHERE date BETWEEN '$startDate' AND '$endDate' AND location='$location' AND shift='$shift' AND shift_type='1'");
                                    $fetch = mysqli_fetch_array($query);
                                    $count = mysqli_num_rows($query);

                                    $color = getStatusCellColor($count, $fetch['is_validated'] ?? null, $startDate);
                                    echo '<td class="' . $color . '"></td>';

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
                            <span class="badge badge-primary float-right"><a href="checklist_admin.php" class="text-light"><i class="fa fa-arrow-right fa-sm"></i> Go To Checklist</a></span>
                        </tr>
                    </table>
                <hr>
                <small><center><i>Always check your filtered data when checking</i></center></small>
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