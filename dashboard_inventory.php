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
include_once("nav_inventory.php");

// Initialize variables

$company = isset($_GET['company']) && !empty($_GET['company']) ? $_GET['company'] : 'ALL COMPANIES'; //set default value
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
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <form method="GET" action="">
        <h1 class="h3 mb-0 text-gray-800 d-flex justify-content-between align-items-center">
        <span class="text-success"><b><?php if($location == 'CDO'){ echo strtoupper($location); } else { echo ucwords(strtolower($location)); } ?> Dashboard</b></span>
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
        </h1>
        <small>Here’s what’s going on at Inventory right now</small>
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
                                    Total Rack Locations</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $rack_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_rack WHERE location='$location'");
                                    $count_rack = mysqli_num_rows($rack_query);
                                    echo number_format($count_rack, 0);
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

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-warning">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Number of Groups</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $group_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_group WHERE location='$location' GROUP BY groupno");
                                    $count_group = mysqli_num_rows($group_query);
                                    echo $count_group;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-users text-light"></i>
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
                                    Total Ending Inventory</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $ending_query = mysqli_query($conn, "SELECT SUM(active) + SUM(hold) AS sum_ending FROM tbl_inventory_ending WHERE location='$location'");
                                    $count_ending = mysqli_fetch_assoc($ending_query);
                                    $sum_ending = $count_ending['sum_ending'] ?? 0;
                                    echo number_format($sum_ending, 0);
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

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-danger">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Total Beginning Inventory</div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $count_query = mysqli_query($conn, "SELECT SUM(qty) as sum_count FROM (SELECT DISTINCT qty, status, racklocation FROM tbl_inventory_count WHERE count_status = 'MATCH' AND location = '$location') AS distinct_inventory");
                                    $count_count = mysqli_fetch_assoc($count_query);
                                    $sum_count = $count_count['sum_count'] ?? 0;
                                    echo number_format($sum_count, 0);
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></a></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-barcode fa-2x text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<div class="row">
    <!-- Opening Checklist -->
    <div class="col-xl-8 col-md-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Initial Count Monitoring
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-3">
                                <label>Company:</label>
                            </div>
                            <div class="col-9">
                            <?php
                            $query = "SELECT company FROM tbl_inventory_ending GROUP BY company ORDER BY company";
                            $result = $conn->query($query);
                            if($result->num_rows> 0){
                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                <select class="form-control form-control-sm w-100" name="company" onchange="this.form.submit()">
                                    <option value="ALL COMPANIES">ALL COMPANIES</option>
                                <?php    
                                foreach ($options as $option) {
                                ?>
                                    <option value="<?php echo $option['company'];?>"><?php echo $option['company']; ?> </option>
                            <?php 
                                }
                            }
                            ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row no-gutters align-items-center">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm table-sm text-center">
                            <thead class="text-center bg-primary text-light">
                                <tr>
                                    <th colspan="3" style="background:#3c58ac;"><?php echo $company; ?></th>
                                </tr>
                                <tr>
                                    <th>Row Labels</th>
                                    <th>Count of SKU</th>
                                    <th>Match %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_skus = 0;
                                $match_count = 0;
                                $ongoing_count = 0;
                                $not_yet_counted = 0;

                                $company_filter = ($company !== 'ALL COMPANIES') ? "AND company='$company'" : "";

                                // Step 1: Get all count data (only MATCH status) with DISTINCT triple
                                $distinct_count_query = "
                                    SELECT DISTINCT sku, status, racklocation, qty
                                    FROM tbl_inventory_count
                                    WHERE count_status = 'MATCH'
                                ";
                                $count_result = mysqli_query($conn, $distinct_count_query);

                                // Step 2: Build a SKU-based map of active and hold
                                $oms_data = [];
                                while ($row = mysqli_fetch_assoc($count_result)) {
                                    $sku = $row['sku'];
                                    $status = $row['status'];
                                    $qty = $row['qty'];

                                    if (!isset($oms_data[$sku])) {
                                        $oms_data[$sku] = ['ACTIVE' => 0, 'HOLD' => 0];
                                    }

                                    if (!isset($oms_data[$sku][$status])) {
                                        $oms_data[$sku][$status] = 0;
                                    }

                                    if ($status === 'ACTIVE' || $status === 'HOLD') {
                                        $oms_data[$sku][$status] += (float)$qty;
                                    }
                                }

                                // Step 3: Loop through ending inventory per SKU
                                $ending_query = "
                                    SELECT sku, SUM(active) AS active_odoo, SUM(hold) AS hold_odoo
                                    FROM tbl_inventory_ending
                                    WHERE location='$location' $company_filter
                                    GROUP BY sku
                                ";
                                $ending_result = mysqli_query($conn, $ending_query);

                                while ($row = mysqli_fetch_assoc($ending_result)) {
                                    $sku = $row['sku'];
                                    $odoo_total = $row['active_odoo'] + $row['hold_odoo'];

                                    $active_oms = $oms_data[$sku]['ACTIVE'] ?? 0;
                                    $hold_oms = $oms_data[$sku]['HOLD'] ?? 0;
                                    $oms_total = $active_oms + $hold_oms;

                                    $total_skus++;

                                    if ($odoo_total == $oms_total) {
                                        $match_count++;
                                    } elseif ($oms_total == 0) {
                                        $not_yet_counted++;
                                    } else {
                                        $ongoing_count++;
                                    }
                                }

                                // Final percentages
                                $match_percent = ($total_skus > 0) ? round(($match_count / $total_skus) * 100, 2) : 0;
                                $ongoing_percent = ($total_skus > 0) ? round(($ongoing_count / $total_skus) * 100, 2) : 0;
                                $not_yet_counted_percent = ($total_skus > 0) ? round(($not_yet_counted / $total_skus) * 100, 2) : 0;

                                ?>
                                <tr>
                                    <td class="table-success"><b>Match</b></td>
                                    <td><?php echo $match_count; ?></td>
                                    <td><?php echo $match_percent; ?>%</td>
                                </tr>
                                <tr>
                                    <td class="table-warning"><b>On Going Count</b></td>
                                    <td><?php echo $ongoing_count; ?></td>
                                    <td><?php echo $ongoing_percent; ?>%</td>
                                </tr>
                                <tr>
                                    <td class="table-danger"><b>Not Yet Counted</b></td>
                                    <td><?php echo $not_yet_counted; ?></td>
                                    <td><?php echo $not_yet_counted_percent; ?>%</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100">
                                    <td><b>Grand Total</td>
                                    <td><b><?php echo $total_skus; ?></td>
                                    <td><b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- <hr>
                <small><center><i>Always check your filtered data when checking</i></center></small> -->
            </div>
        </div>
    </div>

    <!-- Closing Checklist -->
    <div class="col-xl-4 col-md-4 mb-4">
        <div class="card shadow h-100 ">
            <!-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Closing Checklist:
                </h6>
            </div> -->
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div id="chart-container">
                        <canvas id="inventoryChart"></canvas>
                    </div>
                </div>
                <!-- <hr>
                <small class="float-right"><i>as of <?php echo date("F d, Y h:i A"); ?></i></small> -->
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

    <style>
        #chart-container {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin: auto;
            position: relative;
            padding-top: -20px;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('inventoryChart').getContext('2d');

            // Fetch PHP values (replace with actual PHP code)
            var matchPercent = <?php echo isset($match_percent) ? $match_percent : 0; ?>;
            var ongoingPercent = <?php echo isset($ongoing_percent) ? $ongoing_percent : 0; ?>;
            var notYetCountedPercent = <?php echo isset($not_yet_counted_percent) ? $not_yet_counted_percent : 0; ?>;

            var matchCount = <?php echo isset($match_count) ? $match_count : 0; ?>;
            var ongoingCount = <?php echo isset($ongoing_count) ? $ongoing_count : 0; ?>;
            var notYetCounted = <?php echo isset($not_yet_counted) ? $not_yet_counted : 0; ?>;

            var isEmpty = (matchPercent + ongoingPercent + notYetCountedPercent) === 0;

            var data = {
                labels: isEmpty ? ['No Data Available'] : ['Match', 'On Going Count', 'Not Yet Counted'],
                datasets: [{
                    data: isEmpty ? [100] : [matchPercent, ongoingPercent, notYetCountedPercent],
                    backgroundColor: isEmpty ? ['#858796'] : ['#1cc88a', '#f6c23e', '#e74a3b']
                }]
            };

            var options = {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                let value = tooltipItem.raw;
                                let label = data.labels[tooltipItem.dataIndex];
                                let count = [matchCount, ongoingCount, notYetCounted][tooltipItem.dataIndex];
                                return `${label}: ${value}% (${count} SKUs)`;
                            }
                        }
                    }
                }
            };

            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: options
            });

            window.addEventListener('resize', function () {
                myChart.resize();
            });
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

    <!-- Chart JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>