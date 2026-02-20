<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(152, $permission))
{
include_once("nav_inventory.php");

$groupno = $_GET['groupno'];
$location = $_GET['location'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">
                Inventory Group # 
                <?php 
                echo $groupno;
                ?>
            </h4>
            <button class="input-group-addon btn btn-secondary btn-sm" onclick='window.close()'><i class="fa fa-sm fa-times"></i> Close</button>
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
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Rack Summary</h6> 
                    </div>
                    <div class="card-body">
                        <form method="POST" action="inventory_assign_submit.php">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                            <?php
                                            $details_query = mysqli_query($conn, "SELECT *,count(DISTINCT sku) as sku_count FROM tbl_inventory_rack WHERE groupno = '$groupno' AND location = '$location'");
                                            $fetch_details = mysqli_fetch_assoc($details_query);

                                            $group_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_group WHERE groupno = '$groupno' AND location = '$location'");
                                            $fetch_group = mysqli_fetch_assoc($group_query);
                                            ?>
                                            <tbody>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Group #:</td>
                                                    <td><?php echo $groupno; ?></td>
                                                    <td style="background: #f2f2f2;"><b>Location:</td>
                                                    <td><?php echo $location; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Assigned Fin Counter:</td>
                                                    <td><?php echo !empty($fetch_group['fin_name']) ? $fetch_group['fin_name'] : '<i>Not Assigned</i>'; ?></td>
                                                    <td style="background: #f2f2f2;"><b>Assigned Log Counter:</td>
                                                    <td><?php echo !empty($fetch_group['log_name']) ? $fetch_group['log_name'] : '<i>Not Assigned</i>'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Update Fin:</td>
                                                    <td>
                                                    <?php 
                                                    // if($fetch_details['fin'] == 1){
                                                    //     echo $fetch_details['fin_name'];
                                                    // }else{
                                                        $fin_query = "SELECT * FROM tbl_users WHERE tag = 'FINANCE' AND is_active = '1' AND hub = '$location' ORDER BY fname";
                                                        $fin_result = $conn->query($fin_query);
                                                        if($fin_result->num_rows> 0){
                                                            $options= mysqli_fetch_all($fin_result, MYSQLI_ASSOC);?>
                                                         
                                                        <select class="form-control form-control-sm" name="fin" id="search_fin" style="width:100%;" required>
                                                            <!-- <option value=""></option> -->
                                                        <?php 
                                                            foreach ($options as $option) {
                                                        ?>
                                                            <option value="<?php echo $option['id'];?>"><?php echo $option['fname'].' '.$option['lname']; ?> </option>
                                                        <?php 
                                                            }
                                                        }
                                                        echo '</select>';
                                                    // }
                                                    ?>
                                                    </td>
                                                    <td style="background: #f2f2f2;"><b>Update Log:</td>
                                                    <td>
                                                    <?php
                                                    // if($fetch_details['log'] == 1){
                                                    //     echo $fetch_details['log_name'];
                                                    // }else{ 
                                                        $log_query = "SELECT * FROM tbl_users WHERE tag = 'LOGISTICS' AND is_active = '1' AND hub = '$location' ORDER BY fname";
                                                        $log_result = $conn->query($log_query);
                                                        if($log_result->num_rows> 0){
                                                            $options= mysqli_fetch_all($log_result, MYSQLI_ASSOC);?>
                                                         
                                                        <select class="form-control form-control-sm" name="log" id="search_log" style="width:100%;" required>
                                                            <!-- <option value=""></option> -->
                                                        <?php 
                                                            foreach ($options as $option) {
                                                        ?>
                                                            <option value="<?php echo $option['id'];?>"><?php echo $option['fname'].' '.$option['lname']; ?> </option>
                                                        <?php 
                                                            }
                                                        }
                                                        echo '</select>';
                                                    // }
                                                    ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    $assign_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_group WHERE groupno = '$groupno' AND location = '$location'");
                                    $count_assign = mysqli_num_rows($assign_query);

                                    echo '<center>';
                                    if($count_assign > 0){
                                        echo '<button type="button" class="d-sm-inline-block btn btn-sm btn-warning text-dark" data-toggle="modal" data-target="#submitModal"><i class="fa fa-users"></i> Update Counters</button></center>';
                                    }else{
                                        echo '<button type="button" class="d-sm-inline-block btn btn-sm btn-info" data-toggle="modal" data-target="#submitModal"><i class="fa fa-users"></i> Assign Counters</button></center>';
                                    }
                                    ?>

                                    <!-- Submit Modal-->
                                    <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">                                                                                         
                                                <div class="modal-header bg-info">
                                                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-users"></i> Assign Counters</h6>
                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true"><small>×</small></span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Do you want to assign the following counters?
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="text" name="groupno" value="<?php echo $groupno; ?>" hidden>
                                                    <input type="text" name="location" value="<?php echo $location; ?>" hidden>
                                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                    <button class="btn btn-info btn-sm" id="submit-picking-btn" name="submit">Assign</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th hidden>Sort</th>
                                        <th>Rack</th>
                                        <th>Expected SKU</th>
                                        <th>Company</th>
                                        <th>Rack Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM tbl_inventory_rack WHERE groupno = '$groupno' AND location = '$location'");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Parse sort parts from racklocation
                                    $rack_parts = explode('-', $row['racklocation']);
                                    $rack_num = intval(substr($rack_parts[0], 1)); // R1 → 1
                                    $col_num = intval(substr($rack_parts[1], 1));  // C10A → 10 (assumes always starts with C)
                                    $level_num = intval(substr($rack_parts[2], 1)); // L1 → 1

                                    // Combine into sortable format with padding
                                    $sort_key = sprintf('%03d-%03d-%03d', $rack_num, $col_num, $level_num);

                                    echo '<tr>';
                                    echo '<td style="display:none;">' . $sort_key . '</td>'; // HIDDEN COLUMN
                                    echo '<td><center>' . $row['racklocation'] . '</center></td>';

                                    $sku = $row['sku'];
                                    $sku_query = mysqli_query($conn, "SELECT itemcode, description FROM tbl_product WHERE itemcode = '$sku'");
                                    $fetch_sku = mysqli_fetch_assoc($sku_query);

                                    if ($fetch_sku) {
                                        echo '<td><center>' . $fetch_sku['itemcode'] . ' - ' . $fetch_sku['description'] . '</center></td>';
                                    } else {
                                        echo '<td><center>' . $sku . '</center></td>';
                                    }

                                    echo '<td><center>' . $row['company'] . '</center></td>';

                                    echo '<td><center>';
                                    if ($row['inv_status'] == '0') {
                                        echo '<span class="badge badge-danger d-inline">INACTIVE</span>';
                                    } else {
                                        echo '<span class="badge badge-success d-inline">ACTIVE</span>';
                                    }
                                    echo '</center></td>';

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