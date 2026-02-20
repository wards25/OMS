<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$picklistno = $_GET['picklistno'];
$dtr = $_GET['dtr'];

$validation_query = mysqli_query($conn,"SELECT * FROM tbl_trips_picklist WHERE picklistno = '$picklistno' AND dtr = '$dtr' AND validator_dtr IS NULL");
$count_validation = mysqli_num_rows($validation_query);

if(isset($_SESSION['id']) && in_array(101, $permission))
{
include_once("nav_trips.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Validate: 
                <?php 
                $sku_query = mysqli_query($conn,"SELECT count(sku) FROM tbl_trips_picking WHERE picklistno = '$picklistno'");
                $fetch_sku = mysqli_fetch_assoc($sku_query);
                echo $picklistno;
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Picker/Checker has been assigned successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Trips has been imported successfully.';
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
                        <h6 class="m-0 font-weight-bold text-light">Picklist Summary</h6> 
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                            <?php
                                            $details_query = mysqli_query($conn, "SELECT * FROM tbl_trips_picklist WHERE picklistno = '$picklistno' AND dtr = '$dtr'");
                                            $fetch_details = mysqli_fetch_assoc($details_query);
                                            ?>
                                            <thead>
                                                <tr class="table-info">
                                                    <th colspan="4">Picker Summary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Name:</td>
                                                    <td colspan="3"><?php if(empty($fetch_details['picker'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['picker']; } ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Start:</td>
                                                    <td><?php echo $fetch_details['picker_start'];?></td>
                                                    <td style="background: #f2f2f2;"><b>End:</td>
                                                    <td><?php echo $fetch_details['picker_end'];?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Duration:</b></td>
                                                    <td colspan="3">
                                                        <?php
                                                        // Convert picker_start and picker_end to DateTime objects
                                                        $start = new DateTime($fetch_details['picker_start']);
                                                        $end = new DateTime($fetch_details['picker_end']);

                                                        // Calculate the difference
                                                        $interval = $start->diff($end);

                                                        // Format the difference in hours and minutes
                                                        echo $interval->h . ' hour(s) ' . $interval->i . ' minute(s)';
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                            <thead>
                                                <tr class="table-info">
                                                    <th colspan="4">Checker Summary</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Name:</td>
                                                    <td colspan="3"><?php if(empty($fetch_details['checker'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['checker']; } ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Start:</td>
                                                    <td><?php echo $fetch_details['checker_start'];?></td>
                                                    <td style="background: #f2f2f2;"><b>End:</td>
                                                    <td><?php echo $fetch_details['checker_end'];?></td>
                                                </tr>
                                                <tr>
                                                    <td style="background: #f2f2f2;"><b>Duration:</b></td>
                                                    <td colspan="3">
                                                        <?php
                                                        // Convert picker_start and picker_end to DateTime objects
                                                        $start = new DateTime($fetch_details['checker_start']);
                                                        $end = new DateTime($fetch_details['checker_end']);

                                                        // Calculate the difference
                                                        $interval = $start->diff($end);

                                                        // Format the difference in hours and minutes
                                                        echo $interval->h . ' hour(s) ' . $interval->i . ' minute(s)';
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="validation_submit.php">
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-6">
                                    <label>Accountable Picker</label>
                                    <select class="form-control form-control-sm" name="picker" id="picker" disabled>
                                        <option value=""></option>
                                        <option value="<?php echo $fetch_details['picker'];?>"><?php echo $fetch_details['picker'];?></option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>Accountable Checker</label>
                                    <select class="form-control form-control-sm" name="checker" id="checker" disabled>
                                        <option value=""></option>
                                        <option value="<?php echo $fetch_details['checker'];?>"><?php echo $fetch_details['checker'];?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- <p class="text-center text-danger"><small><i>*leave the dropdown blank if no accountable picker/checker*</i></small></p> -->
                        <hr>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Barcode</th>
                                        <th>Description</th>
                                        <th>UOM</th>
                                        <th>Sys Qty</th>
                                        <th>Pick Qty</th>
                                        <th>Check Qty</th>
                                        <th>Error</th>
                                        <th>Position</th>
                                        <th>Final Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_trips_picking WHERE picklistno = '$picklistno' AND dtr = '$dtr' ORDER BY racklocation,uom ASC, sysqty ASC");
                                    while ($row = mysqli_fetch_assoc($result)) {
                
                                        if($row['sysqty'] == $row['pickerqty'] && $row['pickerqty'] == $row['checkerqty']){
                                        }else{
                                            echo '<tr>';
                                            echo '<td><center>' . $row['barcode'] . '</center></td>';
                                            echo '<td>' . $row['description'] . '</td>';
                                            echo '<td><center>' . $row['uom'] . '</center></td>';
                                            echo '<td><center>' . $row['sysqty'] . '</center></td>';
                                            echo '<td><center>' . $row['pickerqty'] . '</center></td>';
                                            echo '<td><center>' . $row['checkerqty'] . '</center></td>';
                                    ?>
                                            <td><center>
                                                <select class="form-control form-control-sm" name="error_type[<?php echo $row['id']; ?>]" id="error_type<?php echo $row['id']; ?>">
                                                    <option value=""></option>
                                                    <option value="Over Picked">Over Picked</option>
                                                    <option value="Short Picked">Short Picked</option>
                                                    <option value="Wrong Encode">Wrong Encode</option>
                                                    <option value="System Discrepancy">System Discrepancy</option>
                                                </select>
                                            </center></td>
                                            <td><center>
                                                <select class="form-control form-control-sm accountable" name="accountable[<?php echo $row['id']; ?>]" id="accountable<?php echo $row['id']; ?>" disabled>
                                                    <option value=""></option>
                                                    <option value="Picker">Picker</option>
                                                    <option value="Checker">Checker</option>
                                                </select>
                                            </center></td>
                                            <td><center>
                                                <input type="text" class="form-control form-control-sm" name="sku[<?php echo $row['id']; ?>]" value="<?php echo $row['sku']; ?>" hidden>
                                                <input type="text" class="form-control form-control-sm" name="description[<?php echo $row['id']; ?>]" value="<?php echo $row['description']; ?>" hidden>
                                                <input type="text" class="form-control form-control-sm" name="uom[<?php echo $row['id']; ?>]" value="<?php echo $row['uom']; ?>" hidden>
                                                <input type="number" class="form-control form-control-sm" name="sysqty[<?php echo $row['id']; ?>]" value="<?php echo $row['sysqty']; ?>" hidden>
                                                <input type="number" class="form-control form-control-sm" name="pickerqty[<?php echo $row['id']; ?>]" value="<?php echo $row['pickerqty']; ?>" hidden>
                                                <input type="number" class="form-control form-control-sm" name="checkerqty[<?php echo $row['id']; ?>]" value="<?php echo $row['checkerqty']; ?>" hidden>
                                                <input type="number" class="form-control form-control-sm" name="finalqty[<?php echo $row['id']; ?>]" required style="width:70px;" required disabled value="<?php echo $row['checkerqty'];?>">
                                            </center></td>
                                    <?php
                                    }
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <center>
                    <button type="button" class="d-sm-inline-block btn btn-sm btn-success" data-toggle="modal" data-target="#submitModal"><i class="fa fa-check"></i> Submit Validation</button>
                </center>

                <!-- Submit Modal-->
                <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">                                                                                         
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-check"></i> Submit Validation</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Do you want to submit the validated quantities?
                            </div>
                            <div class="modal-footer">
                                <input type="text" name="picklistno" value="<?php echo $picklistno; ?>" hidden>
                                <input type="text" name="dtr" value="<?php echo $dtr; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->

        <br>

        <?php
        include_once("footer.php");
        ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Error_type Dropdown Event Listener
            document.querySelectorAll("select[name^='error_type']").forEach(function(select) {
                select.addEventListener("change", function() {
                    let rowId = this.name.match(/\[(\d+)\]/)[1]; // Extract the row ID
                    let accountableSelect = document.getElementById("accountable" + rowId);
                    let finalQtyInput = document.querySelector(`input[name="finalqty[${rowId}]"]`);
                    let pickerSelect = document.getElementById("picker");
                    let checkerSelect = document.getElementById("checker");
                    let checkerQty = parseFloat(document.querySelector(`input[name="checkerqty[${rowId}]"]`).value);

                    if (this.value === "Over Picked" || this.value === "Short Picked" || this.value === "Wrong Encode" || this.value === "System Discrepancy") {
                        // Enable accountable dropdown and finalqty input
                        accountableSelect.removeAttribute("disabled");
                        finalQtyInput.removeAttribute("disabled");
                        finalQtyInput.value = ""; // Reset finalqty for fresh input

                        // Accountable selection event
                        accountableSelect.addEventListener("change", function() {
                            let selectedValues = Array.from(document.querySelectorAll(".accountable"))
                                .map(sel => sel.value)
                                .filter(value => value !== ""); // Get selected non-empty values

                            // Enable both if both are selected
                            if (selectedValues.includes("Picker") && selectedValues.includes("Checker")) {
                                pickerSelect.removeAttribute("disabled");
                                pickerSelect.setAttribute("required", "required");
                                checkerSelect.removeAttribute("disabled");
                                checkerSelect.setAttribute("required", "required");
                            } else if (selectedValues.includes("Picker")) {
                                pickerSelect.removeAttribute("disabled");
                                pickerSelect.setAttribute("required", "required");
                                checkerSelect.setAttribute("disabled", "disabled");
                                checkerSelect.removeAttribute("required");
                                checkerSelect.value = ""; // Reset Checker selection
                            } else if (selectedValues.includes("Checker")) {
                                checkerSelect.removeAttribute("disabled");
                                checkerSelect.setAttribute("required", "required");
                                pickerSelect.setAttribute("disabled", "disabled");
                                pickerSelect.removeAttribute("required");
                                pickerSelect.value = ""; // Reset Picker selection
                            } else {
                                pickerSelect.setAttribute("disabled", "disabled");
                                pickerSelect.removeAttribute("required");
                                pickerSelect.value = ""; // Reset Picker selection
                                checkerSelect.setAttribute("disabled", "disabled");
                                checkerSelect.removeAttribute("required");
                                checkerSelect.value = ""; // Reset Checker selection
                            }
                        });
                    } else {
                        // Reset and disable accountable dropdown
                        accountableSelect.setAttribute("disabled", "disabled");
                        accountableSelect.value = ""; // Reset to blank option

                        // Disable and reset finalqty to 0
                        finalQtyInput.setAttribute("disabled", "disabled");
                        finalQtyInput.value = checkerQty;

                        // Disable and reset picker and checker
                        pickerSelect.setAttribute("disabled", "disabled");
                        pickerSelect.removeAttribute("required");
                        pickerSelect.value = ""; // Reset Picker selection
                        checkerSelect.setAttribute("disabled", "disabled");
                        checkerSelect.removeAttribute("required");
                        checkerSelect.value = ""; // Reset Checker selection
                    }
                });
            });
        });

        // Accountable Dropdown (Enable/Disable picker/checker based on selections)
        document.querySelectorAll(".accountable").forEach(function(select) {
            select.addEventListener("change", function() {
                let picker = document.getElementById("picker");
                let checker = document.getElementById("checker");

                let selectedValues = Array.from(document.querySelectorAll(".accountable"))
                    .map(sel => sel.value)
                    .filter(value => value !== ""); // Get selected non-empty values

                if (selectedValues.includes("Picker")) {
                    picker.removeAttribute("disabled");
                    picker.setAttribute("required", "required");
                } else {
                    picker.setAttribute("disabled", "disabled");
                    picker.removeAttribute("required");
                }

                if (selectedValues.includes("Checker")) {
                    checker.removeAttribute("disabled");
                    checker.setAttribute("required", "required");
                } else {
                    checker.setAttribute("disabled", "disabled");
                    checker.removeAttribute("required");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll("input[name^='finalqty']").forEach(function (input) {
                input.addEventListener("blur", function () { // Changed from "input" to "blur"
                    let rowId = this.name.match(/\[(\d+)\]/)[1]; // Extract row ID
                    let sysQty = parseFloat(document.querySelector(`input[name="sysqty[${rowId}]"]`).value);
                    let pickerQty = parseFloat(document.querySelector(`input[name="pickerqty[${rowId}]"]`).value);
                    let checkerQty = parseFloat(document.querySelector(`input[name="checkerqty[${rowId}]"]`).value);
                    let errorType = document.getElementById(`error_type${rowId}`).value;
                    let accountableValue = document.getElementById(`accountable${rowId}`).value;
                    let finalQty = parseFloat(this.value) || 0; // Ensure valid number

                    if (finalQty > sysQty) {
                        alert("Final Qty cannot be greater than Sys Qty!");
                        this.value = sysQty;
                    } else if (errorType === "Short Picked") {
                        if (accountableValue === "Picker" && finalQty < pickerQty) {
                            alert("Final quantity must be greater than or equal to Picker quantity");
                            this.value = pickerQty;
                        } else if (accountableValue === "Checker" && finalQty < checkerQty) {
                            alert("Final quantity must be greater than or equal to Checker quantity");
                            this.value = checkerQty;
                        }
                    } else if (errorType === "Over Picked") {
                        if (accountableValue === "Picker" && finalQty > pickerQty) {
                            alert("Final quantity must be less than or equal to Picker quantity");
                            this.value = pickerQty;
                        } else if (accountableValue === "Checker" && finalQty > checkerQty) {
                            alert("Final quantity must be less than or equal to Checker quantity");
                            this.value = checkerQty;
                        }
                    }
                });
            });
        });

        // Reset add modal button
        $('.assign-btn').click(function(){
            $('#AssignForm')[0].reset();
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