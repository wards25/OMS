<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

unset($_SESSION['previous_pages']);

// Store the current page URL
$_SESSION['previous_pages'][] = $_SERVER['REQUEST_URI'];

// Keep only the last two pages in the session
if (count($_SESSION['previous_pages']) > 2) {
    array_shift($_SESSION['previous_pages']);
}

if(isset($_SESSION['id']) && in_array(8, $permission))
{
include_once("nav_whse.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_asset_inv");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_asset_inv';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Asset Inventory</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(6, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add Asset</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Asset has been added successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Asset has been imported successfully.';
                    break;
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'incident':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been filed successfully.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Asset has been updated successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Asset ID exists.';
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

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add Asset</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="fireext_add.php" id="AssetForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Asset ID</label>
                                            <input type="text" class="form-control form-control-sm" name="asset_id" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Asset Name</label>
                                            <?php
                                            $query = "SELECT * FROM tbl_asset_inv GROUP BY asset_name ORDER BY asset_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="asset_name" required>
                                                <?php    
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['asset_name'];?>"><?php echo $option['asset_name']; ?></option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Own/Rented:</label>
                                            <select class="form-control form-control-sm" name="rented">
                                                <option value="0">OWN</option>
                                                <option value="1">RENTED</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Condition:</label>
                                            <select class="form-control form-control-sm" name="cond">
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Asset Type:</label>
                                            <select class="form-control form-control-sm" name="type">
                                                <option value="Admin">Admin</option>
                                                <option value="Warehouse">Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Location:</label>
                                            <?php 
                                            $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                            $result = $conn->query($query);

                                            if ($result && $result->num_rows > 0) {
                                                $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                            ?>
                                                <select class="form-control form-control-sm" name="location">
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
                                                ?>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add Asset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
            $update_query = mysqli_query($conn,"SELECT * FROM tbl_asset_inv");
            while($fetch_update = mysqli_fetch_array($update_query)){
            ?>
            <!-- Update Modal -->
            <div class="modal fade" id="update<?php echo $fetch_update['id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <h6 class="modal-title text-warning" id="exampleModalLabel"><i class="fa fa-cog fa-sm"></i> Update Asset</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="fireext_update.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Asset ID:</label>
                                            <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_update['asset_id']?>" name="asset_id" autocomplete="off">
                                        </div>
                                        <div class="col-6">
                                            <label>Asset Name</label>
                                            <?php
                                            $query = "SELECT * FROM tbl_asset_inv GROUP BY asset_name ORDER BY asset_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="asset_name" required>
                                                    <option value="<?php echo $fetch_update['asset_name'];?>"><?php echo $fetch_update['asset_name']; ?> (Existing)</option>
                                                <?php    
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['asset_name'];?>"><?php echo $option['asset_name']; ?></option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Own/Rented:</label>
                                            <select class="form-control form-control-sm" name="rented">
                                                <?php
                                                if($fetch_update['asset_rented'] == 0){
                                                    echo '<option value="0">OWNED (Existing)</option>';
                                                }else{
                                                    echo '<option value="1">RENTED (Existing)</option>';
                                                }
                                                ?>
                                                <option value="0">OWNED</option>
                                                <option value="1">RENTED</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Condition:</label>
                                            <select class="form-control form-control-sm" name="cond">
                                                <?php
                                                if($fetch_update['cond'] == 1){
                                                    echo '<option value="1">ACTIVE (Existing)</option>';
                                                }else{
                                                    echo '<option value="0">INACTIVE (Existing)</option>';
                                                }
                                                ?>
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Condition:</label>
                                            <select class="form-control form-control-sm" name="type">
                                                <option value="<?php echo $fetch_update['asset_type']; ?>"><?php echo $fetch_update['asset_type']; ?> (Existing)</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Warehouse">Warehouse</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Location:</label>
                                            <?php 
                                            $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                            $result = $conn->query($query);

                                            if ($result && $result->num_rows > 0) {
                                                $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                            ?>
                                                <select class="form-control form-control-sm" name="location">
                                                    <option value="<?php echo $fetch_update['location']; ?>"><?php echo $fetch_update['location']; ?> (Existing)</option>
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
                                                ?>
                                                </select>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="text" value="<?php echo $fetch_update['id'];?>" name="update_id" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>

            <!-- DataTales Row -->
            <form action="asset_import.php" method="post" enctype="multipart/form-data">
                <div class="card shadow mb-4">
                    <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                        <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                                <?php 
                                $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                $result = $conn->query($query);

                                if ($result && $result->num_rows > 0) {
                                    $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                ?>
                                    <select class="form-control form-control-sm" name="location">
                                        <option value="">Select Location</option>
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
                                    ?>
                                    </select>
                                <?php
                                }
                                ?>
                                &nbsp;&nbsp;&nbsp;
                                <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                            <div class="input-group-prepend">
                                <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(9, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                                &nbsp;
                                <span><a onclick="window.location.href='IMPORT_ASSET_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing Asset List</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(10, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(16, $permission)){ echo 'href="asset_raw.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-file"></i> Report Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Asset ID</th>
                                        <th>Asset Name</th>
                                        <th>Own/Rented</th>
                                        <th>Condition</th>
                                        <th>Location</th>
                                        <th>Update</th>
                                        <th>Incident Report</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_asset_inv WHERE asset_type = 'Warehouse' AND location IN (" . $location . ")");
                                        while($row = mysqli_fetch_array($result)){

                                            if($row['report'] >= 1){
                                                echo '<tr class="table-danger">';
                                            }else{
                                                echo '<tr>';
                                            }

                                            echo '<td class="align-middle">'.$row['asset_id'].'</td>';
                                            echo '<td class="align-middle">'.$row['asset_name'].'</td>';

                                            if($row['asset_rented'] == 1){
                                                echo '<td class="align-middle"><center>RENTED</center></td>';
                                            }else{
                                                echo '<td class="align-middle"><center>OWNED</center></td>';
                                            }

                                            if($row['cond'] == 1){
                                                echo '<td class="align-middle"><center><span class="badge badge-success">Active</span></center></td>';
                                            }else{
                                                echo '<td class="align-middle"><center><span class="badge badge-danger">Inactive</span></center></td>';
                                            }

                                            echo '<td class="align-middle"><center>'.$row['location'].'</center></td>';
                                            ?>
                                            <td class="align-middle"><center><button type="button" class="d-sm-inline-block btn btn-sm btn-outline-warning" data-toggle="modal" <?php if(in_array(7, $permission)){ echo "data-target=#update".$row['id']; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-cog fa-sm fa-fw"></i> Update</button></td></center>

                                        <?php
                                            $subject = $row['asset_id'];
                                            $location = $row['location'];

                                            $incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE location='$location' AND subject='$subject' AND status=0");
                                            $check_incident = mysqli_num_rows($incident_query);

                                            if($check_incident >= 1){
                                                echo '<td>';
                                                while($fetch_incident = mysqli_fetch_array($incident_query)){
                                                ?>
                                                    <center><a type="button" class="text-dark" <?php if(in_array(19, $permission)){ echo 'href="incident_view.php?ir='.$fetch_incident['ref_no'].'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><small><?php echo $fetch_incident['ref_no']; ?></small></a></center>
                                            <?php
                                                }
                                                echo '</td>';
                                            }else{
                                                echo '<td class="align-middle"><center><small class="text-dark"><i>No Incident Report Filed</i></small></center></td>';
                                            }
                                            ?>
                                            <td class="align-middle"><center><a type="button" class="btn btn-sm btn-danger" <?php if(in_array(17, $permission)){ echo 'href="incident_form.php?location='.$row['location'].'&date='.date("Y-m-d").'&shift=0&shift_type=0&table=tbl_asset_inv&tbl_column=asset_id&subject='.$row['asset_id'].'&tag=LOGISTICS&module_id='.$row['id'].'"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-exclamation-triangle fa-sm"></i> File IR</a></center></td>
                                    </tr>
                                    <?php
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

        // Update Item
        $(document).on('click', '.btn-update', function(){
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "export_update.php",
                data: {id:id},
                success: function() {
                    ExportList1();
                    ExportList2();
                }
            });
        });

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#AssetForm')[0].reset();
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