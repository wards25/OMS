<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(153, $permission))
{
include_once("nav_inventory.php");

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
            <h4 class="mb-0 text-gray-800">
                Inventory Group Table
            </h4>
            <button class="input-group-addon btn btn-secondary btn-sm" onclick="history.back()"><i class="fa fa-sm fa-arrow-left"></i> Back</button>
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
                case 'add':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Fin/Log has been assigned successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Trip number exists.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> PO has been updated successfully.';
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
                <form method="GET" action="">
                    <div class="form-group">
                        <div class="form-row">
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

                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Group Summary</h6> 
                    </div>
                    <div class="card-body">
                    </div>

                    <!-- DataTales Example -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th hidden>Sort</th>
                                        <th>Rack</th>
                                        <th>Group #</th>
                                        <th>Counters</th>
                                        <th>Rack Status</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM tbl_inventory_rack WHERE location = '$location' GROUP BY rack,col,level");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Parse sort parts from racklocation
                                    $rackFormatted = !empty($row['rack']) ? 'R' . $row['rack'] : '';
                                    $columnFormatted = !empty($row['col']) ? 'C' . $row['col'] : '';
                                    $levelFormatted = !empty($row['level']) ? 'L' . $row['level'] : '';

                                    $parts = array_filter([$rackFormatted, $columnFormatted, $levelFormatted]); // Remove empty values
                                    $racklocation = implode('-', $parts);

                                    $rack_parts = explode('-', $racklocation);
                                    $rack_num = intval(substr($rack_parts[0], 1)); // R1 → 1
                                    $col_num = intval(substr($rack_parts[1], 1));  // C10A → 10 (assumes always starts with C)
                                    $level_num = intval(substr($rack_parts[2], 1)); // L1 → 1

                                    // Combine into sortable format with padding
                                    $sort_key = sprintf('%03d-%03d-%03d', $rack_num, $col_num, $level_num);

                                    echo '<tr>';
                                    echo '<td style="display:none;">' . $sort_key . '</td>'; // HIDDEN COLUMN
                                    echo '<td><center>' . $racklocation . '</center></td>';
                                    echo '<td><center>Group ' . $row['groupno'] . '</center></td>';

                                    $groupno = $row['groupno'];
                                    $group_query = mysqli_query($conn, "SELECT fin_name,log_name FROM tbl_inventory_group WHERE groupno = '$groupno'");
                                    $fetch_group = mysqli_fetch_assoc($group_query);

                                    if ($fetch_group) {
                                        echo '<td>
                                            <span class="badge badge-success d-inline">' . $fetch_group['fin_name'] . '</span>
                                            <span class="badge badge-success d-inline ms-2">' . $fetch_group['log_name'] . '</span>
                                        </td>';
                                    } else {
                                        echo '<td><span class="badge badge-secondary d-inline"><i>Not Assigned</i></span></td>';
                                    }

                                    echo '<td><center>';
                                    if ($row['inv_status'] == '0') {
                                        echo '<span class="badge badge-danger d-inline">INACTIVE</span>';
                                    } else {
                                        echo '<span class="badge badge-success d-inline">ACTIVE</span>';
                                    }
                                    echo '</center></td>';

                                    echo '<td><center>' . $row['location'] . '</center></td>';
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
        // Reset add modal button
        $('.assign-btn').click(function(){
            $('#AssignForm')[0].reset();
        });

        $('#search_fin,#search_log').select2({
            theme: "bootstrap"
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