<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(3, $permission))
{
include_once("nav_settings.php");
include_once("export_modal.php");

// delete tbl_export in db 
mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

$export_query = mysqli_query($conn,"DESCRIBE tbl_search");
while($fetch_export = mysqli_fetch_array($export_query)) {
    $col_name = $fetch_export['Field'];
    $tbl_name = 'tbl_search';
    mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
}
    $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
    <form action="search_import.php" method="post" enctype="multipart/form-data">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Search Settings</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(1, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa-solid fa-user-plus"></i> Add Employee</button> -->
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'invalid_file':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-circle fa-sm"></i>&nbsp;<b>Error!</b> Please upload a valid CSV file.';
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
        <div class="card shadow mb-4">
            <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                    <div class="input-group-prepend">
                        <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(4, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                        &nbsp;
                        <span><a onclick="window.location.href='SEARCH_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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
                    <!-- <div class="card-header py-2">
                        <h6 class="m-0 font-weight-bold text-light">Existing Employee List</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <a type="button" class="btn btn-sm btn-light ml-auto" data-toggle="modal" <?php if(in_array(5, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Module</th>
                                        <th>Sub1</th>
                                        <th>Sub2</th>
                                        <th>Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT * FROM tbl_search ORDER BY module");
                                        while ($row = mysqli_fetch_array($result)) {
                                        ?>
                                            <tr>
                                                <?php 
                                                echo '<td>' . $row['module'] . '</td>';
                                                echo '<td>' . $row['sub1'] . '</td>';
                                                echo '<td>' . $row['sub2'] . '</td>';
                                                echo '<td>' . $row['link'] . '</td>';
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

        function filterByShift() {
            var shift = document.getElementById('shiftFilter').value;
            window.location.href = window.location.pathname + '?shift=' + shift;
        }

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#EmployeeForm')[0].reset();
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