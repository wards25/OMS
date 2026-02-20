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
include_once("nav.php");

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
                    // Set the timezone (modify as needed)
                    date_default_timezone_set('America/New_York');

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
                <small>Here’s what’s going on at OMS right now</small>
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
                                        <i class="fas fa-boxes fa-2x text-light"></i>
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
            // $role_id = $_SESSION['role_id'];
            // $role_query = mysqli_query($conn,"SELECT * FROM tbl_roles WHERE id = '$role_id'");
            // $fetch_role = mysqli_fetch_array($role_query);
            // $role = $fetch_role['role_name'];
            include_once("dashboard_whse.php");
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