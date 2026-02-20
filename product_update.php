<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(78, $permission))
{
include_once("nav_product.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Update Product</h4>
            <?php
            if(isset($_GET['type'])){
            ?>
            <a type="button" href="product_scan.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
            <?php
            }else{
            ?>
            <a type="button" href="product.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
            <?php
            }
            ?>
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


                <!-- DataTales Example -->
                <form method="POST" action="product_update_process.php">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2 bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">Product Details</h6> 
                        </div>
                        <div class="card-body">
                            <?php
                            $update_id = $_GET['update_id'];
                            $update_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE id = '$update_id'");
                            $fetch_update = mysqli_fetch_array($update_query);
                            ?>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-9">
                                        <label>SKU Name:</label>
                                        <input type="text" class="form-control form-control-sm" name="sku" value="<?php echo $fetch_update['description']; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="col-3">
                                        <label>Uom:</label>
                                        <select class="form-control form-control-sm" name="uom">
                                            <option value="<?php echo $fetch_update['uom']; ?>"><?php echo $fetch_update['uom']; ?></option>
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
                                        <input type="text" class="form-control form-control-sm" name="itemcode" value="<?php echo $fetch_update['itemcode']; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="col-6">
                                        <label>Item Barcode:</label>
                                        <input type="text" class="form-control form-control-sm" name="itembarcode" value="<?php echo $fetch_update['itembarcode']; ?>" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-6">
                                        <label>Barcode:</label>
                                        <input type="text" class="form-control form-control-sm" name="barcode" value="<?php echo $fetch_update['barcode']; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="col-6">
                                        <label>Vendorcode:</label>
                                        <?php
                                        $query = "SELECT * FROM tbl_product GROUP BY vendorcode ORDER BY vendorcode ASC";
                                        $result = $conn->query($query);
                                        if($result->num_rows> 0){
                                            $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                            <select class="form-control form-control-sm" name="vendorcode" required>
                                                <option value="<?php echo $fetch_update['vendorcode']; ?>"><?php echo $fetch_update['vendorcode']; ?> (Existing)</option>
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
                                        <input type="text" class="form-control form-control-sm" name="cs" value="<?php echo $fetch_update['percase']; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="col-6">
                                        <label>IB/PCK/PCS:</label>
                                        <input type="text" class="form-control form-control-sm" name="ib" value="<?php echo $fetch_update['perserving']; ?>" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-6">
                                        <label>Rack Location:</label>
                                        <input type="text" class="form-control form-control-sm" name="racklocation" value="<?php echo $fetch_update['racklocation']; ?>" autocomplete="off">
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
                                                <option value="<?php echo $fetch_update['principal']; ?>"><?php echo $fetch_update['principal']; ?> (Existing)</option>
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
                            <hr>
                                <input type="text" name="update_id" value=<?php echo $update_id; ?> hidden>
                                <center><button class="btn btn-success btn-sm" name="update" type="submit">Update</button>
                        </div>
                    </div>
                </form>
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