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

$excluded_racks = "'BSMULTILINES','BSBONAFIDE','BSCLUTCH','HANDSHAKESTAGING','COLDROOM1','COLDROOM2','BSULP','INBOUNDSTAGING','OUTBOUNDSTAGING','W3STAGING'";
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <form method="GET" action="">
        <h1 class="h3 mb-0 text-gray-800 d-flex justify-content-between align-items-center">
        <span class="text-success"><b><?php if($location == 'CDO'){ echo strtoupper($location); } else { echo ucwords(strtolower($location)); } ?> Dashboard</b></span>
            <select id="locationFilter" name="location" class="form-control form-control-sm shadow-sm" onchange="this.form.submit()" style="width:25%;">
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
        </h1>
        <small>Here’s what’s going on at Stocks right now</small>
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
                                    Number of SKUs
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $sku_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_stocks WHERE location='$location' GROUP BY sku");
                                    $count_sku = mysqli_num_rows($sku_query);
                                    echo $count_sku;
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-barcode fa-2x text-light"></i>
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
                                    SKU Quantities
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    $qty_query = mysqli_query($conn,"SELECT sum(qty) FROM tbl_inventory_stocks WHERE location='$location'");
                                    $fetch_qty = mysqli_fetch_assoc($qty_query);
                                    echo number_format($fetch_qty['sum(qty)'],2);
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-boxes text-light"></i>
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
                                    Available Racks
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    // Build the SQL query for Racking category
                                    $racking_query = "
                                        SELECT COUNT(*) as count
                                        FROM tbl_inventory_rack
                                        WHERE rack NOT IN ($excluded_racks)
                                        AND location = '$location'
                                        GROUP BY rack, col, level
                                    ";

                                    // Execute the query
                                    $racking_result = mysqli_query($conn, $racking_query);

                                    // Count the number of resulting rows (distinct pallet positions)
                                    $racking_count = mysqli_num_rows($racking_result);

                                    // Build the SQL query to count occupied pallet positions in Racking area
                                    $rackingOccupiedCountQuery = "
                                        SELECT COUNT(*) AS total
                                        FROM (
                                            SELECT rack, col, level
                                            FROM tbl_inventory_stocks
                                            WHERE rack NOT IN ($excluded_racks)
                                            AND location = '$location'
                                            GROUP BY rack, col, level
                                        ) AS uniqueOccupiedRacks
                                    ";

                                    // Execute the query
                                    $rackingOccupiedCountResult = mysqli_query($conn, $rackingOccupiedCountQuery);

                                    // Retrieve the result
                                    $rackingOccupiedRow = mysqli_fetch_assoc($rackingOccupiedCountResult);
                                    $rackingOccupiedCount = $rackingOccupiedRow['total'] ?? 0;

                                    // Output the formatted result
                                    $empty_racks = $racking_count - $rackingOccupiedCount;
                                    // Output or use the value
                                    echo number_format($empty_racks).' / '.number_format($racking_count);
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-grip fa-2x text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2 bg-danger">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-light text-uppercase mb-1">
                                    Percent Available
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-light">
                                    <?php
                                    // Output the formatted result
                                    $empty_racks = $racking_count - $rackingOccupiedCount;
                                    if ($racking_count != 0) {
                                        $percentage_rack = ($empty_racks / $racking_count) * 100;
                                    } else {
                                        $percentage_rack = 0; // or set to null or an appropriate default
                                    }
                                    echo number_format($percentage_rack, 2) . '%';
                                    ?>
                                </div>
                                <small class="mb-0 text-light">as of <?php echo date("h:i A"); ?></small>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-percent text-light"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <!-- Content Row -->
        <div class="row">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-md-12 mb-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm shadow-sm">
                        <thead class="text-center bg-success text-white">
                            <tr class="table-success text-secondary">
                                <th>Racking</th>
                                <th>BlkStock</th>
                                <th>ColdRoom</th>
                                <th>Inbound</th>
                                <th>Outbound</th>
                                <th>Handshake</th>
                                <th>W3Staging</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white font-weight-bold">
                            <?php
                            $categories = [
                                'Racking' => ["NOT IN", ['BSMULTILINES', 'BSBONAFIDE', 'BSCLUTCH', 'HANDSHAKESTAGING', 'COLDROOM1', 'COLDROOM2', 'BSULP', 'INBOUNDSTAGING', 'OUTBOUNDSTAGING', 'W3STAGING']],
                                'BlkStock' => ["IN", ['BSMULTILINES', 'BSBONAFIDE', 'BSCLUTCH', 'BSULP']],
                                'ColdRoom' => ["IN", ['COLDROOM1', 'COLDROOM2']],
                                'Inbound' => ["IN", ['INBOUNDSTAGING']],
                                'Outbound' => ["IN", ['OUTBOUNDSTAGING']],
                                'Handshake' => ["IN", ['HANDSHAKESTAGING']],
                                'W3Staging' => ["IN", ['W3STAGING']]
                            ];

                            $totals = [];

                            foreach ($categories as $key => [$condition, $racks]) {
                                $rack_list = "'" . implode("','", $racks) . "'";
                                $operator = $condition === "IN" ? "IN" : "NOT IN";
                                $query = "SELECT COUNT(*) as count FROM tbl_inventory_rack WHERE rack $operator ($rack_list) AND location = '$location' GROUP BY rack, col, level";
                                $result = mysqli_query($conn, $query);
                                $totals[$key] = mysqli_num_rows($result);
                            }

                            // Print each column
                            echo '<tr class="text-center">';
                            foreach ($totals as $count) {
                                echo '<td>' . number_format($count) . '</td>';
                            }
                            echo '</tr>';

                            // Calculate total_active
                            $total_active = array_sum($totals);
                            ?>
                            <tr class="table-warning font-weight-bold text-center">
                                <td colspan="7"><i class="fa fa-solid fa-table-cells"></i> TOTAL PALLET COUNT : <?php echo number_format($total_active); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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

        // 5. Sort principals by converted quantity desc and take top 10
        arsort($converted_data);
        $top_principals = array_slice($converted_data, 0, 10, true);

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
            LIMIT 10
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

<div class="row">
    <div class="col-xl-8 col-md-8 mb-4">
        <div class="card shadow h-100">
            <!-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Max Qty Per Principal
                </h6>
            </div> -->
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="mb-3 d-flex align-items-center">
                        <label for="viewSelect" class="mr-2"><strong>Top 10 Qty Graph View:</strong></label>
                        <select id="viewSelect" class="form-control form-control-sm w-auto">
                            <option value="principal">By Principal</option>
                            <option value="company">By Company</option>
                        </select>
                    </div>
                    <div style="position: relative; height: 60vh; width: 100%;">
                        <canvas id="qtyBarChart"></canvas>
                    </div>
                    <div class="mt-2">
                        <a type="button" class="btn btn-sm text-white" style="background-color: #86c7f3;" href="stocks_graph.php" target="_blank">View Full Graph</a>
                        <button onclick="qtyBarChart.resetZoom()" class="btn btn-sm btn-secondary">
                            Reset Zoom
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 mb-4">
        <div class="card shadow h-100 ">
            <!-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Rack Percentage (with SKU)
                </h6>
            </div> -->
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <!-- Table Display -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center table-striped" id="dataTable1">
                            <thead class="text-center bg-info text-light">
                                <tr>
                                    <th>Rack</th>
                                    <th>Slots</th>
                                    <th>% w/ SKU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Modify query to order by rack
                                $rack_query = mysqli_query($conn,"SELECT rack, sku, COUNT(*) as rack_count FROM tbl_inventory_rack WHERE location = '$location' GROUP BY rack ORDER BY rack + 0 ASC");

                                while($fetch_rack = mysqli_fetch_assoc($rack_query)){
                                    $rack = $fetch_rack['rack'];
                                    $rack_count = $fetch_rack['rack_count'];

                                    // Query to count SKUs that are not 'NO SKU' and not empty
                                    $valid_sku_query = mysqli_query($conn,"SELECT count(sku) FROM tbl_inventory_rack WHERE rack = '$rack' AND (sku != 'NO SKU' AND sku != '')");
                                    $fetch_valid_sku = mysqli_fetch_assoc($valid_sku_query);
                                    $valid_sku = $fetch_valid_sku['count(sku)'];

                                    $percentage = ($valid_sku / $rack_count) * 100;
                                    $slot = ($rack_count - $valid_sku);

                                    echo '<tr>';
                                    echo '<td>'.$fetch_rack['rack'].'</td>';
                                    echo '<td>'.$slot.'</td>';

                                    if($percentage == 100){
                                        echo '<td class="table-success">FULL</td>';
                                    }else{
                                        echo '<td>'.number_format($percentage,2).'%</td>';
                                    }
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
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card shadow h-100 ">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Movements Today: <?php echo date("F d, Y"); ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <!-- Table Display -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center" id="dataTable">
                            <thead class="text-center bg-primary text-light">
                                <tr>
                                    <th>Racklocation</th>
                                    <th>SKU</th>
                                    <th>Qty</th>
                                    <th>Principal</th>
                                    <th>Submitted By</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($stock_query) > 0) {
                                    while($fetch_stock = mysqli_fetch_assoc($stock_query)){

                                        if($fetch_stock['status'] == 'ACTIVE'){
                                            $row_class = 'table-success';
                                        }else if($fetch_stock['status'] == 'HOLD'){
                                            $row_class = 'table-warning';
                                        }else if($fetch_stock['status'] == 'FREE GOODS/PREMIUM'){
                                            $row_class = 'table-info';
                                        }else{
                                            $row_class = '';
                                        }

                                        echo '<tr>';
                                        echo '<td class="'.$row_class.'">' . $fetch_stock['racklocation'] . '</td>';
                                        echo '<td>' . $fetch_stock['sku'] . ' - '.$fetch_stock['description'].'</td>';
                                        echo '<td>' . $fetch_stock['qty'] . ' ' . $fetch_stock['uom'] . '</td>';
                                        echo '<td>' . $fetch_stock['principal'] . '</td>';
                                        echo '<td>' . $fetch_stock['submit_by'] . '</td>';
                                        echo '<td><a class="btn btn-sm btn-warning" href="stocks_level.php?location=' . $location . '&rack=' . $fetch_stock['rack'] . '&column=' . $fetch_stock['col'] . '&update=x" target="_blank"><i class="fa-solid fa-barcode"></i></a></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                    echo '<td colspan="6">No updated stocks found for this location.</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                    <table class="table-responsive table-sm">
                        <tr>
                            <td><small><u><b>Elements:</u></small></b></u></small></td>
                            <td><span class="badge badge-success">ACTIVE</span></td>
                            <td><span class="badge badge-warning">HOLD</span></td>
                            <td><span class="badge badge-info">FREE GOODS/PREMIUM</span></td>
                        </tr>
                        <tr>
                            <span class="badge badge-primary float-right"><a href="stocks.php" class="text-light"><i class="fa fa-arrow-right fa-sm"></i> Go To Summary</a></span>
                        </tr>
                    </table>
                <hr>
                <small><center><i>Always check your filtered data when checking</i></center></small>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-2 d-flex flex-row align-items-center justify-content-between bg-success">
                <h6 class="m-0 font-weight-bold text-light">Pallet Occupancy</h6>
            </div>
            <!-- Set flex container for progress bar and table -->
            <div class="card-body d-flex">
                <!-- Rack Occupancy Section with Scrollable Progress Bars -->
                <div class="progress-bars" style="width: 48%; height: 50vh; overflow-y: auto;">
                    <?php
                    // Step 1: Get total slots per rack
                    $total_query = mysqli_query($conn, "
                        SELECT rack, COUNT(*) AS total_slots 
                        FROM (
                            SELECT DISTINCT rack, col, level 
                            FROM tbl_inventory_rack 
                            WHERE location = '$location'
                              AND rack NOT IN ($excluded_racks)
                        ) AS unique_slots
                        GROUP BY rack
                    ");

                    // Step 2: Get occupied slots per rack (only with valid SKUs)
                    $occupied_query = mysqli_query($conn, "
                        SELECT rack, COUNT(*) AS occupied_slots 
                        FROM (
                            SELECT DISTINCT rack, col, level 
                            FROM tbl_inventory_rack 
                            WHERE location = '$location'
                              AND sku != 'NO SKU' AND sku != ''
                              AND rack NOT IN ($excluded_racks)
                        ) AS occupied
                        GROUP BY rack
                    ");

                    // Step 3: Build arrays for easy lookup
                    $totals = [];
                    while ($row = mysqli_fetch_assoc($total_query)) {
                        $totals[$row['rack']] = $row['total_slots']; 
                    }

                    $occupied = [];
                    while ($row = mysqli_fetch_assoc($occupied_query)) {
                        $occupied[$row['rack']] = $row['occupied_slots'];
                    }

                    // Step 4: Build combined array with percentage
                    $racks = [];
                    foreach ($totals as $rack => $total_slots) {
                        $valid_sku = isset($occupied[$rack]) ? $occupied[$rack] : 0;
                        $percentage = ($total_slots > 0) ? ($valid_sku / $total_slots) * 100 : 0;
                        $slot = $total_slots - $valid_sku;

                        $racks[] = [
                            'rack' => $rack,
                            'total_slots' => $total_slots,
                            'valid_sku' => $valid_sku,
                            'percentage' => $percentage,
                            'slot' => $slot,
                        ];
                    }

                    // Step 5: Sort racks by percentage descending
                    usort($racks, function($a, $b) {
                        return $b['percentage'] <=> $a['percentage'];
                    });

                    // Step 6: Display
                    foreach ($racks as $rack_data) {
                        $percentage_display = number_format($rack_data['percentage'], 0);
                        $slot = $rack_data['slot'];

                        if ($rack_data['percentage'] == 100) {
                            $progress_class = "bg-danger";
                            $status_text = "FULL";
                        } elseif ($rack_data['percentage'] >= 70) {
                            $progress_class = "bg-warning";
                            $status_text = "$percentage_display%";
                        } elseif ($rack_data['percentage'] >= 40) {
                            $progress_class = "bg-info";
                            $status_text = "$percentage_display%";
                        } else {
                            $progress_class = "bg-success";
                            $status_text = "$percentage_display%";
                        }

                        echo '
                        <h6 class="small font-weight-bold">RACK '.$rack_data['rack'].' - '.$slot.' Slot(s)
                            <span class="float-right">'.$status_text.'</span>
                        </h6>
                        <div class="progress mb-3">
                            <div class="progress-bar '.$progress_class.'" role="progressbar" 
                                style="width: '.$percentage_display.'%" 
                                aria-valuenow="'.$percentage_display.'" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                            </div>
                        </div>';
                    }
                    ?>
                </div>


                <div class="table-responsive" style="width: 48%; overflow-y: auto; margin-left: 2.5%;">
                    <table class="table table-bordered table-sm text-center" id="dataTable2">
                        <thead class="text-center table-info">
                            <tr>
                                <th>Principal</th>
                                <th>Slots</th>
                                <th>% Occupied</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get total slots grouped by rack, col, level (excluding certain racks)
                            $rack_query = mysqli_query($conn, "
                                SELECT * FROM tbl_inventory_rack 
                                WHERE location = '$location' 
                                  AND rack NOT IN ($excluded_racks)
                                GROUP BY rack, col, level
                            ");
                            $count_rack = mysqli_num_rows($rack_query);

                            // Get slots grouped by principal, rack, col, level (each is 1 slot), excluding certain racks
                            $principal_query = mysqli_query($conn, "
                                SELECT principal, rack, col, level 
                                FROM tbl_inventory_stocks 
                                WHERE location = '$location' 
                                  AND rack NOT IN ($excluded_racks)
                                GROUP BY principal, rack, col, level
                            ");

                            $principal_counts = [];
                            while ($row = mysqli_fetch_assoc($principal_query)) {
                                $principal = $row['principal'];
                                if (!isset($principal_counts[$principal])) {
                                    $principal_counts[$principal] = 0;
                                }
                                $principal_counts[$principal]++; // Count slots per principal
                            }

                            $total_slots_occupied = 0;
                            $total_percentage = 0;
                            foreach ($principal_counts as $principal => $count) {
                                $percentage = ($count / $count_rack) * 100;
                                $total_slots_occupied += $count;
                                $total_percentage += $percentage;

                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($principal) . '</td>';
                                echo '<td>' . number_format($count) . '</td>'; // slots count
                                echo '<td>' . number_format($percentage, 2) . '%</td>'; // % occupied
                                echo '</tr>';
                            }

                            // Output total row
                            echo '<tr class="table-warning font-weight-bold">';
                            echo '<td><strong>Total</strong></td>';
                            echo '<td><strong>' . number_format($total_slots_occupied) . '</strong></td>';
                            echo '<td><strong>' . number_format($total_percentage, 2) . '%</strong></td>';
                            echo '</tr>';
                            ?>
                        </tbody>
                    </table>

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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1"></script> <!-- Added Zoom Plugin -->

    <script>
        $(document).ready(function() {
            $('#dataTable1').DataTable({
                "order": [[2, "desc"]]  // 3rd column (percentage), descending order
            });
            $('#dataTable2').DataTable({
                "order": [[1, "desc"]],  // 2nd column (percentage), descending order
                "pageLength": 5          // Show 5 entries per page
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

        const ctx = document.getElementById('qtyBarChart').getContext('2d');
        const qtyBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels.principal,
                datasets: [
                    {
                        label: 'Total CS Qty per Principal',
                        data: chartData.principal,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        hidden: false
                    },
                    {
                        label: 'Distinct SKUs per Principal',
                        data: distinctSkuData.principal,
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
                        stacked: false
                    }
                },
                plugins: {
                    legend: {
                        onClick: function(evt, legendItem, legend) {
                            const index = legendItem.datasetIndex;
                            const ci = legend.chart;
                            const meta = ci.getDatasetMeta(index);
                            // Toggle dataset visibility
                            meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;

                            // After toggling, update chart and resort data
                            sortAndUpdateChart(ci);
                        }
                    },
                    zoom: {
                        pan: { enabled: true, mode: 'xy' },
                        zoom: { wheel: { enabled: true }, pinch: { enabled: true }, mode: 'xy' }
                    }
                }
            }
        });

        function sortAndUpdateChart(chartInstance) {
            const view = document.getElementById('viewSelect').value;

            // Clone arrays to avoid mutation
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
                // Sort by distinct SKU descending
                combined.sort((a, b) => b.sku - a.sku);
            } else {
                // Sort by total CS qty descending
                combined.sort((a, b) => b.qty - a.qty);
            }

            // Unpack sorted data
            labels = combined.map(item => item.label);
            qtyData = combined.map(item => item.qty);
            skuData = combined.map(item => item.sku);

            chartInstance.data.labels = labels;
            chartInstance.data.datasets[0].data = qtyData;
            chartInstance.data.datasets[1].data = skuData;

            chartInstance.update();
        }

        // Also update sorting when changing view
        document.getElementById('viewSelect').addEventListener('change', function () {
            sortAndUpdateChart(qtyBarChart);
        });

        // Initialize chart sorted by total CS qty descending on page load
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