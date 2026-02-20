<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

mysqli_query($conn,"DELETE FROM tbl_variance_list WHERE user = '$user'");

//get data from checklist
$type = $_GET['type'];

if($type == 'PVF'){
    $array_view = in_array(87, $permission);
}else if($type == 'RVF'){
    $array_view = in_array(91, $permission);
}else if($type == 'LVF'){
    $array_view = in_array(95, $permission);
}else{
    $array_view = in_array(161, $permission);
}

if(isset($_SESSION['id']) && $array_view)
{
include_once("nav_forms.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Add Form</h4>
            <a type="button" href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
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
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Invoice number exists.';
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
                        <h6 class="m-0 font-weight-bold text-light"><?php if($type == 'PVF'){ echo 'Picking Variance Form'; }else if($type == 'RVF'){ echo 'Redel Variance Form'; }else if($type == 'LVF'){ echo 'Loading Variance Form'; }else{ echo 'Sorting Variance Form'; } ?></h6> 
                    </div>
                    <div class="card-body">
                        <form id="ItemForm">
                        <div class="table-responsive">
                            <div id="alert3"></div>
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-warning">
                                        <th>Error Type</th>
                                        <th>Invoiced Sku</th>
                                        <th>Invoiced Qty</th>
                                        <th>Actual Sku</th>
                                        <th>Actual Qty</th>
                                        <th>Uom</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control form-control-sm" name="error_type" id="error_type" required>
                                                <option value=""></option>
                                                <?php
                                                if($type == 'PVF'){
                                                    echo '<option value="Checker Error">Checker Error</option>';
                                                    echo '<option value="Over Picked">Over Picked</option>';
                                                    echo '<option value="Short Picked">Short Picked</option>';
                                                    echo '<option value="Not In Invoice">Not In Invoice</option>';
                                                }else{
                                                    echo '<option value="Overlanded">Overlanded</option>';
                                                    echo '<option value="Shortlanded">Shortlanded</option>';
                                                    echo '<option value="Not In Invoice">Not In Invoice</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php 
                                            $query = "SELECT * FROM tbl_product";
                                            $result = $conn->query($query);
                                            if ($result->num_rows > 0) {
                                                $options = mysqli_fetch_all($result, MYSQLI_ASSOC); ?>
                                                <select class="form-control form-control-sm" name="invoiced_sku" id="search_invoiced" required onchange="updateUOM()">
                                                    <option value=""></option>
                                                    <?php 
                                                    foreach ($options as $option) {
                                                        ?>
                                                        <option value="<?php echo $option['itemcode']; ?>" data-uom="<?php echo $option['uom']; ?>">
                                                            <?php echo $option['itemcode'].' - '.$option['description']; ?>
                                                        </option>
                                                        <?php 
                                                    }
                                                }
                                                ?>
                                                </select>
                                        </td>
                                        <td><input type="number" class="form-control form-control-sm" name="qty1" id="invoiced_qty" required></td>
                                        <td>
                                            <select class="form-control form-control-sm" name="picked_sku" id="search_picked" required>
                                                <option value=""></option>
                                                <?php 
                                                foreach ($options as $option) {
                                                    ?>
                                                    <option value="<?php echo $option['itemcode']; ?>">
                                                        <?php echo $option['itemcode'].' - '.$option['description']; ?>
                                                    </option>
                                                    <?php 
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control form-control-sm" name="qty2" id="picked_qty" required></td>
                                        <td class="text-center align-middle" id="display-uom"></td>
                                        <td colspan="2"><center><button type="submit" id="add" name="add" class="btn btn-sm btn-success item-add"><i class="fa fa-plus"></i></button></center></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success text-center">
                                        <th>Error Type</th>
                                        <th>Invoiced Sku</th>
                                        <th>Invoiced Qty</th>
                                        <th>Actual Sku</th>
                                        <th>Actual Qty</th>
                                        <th>Uom</th>
                                        <th>Picked Qty</th>
                                        <th>Return Qty</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="item-list">
                                </tbody>
                            </table>
                        </div>
                        </div>
                        <hr>
                        <form method="POST" action="variance_submit.php">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <label>PO No:</label>
                                    <input type="text" class="form-control form-control-sm" name="po_no" placeholder="Enter PO No" autocomplete="off" required>
                                </div>
                                <div class="col-6">
                                    <label>Invoice No:</label>
                                    <input type="number" class="form-control form-control-sm" name="invoice_no" placeholder="Enter Invoice No" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <label>Picker Name:</label>
                                    <?php 
                                    $query = "SELECT * FROM tbl_employees WHERE location IN (" . $location . ") AND (position = 'stockman' OR position = 'utility picker' OR position = 'puller')";
                                    $result = $conn->query($query);
                                    if($result->num_rows> 0){
                                        $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
                                     
                                    <select class="form-control form-control-sm" name="picker" id="search_picker" required>
                                        <option value=""></option>
                                    <?php 
                                        foreach ($options as $option) {
                                    ?>
                                        <option value="<?php echo $option['employee_name'];?>"><?php echo $option['employee_name']; ?> </option>
                                    <?php 
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>Checker Name:</label>
                                    <?php 
                                    $query = "SELECT * FROM tbl_employees WHERE location IN (" . $location . ") AND (position = 'ib checker' OR position = 'ob checker' OR position = 'custodian')";
                                    $result = $conn->query($query);
                                    if($result->num_rows> 0){
                                        $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
                                     
                                    <select class="form-control form-control-sm" name="checker" id="search_checker" required>
                                        <option value=""></option>
                                    <?php 
                                        foreach ($options as $option) {
                                    ?>
                                        <option value="<?php echo $option['employee_name'];?>"><?php echo $option['employee_name']; ?> </option>
                                    <?php 
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php
                        if($type == 'PVF' || $type == 'LVF' || $type == 'SVF'){

                        }else{
                        ?>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <label>Original Driver:</label>
                                    <input type="text" class="form-control form-control-sm" name="driver" required>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>Original Helper:</label>
                                    <input type="text" class="form-control form-control-sm" name="helper" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <label>New Driver:</label>
                                    <input type="text" class="form-control form-control-sm" name="newdriver" required>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>New Helper:</label>
                                    <input type="text" class="form-control form-control-sm" name="newhelper" required>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12">
                                    <label>Location:</label>
                                    <?php 
                                    $query = "SELECT * FROM tbl_user_locations WHERE user_id = " . intval($_SESSION['id']);
                                    $result = $conn->query($query);

                                    if ($result && $result->num_rows > 0) {
                                        $options = mysqli_fetch_all($result, MYSQLI_ASSOC);
                                    ?>
                                        <select class="form-control form-control-sm" name="location" required>
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
                                </div>
                            </div>
                        </div>
                        <hr>
                            <center>
                                <input type="text" value="<?php echo $_GET['type']; ?>" name="type" hidden>
                                <a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-check"></i> Submit</a>
                            </center>

                            <!-- Submit Modal -->
                            <div class="modal fade" id="SubmitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success">
                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check fa-sm"></i> Submit Form</h6>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><small>×</small></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Do you want to submit this form?</div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit modal end -->
                        </center>
                        </form>
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
        // submit item
        $('#ItemForm').submit(function(e) {
            e.preventDefault();
            var item = $('#ItemForm').serialize();
            $.ajax({
                type: "post",
                url: "variance_add.php",
                data: item,
                success: function(data) {
                    // Reset the form fields
                    $('#ItemForm')[0].reset();
                    
                    // Reset the Select2 dropdowns
                    $('#search_invoiced').val(null).trigger('change');  // Reset invoiced_sku
                    $('#search_picked').val(null).trigger('change');     // Reset picked_sku
                    
                    // Reset the UOM display
                    $('#display-uom').text('');

                    if (data == '') {
                        ItemList();
                    } else {
                        $('#alert3').show();
                        $('#alert3').html(data);
                        window.setTimeout(function() {
                            $(".alert3").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove();
                            });
                        }, 2000);
                    }
                }
            });
        });

        document.getElementById('error_type').addEventListener('change', function() {
            var invoicedSku = document.getElementById('search_invoiced');
            var invoicedQty = document.getElementById('invoiced_qty');
            var pickedSku = document.getElementById('search_picked');
            var pickedQty = document.getElementById('picked_qty');
            if (this.value === 'Short Picked' || this.value === 'Over Picked' || this.value === 'Shortlanded' || this.value === 'Overlanded') {
                invoicedSku.disabled = false;
                invoicedQty.disabled = false;
                pickedSku.disabled = true;
                pickedSku.value = "";
                pickedQty.value = "";
            } else if(this.value === 'Not In Invoice'){
                invoicedSku.disabled = true;
                invoicedSku.value = "";
                invoicedQty.disabled = true;
                invoicedQty.value = "";
                pickedSku.disabled = false;
                pickedQty.value = "";
            }else{ 
                invoicedSku.disabled = false;
                pickedSku.disabled = false;
            }
        });

        function updateUOM() {
            var selectElement = document.getElementById('search_invoiced');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var uom = selectedOption.getAttribute('data-uom') || ''; // Get the UOM or set it to empty if none
            document.getElementById('display-uom').innerText = uom; // Display the UOM
        }

        // item list
        function ItemList() {
            $.ajax({
                type: "post",
                url: "variance_list.php",
                success: function(data) {
                    $('#item-list').html(data);
                }
            });
        }
        ItemList();

        // delete item
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "variance_delete.php",
                data: {id:id},
                success: function() {
                    ItemList();
                }
            });
        });

        // Reset add modal button
        $('#search_picker,#search_checker,#search_driver,#search_helper,#search_invoiced,#search_picked').select2({
            theme: "bootstrap"
        });
    </script>

    <!-- Bootstrap core JavaScript-->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
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