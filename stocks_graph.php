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
include_once("nav_stocks.php");

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
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <!-- <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0 text-gray-800 d-flex justify-content-between align-items-center">
             
        </h1>
        <a type="button" href="javascript:window.close()" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-times"></i> Close</a>
    </div>
    <hr> -->

    <?php
        $date = date("Y-m-d");

        // 1. Fetch all stock data for location and date
        $stock_query = mysqli_query($conn, "
            SELECT * FROM tbl_inventory_stocks 
            WHERE location = '$location' AND DATE(submit_date) = '$date'
        ");

        // 2. Fetch SKU-level inventory grouped by sku and principal
        $sku_level_query = mysqli_query($conn, "
            SELECT sku, principal, SUM(qty) AS total_qty
            FROM tbl_inventory_stocks
            WHERE location = '$location'
            GROUP BY sku, principal
        ");

        // 3. Fetch all relevant products in one query for performance
        $all_skus = [];
        while ($row = mysqli_fetch_assoc($sku_level_query)) {
            $all_skus[$row['sku']] = [
                'principal' => $row['principal'],
                'qty' => $row['total_qty']
            ];
        }

        // Reset pointer to reuse $sku_level_query rows
        mysqli_data_seek($sku_level_query, 0);

        $sku_list = "'" . implode("','", array_keys($all_skus)) . "'";
        $product_query = mysqli_query($conn, "
            SELECT itemcode, UPPER(TRIM(uom)) AS uom, percase 
            FROM tbl_product 
            WHERE itemcode IN ($sku_list)
        ");

        // Build lookup for product details
        $product_info = [];
        while ($row = mysqli_fetch_assoc($product_query)) {
            $product_info[$row['itemcode']] = [
                'uom' => $row['uom'],
                'percase' => floatval($row['percase'])
            ];
        }

        // 4. Calculate converted quantities per principal
        $converted_data = [];
        while ($row = mysqli_fetch_assoc($sku_level_query)) {
            $sku = $row['sku'];
            $principal = $row['principal'];
            $qty = $row['total_qty'];

            if (isset($product_info[$sku])) {
                $uom = $product_info[$sku]['uom'];
                $percase = $product_info[$sku]['percase'];

                if ($uom !== 'CS' && $percase > 0) {
                    $qty = $qty / $percase;
                }
                // else qty stays same if UOM = 'CS'
            }

            $converted_data[$principal] = ($converted_data[$principal] ?? 0) + $qty;
        }

        // 5. Sort principals by converted quantity desc without limiting
        arsort($converted_data);
        $top_principals = $converted_data; // No array_slice = no limit

        // 6. Get distinct SKU counts per principal in one query
        $principal_list = "'" . implode("','", array_keys($top_principals)) . "'";
        $sku_count_query = mysqli_query($conn, "
            SELECT principal, COUNT(DISTINCT sku) AS distinct_sku
            FROM tbl_inventory_stocks
            WHERE location = '$location' AND principal IN ($principal_list)
            GROUP BY principal
        ");

        $principal_distinct_sku_lookup = [];
        while ($row = mysqli_fetch_assoc($sku_count_query)) {
            $principal_distinct_sku_lookup[$row['principal']] = $row['distinct_sku'];
        }

        // Prepare arrays for output
        $principals = [];
        $principal_qty = [];
        $principal_distinct_sku = [];

        foreach ($top_principals as $principal => $qty) {
            $principals[] = $principal;
            $principal_qty[] = round($qty, 2);
            $principal_distinct_sku[] = $principal_distinct_sku_lookup[$principal] ?? 0;
        }

        // 7. Fetch company-level data (unchanged)
        $qty_query_company = mysqli_query($conn, "
            SELECT company AS label, SUM(qty) AS total_qty, COUNT(DISTINCT sku) AS distinct_sku 
            FROM tbl_inventory_stocks 
            WHERE location = '$location' 
            GROUP BY company
            ORDER BY total_qty DESC
        ");

        $companies = $company_qty = $company_distinct_sku = [];

        while ($row = mysqli_fetch_assoc($qty_query_company)) {
            $companies[] = $row['label'];
            $company_qty[] = $row['total_qty'];
            $company_distinct_sku[] = $row['distinct_sku'];
        }

        // 8. Total qty across all principals (in CS)
        $total_principal_qty = array_sum($converted_data);
    ?>

<style>
    .chart-wrapper {
        min-height: 400px;
    }

    #chartContainer {
        position: relative;
        width: 100%;
        min-height: 400px;
        overflow: visible;
    }

    #qtyBarChart {
        width: 100% !important;
        height: auto !important;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="card shadow h-100 ">
            <!-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Max Qty Per Principal
                </h6>
            </div> -->
            <div class="card-body position-relative">
                <!-- Your Close Button in Top Right -->
                <a type="button" href="javascript:window.close()" 
                   class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm position-absolute" 
                   style="top: 15px; right: 12px; z-index: 10;">
                    <i class="fa fa-times"></i> Close
                </a>

                <div class="row no-gutters align-items-start flex-column chart-wrapper">
                    <div class="mb-3 d-flex align-items-center w-100">
                        <label for="viewSelect" class="mr-2"><strong>Full Qty Graph View:</strong></label>
                        <select id="viewSelect" class="form-control form-control-sm w-auto">
                            <option value="principal">By Principal</option>
                            <option value="company">By Company</option>
                        </select>
                        &nbsp;&nbsp;
                        <form method="GET" action="">
                            <select id="locationFilter" name="location" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 200px;">
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
                        </form>
                    </div>

                    <!-- Chart Container -->
                    <div id="chartContainer" style="position: relative; width: 100%; height: auto;">
                        <canvas id="qtyBarChart"></canvas>
                    </div>

                    <!-- Reset Zoom Button -->
                    <div class="mt-2 justify-content-end w-100">
                        <!-- <button onclick="qtyBarChart.resetZoom()" class="btn btn-sm btn-secondary">
                            Reset Zoom
                        </button> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<br>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Principal Name</th>
                            <th>Total CS Qty</th>
                            <th>Distinct SKUs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_principals as $principal => $qty): ?>
                            <tr>
                                <td><?= htmlspecialchars($principal) ?></td>
                                <td><?= number_format($qty, 2) ?></td>
                                <td><?= $principal_distinct_sku_lookup[$principal] ?? 0 ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script> <!-- Added Zoom Plugin -->

    <script>
        $(document).ready(function() {
            $('#dataTable1').DataTable({
                "order": [[2, "desc"]]
            });
            $('#dataTable2').DataTable({
                "order": [[1, "desc"]],
                "pageLength": 5
            });
        });

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

        const chartLabels = {
            principal: <?php echo json_encode($principals); ?>,
            company: <?php echo json_encode($companies); ?>
        };

        const chartData = {
            principal: <?php echo json_encode($principal_qty); ?>,
            company: <?php echo json_encode($company_qty); ?>
        };

        const distinctSkuData = {
            principal: <?php echo json_encode($principal_distinct_sku); ?>,
            company: <?php echo json_encode($company_distinct_sku); ?>
        };

        function updateChartHeight(labels) {
            const baseHeight = 400;
            const perLabelHeight = 35;
            const newHeight = Math.max(baseHeight, labels.length * perLabelHeight);
            document.getElementById('chartContainer').style.height = `${newHeight}px`;
        }

        const initialView = document.getElementById('viewSelect').value;
        const initialLabels = chartLabels[initialView];
        updateChartHeight(initialLabels);

        const ctx = document.getElementById('qtyBarChart').getContext('2d');
        const qtyBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: initialLabels,
                datasets: [
                    {
                        label: 'Total CS Qty per Principal',
                        data: chartData[initialView],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        hidden: false
                    },
                    {
                        label: 'Distinct SKUs per Principal',
                        data: distinctSkuData[initialView],
                        backgroundColor: 'rgba(255, 99, 132, 0.8)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        barThickness: 10,
                        categoryPercentage: 0.5,
                        barPercentage: 0.8,
                        hidden: false
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                        beginAtZero: true
                    },
                    y: {
                        stacked: false,
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        onClick: function(evt, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const meta = ci.getDatasetMeta(index);
                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                            sortAndUpdateChart(ci);
                        }
                    }
                }
            }
        });

        function sortAndUpdateChart(chartInstance) {
            const view = document.getElementById('viewSelect').value;

            let labels = [...chartLabels[view]];
            let qtyData = [...chartData[view]];
            let skuData = [...distinctSkuData[view]];

            const qtyVisible = !chartInstance.getDatasetMeta(0).hidden;
            const skuVisible = !chartInstance.getDatasetMeta(1).hidden;

            let combined = labels.map((label, i) => ({
                label: label,
                qty: qtyData[i],
                sku: skuData[i]
            }));

            if (skuVisible && !qtyVisible) {
                combined.sort((a, b) => b.sku - a.sku);
            } else {
                combined.sort((a, b) => b.qty - a.qty);
            }

            labels = combined.map(item => item.label);
            qtyData = combined.map(item => item.qty);
            skuData = combined.map(item => item.sku);

            chartInstance.data.labels = labels;
            chartInstance.data.datasets[0].data = qtyData;
            chartInstance.data.datasets[1].data = skuData;

            updateChartHeight(labels);
            chartInstance.update();
        }

        document.getElementById('viewSelect').addEventListener('change', function () {
            sortAndUpdateChart(qtyBarChart);
        });

        document.getElementById('viewSelect').dispatchEvent(new Event('change'));
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