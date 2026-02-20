<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(148, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Inventory Validate</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa-solid fa-plus"></i> Add SKU Rack</button> -->
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
                case 'succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Rack has been added successfully.';
                    break;
                case 'assign':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Counters has been assigned successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> You can only submit once per day.';
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
                        <h6 class="m-0 font-weight-bold text-light">Variance List</h6> 
                    </div>
                    <div class="card-body">
                        <!-- <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(140, $permission)){ echo 'href="inventory_export.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div> -->
                        <form method="GET" action="">
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-6">
                                        <?php
                                        $query = "SELECT company FROM tbl_inventory_ending GROUP BY company ORDER BY company";
                                        $result = $conn->query($query);
                                        if($result->num_rows> 0){
                                            $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                            <select class="form-control form-control-sm w-100" name="company" onchange="this.form.submit()">
                                                <option value="">Filter Company</option>
                                                <option value="ALL COMPANIES">ALL COMPANIES</option>
                                            <?php    
                                            foreach ($options as $option) {
                                            ?>
                                                <option value="<?php echo $option['company'];?>"><?php echo $option['company']; ?> </option>
                                        <?php 
                                            }
                                        }
                                            echo '</select>';
                                        ?>
                                    </div>
                                    <div class="col-6">
                                        <?php 
                                        $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                        $result = $conn->query($query);

                                        if ($result && $result->num_rows > 0) {
                                            $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                        ?>
                                            <select class="form-control form-control-sm" name="location" onchange="this.form.submit()">
                                                <option value="">Filter Location</option>
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
                                            echo '</select>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Odoo</th>
                                        <th>OMS</th>
                                        <th>Variance</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Recon1</th>
                                        <th>Recon2</th>
                                        <th>Recon3</th>
                                        <th>Recon4</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $company = $_GET['company'] ?? '';
                                $location = $_GET['location'] ?? '';

                                $query = "
                                    SELECT sku, description, SUM(active) AS active_odoo, SUM(hold) AS hold_odoo, uom, location 
                                    FROM tbl_inventory_ending
                                ";

                                $conditions = [];
                                if (!empty($company)) $conditions[] = "company='$company'";
                                if (!empty($location)) $conditions[] = "location='$location'";
                                if (!empty($conditions)) $query .= " WHERE " . implode(" AND ", $conditions);

                                $query .= " GROUP BY sku, description, uom, location";

                                $result = mysqli_query($conn, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $sku = $row['sku'];
                                    $odoo_total = $row['active_odoo'] + $row['hold_odoo'];

                                    // Get recount info (qty to qty5)
                                    $recount_query = mysqli_query($conn, "
                                        SELECT MAX(qty) AS qty, MAX(qty2) AS qty2, MAX(qty3) AS qty3, MAX(qty4) AS qty4, MAX(qty5) AS qty5
                                        FROM tbl_inventory_count
                                        WHERE sku = '$sku' AND count_status = 'MATCH'
                                    ");
                                    $recounts = mysqli_fetch_assoc($recount_query);
                                    $qty = $recounts['qty'] ?? 0;
                                    $qty2 = $recounts['qty2'] ?? 0;
                                    $qty3 = $recounts['qty3'] ?? 0;
                                    $qty4 = $recounts['qty4'] ?? 0;
                                    $qty5 = $recounts['qty5'] ?? 0;

                                    // Active OMS (distinct count based)
                                    $active_query = mysqli_query($conn, "
                                        SELECT SUM(qty_sum) AS active_oms 
                                        FROM (
                                            SELECT sku, racklocation, SUM(qty) AS qty_sum
                                            FROM (
                                                SELECT DISTINCT sku, racklocation, qty
                                                FROM tbl_inventory_count
                                                WHERE status = 'ACTIVE' AND count_status = 'MATCH'
                                            ) AS unique_counts
                                            GROUP BY sku, racklocation
                                        ) AS summed_counts
                                        WHERE sku = '$sku'
                                    ");
                                    $active_oms = mysqli_fetch_assoc($active_query)['active_oms'] ?? 0;

                                    // Hold OMS (distinct count based)
                                    $hold_query = mysqli_query($conn, "
                                        SELECT SUM(qty_sum) AS hold_oms 
                                        FROM (
                                            SELECT sku, racklocation, SUM(qty) AS qty_sum
                                            FROM (
                                                SELECT DISTINCT sku, racklocation, qty
                                                FROM tbl_inventory_count
                                                WHERE status = 'HOLD' AND count_status = 'MATCH'
                                            ) AS unique_counts
                                            GROUP BY sku, racklocation
                                        ) AS summed_counts
                                        WHERE sku = '$sku'
                                    ");
                                    $hold_oms = mysqli_fetch_assoc($hold_query)['hold_oms'] ?? 0;

                                    // Total + variance
                                    $oms_total = $active_oms + $hold_oms;
                                    $variance = $oms_total - $odoo_total;

                                    if ($variance != 0 && $qty > 0) {
                                        $variance_class = ($variance > 0) ? 'table-warning' : 'table-danger';
                                        $variance_status = ($variance > 0) ? 'OVER' : 'SHORT';

                                        echo '<tr>';
                                        echo '<td>' . $sku . '</td>';
                                        echo '<td>' . $row['description'] . '</td>';
                                        echo '<td>' . $odoo_total . '</td>';
                                        echo '<td>' . $oms_total . '</td>';
                                        echo '<td class="' . $variance_class . '">' . $variance . ' ' . $row['uom'] . '</td>';
                                        echo '<td class="' . $variance_class . '">' . $variance_status . '</td>';
                                        echo '<td>' . $row['location'] . '</td>';

                                        // Recount Buttons
                                        $recount_base_url = "inventory_validate_assign.php?sku=$sku&description={$row['description']}&location={$row['location']}";

                                        // R1
                                        echo '<td><center><button class="btn btn-sm btn-info" ' .
                                             (($qty > 0 && $qty2 == 0) ? '' : 'disabled') . 
                                             ' onclick="window.open(\'' . $recount_base_url . '&recount=R1\', \'_blank\')">
                                             <i class="fa-solid fa-boxes"></i></button></center></td>';

                                        // R2
                                        echo '<td><center><button class="btn btn-sm btn-info" ' .
                                             (($qty2 > 0 && $qty3 == 0) ? '' : 'disabled') . 
                                             ' onclick="window.open(\'' . $recount_base_url . '&recount=R2\', \'_blank\')">
                                             <i class="fa-solid fa-boxes"></i></button></center></td>';

                                        // R3
                                        echo '<td><center><button class="btn btn-sm btn-info" ' .
                                             (($qty3 > 0 && $qty4 == 0) ? '' : 'disabled') . 
                                             ' onclick="window.open(\'' . $recount_base_url . '&recount=R3\', \'_blank\')">
                                             <i class="fa-solid fa-boxes"></i></button></center></td>';

                                        // R4
                                        echo '<td><center><button class="btn btn-sm btn-info" ' .
                                             ($qty4 > 0 ? '' : 'disabled') . 
                                             ' onclick="window.open(\'' . $recount_base_url . '&recount=R4\', \'_blank\')">
                                             <i class="fa-solid fa-boxes"></i></button></center></td>';

                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

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