<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(79, $permission))
{
include_once("nav_product.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_product");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_product';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
    <form action="product_import.php" method="post" enctype="multipart/form-data">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Product List</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(77, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add SKU</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> SKU has been added successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> SKU list has been imported successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> SKU name / SKU code exists.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> SKU has been updated successfully.';
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
                            <span class="btn btn-primary btn-sm" data-toggle="modal" <?php if(in_array(80, $permission)){ echo 'data-target="#import"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-upload"></i> Upload</span>
                            &nbsp;
                            <span><a onclick="window.location.href='IMPORT_PRODUCT_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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

            <!-- Add Employee Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-plus fa-sm"></i> Add SKU</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="product_add.php" id="ModuleForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-10">
                                            <label>SKU Name:</label>
                                            <input type="text" class="form-control form-control-sm" name="sku" autocomplete="off" required>
                                        </div>
                                        <div class="col-2">
                                            <label>Uom:</label>
                                            <select class="form-control form-control-sm" name="uom" required>
                                                <option></option>
                                                <option value="CS">CS</option>
                                                <option value="IB">IB</option>
                                                <option value="PCK">PCK</option>
                                                <option value="PCS">PCS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Item Code:</label>
                                            <input type="text" class="form-control form-control-sm" name="itemcode" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Item Barcode:</label>
                                            <input type="text" class="form-control form-control-sm" name="itembarcode" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Barcode:</label>
                                            <input type="text" class="form-control form-control-sm" name="barcode" autocomplete="off">
                                        </div>
                                        <div class="col-6">
                                            <label>Vendorcode:</label>
                                            <?php
                                            $vendor_query = "SELECT * FROM tbl_product GROUP BY vendorcode ORDER BY vendorcode ASC";
                                            $vendor_result = $conn->query($vendor_query);
                                            if($vendor_result->num_rows> 0){
                                                $options= mysqli_fetch_all($vendor_result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="vendorcode" required>
                                                <?php    
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['vendorcode'];?>"><?php echo $option['vendorcode']; ?> </option>
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
                                            <label>CS:</label>
                                            <input type="text" class="form-control form-control-sm" name="cs" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>IB/PCK/PCS:</label>
                                            <input type="text" class="form-control form-control-sm" name="ib" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Rack Location:</label>
                                            <input type="text" class="form-control form-control-sm" name="racklocation" autocomplete="off">
                                        </div>
                                        <div class="col-6">
                                            <label>Status:</label>
                                            <select class="form-control form-control-sm" name="status">
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-12">
                                            <label>Principal:</label>
                                            <?php
                                            $query = "SELECT * FROM tbl_product GROUP BY principal ORDER BY principal ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="principal" required>
                                                <?php    
                                                foreach ($options as $option) {
                                                ?>
                                                    <option value="<?php echo $option['principal'];?>"><?php echo $option['principal']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                                </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add SKU</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Product Details</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(81, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>SKU</th>
                                        <th>Description</th>
                                        <th>Uom</th>
                                        <th>Principal</th>
                                        <th>Status</th>
                                        <th width="12%">Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_product");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['itemcode'].'</td>';
                                            echo '<td>'.$row['description'].'</td>';
                                            echo '<td><center>'.$row['uom'].'</center></td>';
                                            echo '<td>'.$row['principal'].'</td>';

                                            if($row['is_active'] == 1){
                                                echo '<td><center><span class="badge badge-success">Active</span></center></td>';
                                            }else{
                                                echo '<td><center><span class="badge badge-danger">Inactive</span></center></td>';
                                            }
                                            ?>
                                            <td><center><a class="d-sm-inline-block btn btn-sm btn-outline-warning" name="update" type="button" <?php if (in_array(78, $permission)) { echo 'href="product_update.php?update_id='.$row['id'].'"'; } else {  echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa fa-cog fa-sm"></i> Update</a></center></td>
                                    <?php   
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