<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(153, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_inventory_rack");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_inventory_rack';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Group Assignment</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa-solid fa-plus"></i> Add Rack</button>
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
        <form action="inventory_assign_import.php" method="post" enctype="multipart/form-data">
        <div class="card shadow mb-4">
            <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                        <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                    <div class="input-group-prepend">
                        <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(154, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                        &nbsp;
                        <span><a onclick="window.location.href='IMPORT_RACK_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-plus"></i> Add SKU Rack</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="inventory_assign_add.php" id="UserForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Rack:</label>
                                            <input type="text" class="form-control form-control-sm" name="rack" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Column:</label>
                                            <input type="text" class="form-control form-control-sm" name="column" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Level:</label>
                                            <input type="text" class="form-control form-control-sm" name="level" autocomplete="off">
                                        </div>
                                        <div class="col-6">
                                            <label>Position:</label>
                                            <select class="form-control form-control-sm" name="position">
                                                <option value=""></option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Location:</label>
                                            <select class="form-control form-control-sm" name="location" required>
                                                <option value="CAINTA">CAINTA</option>
                                                <option value="CDO">CDO</option>
                                                <option value="CEBU">CEBU</option>
                                                <option value="DAVAO">DAVAO</option>
                                                <option value="ILOILO">ILOILO</option>
                                                <option value="PANGASINAN">PANGASINAN</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Group #:</label>
                                            <input type="text" class="form-control form-control-sm" name="groupno" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add SKU Rack</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing User List</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-download"></i> Export Data</a>
                            &nbsp;|&nbsp; 
                            <a type="button" onclick="window.open('inventory_assign_print.php')" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-print"></i> Print Group</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th hidden></th>
                                        <th>Group</th>
                                        <th>Active Racks</th>
                                        <th>Location</th>
                                        <th>Up Date</th>
                                        <th>Counters</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "
                                            SELECT *, COUNT(DISTINCT sku) AS sku_count, COUNT(racklocation) AS rack_count, SUM(inv_status) as total_active
                                            FROM tbl_inventory_rack 
                                            WHERE location IN (" . $location . ")
                                            GROUP BY groupno, location 
                                            ORDER BY CAST(groupno AS UNSIGNED)
                                        ");
                                        while($row = mysqli_fetch_array($result)) {
                                    ?>
                                        <tr>
                                            <?php 
                                                echo '<td hidden>'.$row['groupno'].'</td>';
                                                echo '<td>Group '.$row['groupno'].'</td>';
                                                echo '<td>'.$row['total_active'].' / '.$row['rack_count'].'</td>';
                                                echo '<td>'.$row['location'].'</td>';
                                                echo '<td>'.$row['dtr'].'</td>';

                                                $groupno = $row['groupno'];
                                                $location = $row['location'];
                                                $assign_query = mysqli_query($conn, "SELECT fin_name, log_name FROM tbl_inventory_group WHERE groupno = '$groupno' AND location = '$location'");
                                                $count_assign = mysqli_num_rows($assign_query);
                                                $fetch_assign = mysqli_fetch_assoc($assign_query);

                                                if ($fetch_assign) {
                                                    echo '<td>
                                                        <span class="badge badge-success d-inline">' . $fetch_assign['fin_name'] . '</span>
                                                        <span class="badge badge-success d-inline ms-2">' . $fetch_assign['log_name'] . '</span>
                                                    </td>';
                                                } else {
                                                    echo '<td><span class="badge badge-secondary d-inline"><i>Not Assigned</i></span></td>';
                                                }

                                                if(in_array(152, $permission)){

                                                    if($count_assign > 0){
                                                        echo '<td><a type="button" name="view" class="btn btn-sm btn-warning text-dark" onclick="window.open(\'inventory_assign_view.php?groupno=' . $row['groupno'] . '&location='.$row ['location'].'\', \'_blank\')"><i class="fa-solid fa-users"></i> Update</a></td>';
                                                    }else{
                                                        echo '<td><a type="button" name="view" class="btn btn-sm btn-info" onclick="window.open(\'inventory_assign_view.php?groupno=' . $row['groupno'] . '&location='.$row ['location'].'\', \'_blank\')"><i class="fa-solid fa-users"></i> Assign</a></td>';
                                                    }

                                                }else{
                                                    echo '<td><button class="d-sm-inline-block btn btn-sm btn-info" data-toggle="modal" data-target="#alertModal"><i class="fa-solid fa-users"></i> Assign</button></td>';
                                                }
                                                ?>
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