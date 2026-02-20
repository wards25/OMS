<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(138, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

$company = isset($_GET['company']) && !empty($_GET['company']) ? $_GET['company'] : 'ALL COMPANIES'; //set default value
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Inventory Summary</h4>
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

        <!-- DataTales Row -->
        <form action="inventory_import.php" method="post" enctype="multipart/form-data">
            <div class="card shadow mb-4">
                <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                    <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                    <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                            <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                        <div class="input-group-prepend">
                            <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(139, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                            &nbsp;
                            <span><a onclick="window.location.href='IMPORT_ENDING_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Upload Modal-->
                <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-upload fa-sm"></i> Upload File</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">Are you sure you want to upload this csv file?</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-success btn-sm" name="submit" value="Upload">
                            </div>
                        </div>
                    </div>
                </div>
            </body>
        </form>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing User List</h6> 
                    </div> -->
                    <div class="card-body">
                        <form method="GET" action="">
                        <div class="d-flex align-items-center">
                            <div class="input-group mr-3" style="width:40%;">
                                <label class="mr-2">Filter by Company:</label>
                                <?php
                                $query = "SELECT company FROM tbl_inventory_ending GROUP BY company ORDER BY company";
                                $result = $conn->query($query);
                                if($result->num_rows> 0){
                                    $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                    <select class="form-control form-control-sm" name="company" onchange="this.form.submit()">
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
                            <a type="button" class="btn btn-sm btn-light ml-auto" <?php if(in_array(140, $permission)){ echo 'href="inventory_export.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        </form>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th class="table-success align-middle" rowspan="2">SKU</th>
                                        <th class="table-success align-middle" rowspan="2">Description</th>
                                        <th class="bg-success text-white" colspan="2">ACTIVE</th>
                                        <th class="bg-warning text-white" colspan="2">HOLD</th>
                                        <th class="bg-primary text-white" colspan="2">TOTAL</th>
                                        <th class="bg-danger text-white align-middle" rowspan="2">Variance</th>
                                        <th class="table-success align-middle" rowspan="2">UOM</th>
                                        <th class="table-success align-middle" rowspan="2">Location</th>
                                        <th class="table-success align-middle" rowspan="2">Summary</th>
                                    </tr>
                                    <tr class="table-success">
                                        <th>Odoo</th>
                                        <th>OMS</th>
                                        <th>Odoo</th>
                                        <th>OMS</th>
                                        <th>Odoo</th>
                                        <th>OMS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    // Step 1: Fetch all Odoo data
                                    $query = "
                                        SELECT 
                                            sku, 
                                            description, 
                                            SUM(active) AS active_odoo, 
                                            SUM(hold) AS hold_odoo,
                                            uom,
                                            location
                                        FROM tbl_inventory_ending
                                    ";

                                    // Add WHERE clause only if a specific company is selected
                                    if ($company !== 'ALL COMPANIES') {
                                        $company_safe = mysqli_real_escape_string($conn, $company); // prevent SQL injection
                                        $query .= " WHERE company = '$company_safe'";
                                    }

                                    // Add GROUP BY clause
                                    $query .= " GROUP BY sku, description, uom, location";

                                    $result = mysqli_query($conn, $query);

                                    // Step 2: Preload all OMS counts into a map (active & hold per SKU)
                                    $oms_counts = [];
                                    $oms_query = "
                                        SELECT sku, status, SUM(qty) AS qty
                                        FROM (
                                            SELECT sku, racklocation, status, qty
                                            FROM tbl_inventory_count
                                            WHERE count_status = 'MATCH'
                                            GROUP BY sku, racklocation, status
                                        ) AS grouped_counts
                                        GROUP BY sku, status
                                    ";
                                    $oms_result = mysqli_query($conn, $oms_query);

                                    while ($row = mysqli_fetch_assoc($oms_result)) {
                                        $sku = $row['sku'];
                                        $status = $row['status']; // 'ACTIVE' or 'HOLD'
                                        $qty = $row['qty'];

                                        if (!isset($oms_counts[$sku])) {
                                            $oms_counts[$sku] = ['ACTIVE' => 0, 'HOLD' => 0];
                                        }

                                        $oms_counts[$sku][$status] = $qty;
                                    }

                                    // Step 3: Loop through Odoo records and compute variances
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $sku = $row['sku'];
                                        $description = $row['description'];
                                        $active_odoo = $row['active_odoo'];
                                        $hold_odoo = $row['hold_odoo'];
                                        $odoo_total = $active_odoo + $hold_odoo;

                                        $active_oms = $oms_counts[$sku]['ACTIVE'] ?? 0;
                                        $hold_oms = $oms_counts[$sku]['HOLD'] ?? 0;
                                        $oms_total = $active_oms + $hold_oms;
                                        $variance = $oms_total - $odoo_total;

                                        $match_class = ($odoo_total == $oms_total) ? 'table-success' : 'table-warning';
                                        $variance_class = ($variance != 0) ? 'table-danger' : '';

                                        echo '<tr>';
                                        echo '<td>' . $sku . '</td>';
                                        echo '<td>' . $description . '</td>';
                                        echo '<td>' . round($active_odoo, 2) . '</td>';
                                        echo '<td>' . round($active_oms, 2) . '</td>';
                                        echo '<td>' . round($hold_odoo, 2) . '</td>';
                                        echo '<td>' . round($hold_oms, 2) . '</td>';
                                        echo "<td class=\"$match_class\"><b>" . round($odoo_total, 2) . "</b></td>";
                                        echo "<td class=\"$match_class\"><b>" . round($oms_total, 2) . "</b></td>";
                                        echo "<td class=\"$variance_class\"><b>" . round($variance, 2) . "</b></td>";
                                        echo '<td>' . $row['uom'] . '</td>';
                                        echo '<td>' . $row['location'] . '</td>';
                                        echo '
                                            <td>
                                                <center>
                                                    <a type="button" name="view" class="btn btn-sm btn-info" 
                                                       onclick="window.open(\'inventory_view.php?sku=' . $sku . '&description=' . $description . '&location=' . $row['location'] . '\', \'_blank\')">
                                                       <i class="fa-solid fa-boxes"></i>
                                                    </a>
                                                </center>
                                            </td>';
                                        echo '</tr>';
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

    <script>
        // Export List 1
        function ExportList1() {
            $.ajax({
                type: "post",
                url: "export_list1.php",
                success: function(data) {
                    $('#export-list1').html(data);
                }
            });
        }
        ExportList1();   

        // Export List 2
        function ExportList2() {
            $.ajax({
                type: "post",
                url: "export_list2.php",
                success: function(data) {
                    $('#export-list2').html(data);
                }
            });
        }
        ExportList2(); 

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#UserForm')[0].reset();
        });

        $('#search_sku').select2({
            theme: "bootstrap",
            dropdownParent: $("#addModal")
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