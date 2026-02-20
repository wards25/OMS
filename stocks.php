<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(158, $permission))
{
include_once("nav_stocks.php");
include_once("export_modal.php");

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
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Stock Summary</h4>
            <!-- <a type="button" class="d-sm-inline-block btn btn-sm btn-success shadow-sm" <?php if(in_array(160, $permission)){ echo 'href="stocks_export.php?location='.$location.'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a> -->
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
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Rack has been updated successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'assign':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Counters has been assigned successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Rack location exists.';
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
                <form action="stocks_import.php" method="post" enctype="multipart/form-data">
                    <div class="card shadow mb-4">
                        <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                            <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                            <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                                <div class="input-group-prepend">
                                    <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(159, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                                    &nbsp;
                                    <span><a onclick="window.location.href='IMPORT_STOCKS_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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
                                        <input type="text" value="Warehouse" name="asset_type" hidden>
                                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                        <input type="submit" class="btn btn-success btn-sm" name="submit" value="Upload">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </body>
                </form>

                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Stock Summary as of | <?php echo date("F d, Y"); ?></h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="input-group mr-3" style="width:40%;">
                                <label class="mr-2">Filter by Location:</label>
                                <form method="GET" action="">
                                    <select id="locationFilter" name="location" class="form-control form-control-sm" onchange="this.form.submit()">
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
                            <a type="button" class="btn btn-sm btn-light ml-auto" <?php if(in_array(160, $permission)){ echo 'href="stocks_export.php?location='.$location.'&type=6months"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa-solid fa-6"></i> Months Report</a>&nbsp;|&nbsp;
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(160, $permission)){ echo 'href="stocks_export.php?location='.$location.'&type=3months"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa-solid fa-3"></i> Months Report</a>&nbsp;|&nbsp;
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(160, $permission)){ echo 'href="stocks_export.php?location='.$location.'&type=raw"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>SKU</th>
                                        <th>Total</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stock_query = mysqli_query($conn, "SELECT *,SUM(qty) as total_qty FROM tbl_inventory_stocks WHERE location = '$location' GROUP BY sku ORDER BY description ASC");

                                    if (mysqli_num_rows($stock_query) > 0) {
                                        while($fetch_stock = mysqli_fetch_assoc($stock_query)){
                                            echo '<tr>';
                                            echo '<td>'.$fetch_stock['sku'].' - '.$fetch_stock['description'].'</td>';
                                            echo '<td>'.$fetch_stock['total_qty'].' '.$fetch_stock['uom'].'</td>';
                                            echo '<td><a class="btn btn-sm btn-info" href="stocks_view.php?location=' . $location . '&sku=' . $fetch_stock['sku'] . '&description=' . $fetch_stock['description'] . '"><i class="fa-solid fa-eye"></i></a></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr>';
                                        echo '<td colspan="3">No stocks found for this location.</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                    </tr>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Reset add modal button
        $('.add-btn').click(function(){
            $('#UserForm')[0].reset();
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