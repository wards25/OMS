<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && $_SESSION['role'] == 'Admin')
{
include_once("nav_settings.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_permissions");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_permissions';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Permission Map</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i> Add Module</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Module has been added successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Module exists.';
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

            <!-- Add Employee Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add Module</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="module_add.php" id="ModuleForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12">
                                            <label>Module Name</label>
                                            <input type="text" class="form-control form-control-sm" name="modulename" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" class="text-primary bg-light">Access</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-middle"><div class="form-check"><input class="form-check-input" type="checkbox" name="Add" value="1" id="flexCheckDefaultAdd"><label class="form-check-label" for="flexCheckDefaultAdd">Add</label></div></td>
                                                    <td class="align-middle"><div class="form-check"><input class="form-check-input" type="checkbox" name="Edit" value="1" id="flexCheckDefaultEdit"><label class="form-check-label" for="flexCheckDefaultEdit">Edit</label></div></td>
                                                </tr>
                                                <tr>
                                                    <td class="align-middle"><div class="form-check"><input class="form-check-input" type="checkbox" name="View" value="1" id="flexCheckDefaultView"><label class="form-check-label" for="flexCheckDefaultView">View</label></div></td>
                                                    <td class="align-middle"><div class="form-check"><input class="form-check-input" type="checkbox" name="Import" value="1" id="flexCheckDefaultImport"><label class="form-check-label" for="flexCheckDefaultImport">Import</label></div></td>
                                                </tr>
                                                <tr>
                                                    <td class="align-middle"><div class="form-check"><input class="form-check-input" type="checkbox" name="Export" value="1" id="flexCheckDefaultExport"><label class="form-check-label" for="flexCheckDefaultExport">Export</label></div></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add Module</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing Permission List</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Module Name</th>
                                        <th>Access</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_module");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            $module = $row['id'];

                                            echo '<tr>';
                                            echo '<td>'.$row['module'].'</td>';
                                            echo '<td class="d-flex">';

                                            $access_query = mysqli_query($conn,"SELECT * FROM tbl_permissions WHERE module_id = '$module'");
                                            while($fetch_access = mysqli_fetch_array($access_query)){
                                                $badge = $fetch_access['permission_name'];
                                                
                                                if($badge == 'Add'){
                                                    echo '<h6><span class="badge badge-success">'.$fetch_access['id'].': Add</span></h6>&nbsp;';
                                                }else if($badge == 'Edit'){
                                                    echo '<h6><span class="badge badge-warning">'.$fetch_access['id'].': Edit</span></h6>&nbsp;';
                                                }else if($badge == 'View'){
                                                    echo '<h6><span class="badge badge-primary">'.$fetch_access['id'].': View</span></h6>&nbsp;';
                                                }else if($badge == 'Import'){
                                                    echo '<h6><span class="badge badge-info">'.$fetch_access['id'].': Import</span></h6>&nbsp;';
                                                }else if($badge == 'Export'){
                                                    echo '<h6><span class="badge badge-danger">'.$fetch_access['id'].': Export</span></h6>&nbsp;';
                                                }else{

                                                }
                                            } 
                                            echo '</td>';
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
            $('#ModuleForm')[0].reset();
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