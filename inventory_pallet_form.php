<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

$month = date("F");

if(isset($_SESSION['id']) && in_array(143, $permission))
{
include_once("nav_inventory.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Count Pallets</h4>
            <a type="button" href="inventory_pallet.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Pallet Count Form</h6> 
                    </div>
                    <div class="card-body">
                        <form method="POST" action="inventory_pallet_submit.php">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Location</b></td>
                                        <td>
                                            <select name="location" class="form-control form-control-sm" required>
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Good Pallets</b></td>
                                        <td><input type="number" class="form-control form-control-sm" id="good_pallet" name="good_pallet" required></td>
                                    </tr>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Pallets With Issue</b></td>
                                        <td><input type="number" class="form-control form-control-sm" id="pallets_with_issue" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>For Repair</td>
                                        <td colspan="2"><input type="number" class="form-control form-control-sm" id="for_repair" name="for_repair"></td>
                                    </tr>
                                    <tr>
                                        <td>Missing</td>
                                        <td colspan="2"><input type="number" class="form-control form-control-sm" id="missing" name="missing"></td>
                                    </tr>
                                    <tr>
                                        <td>Others</td>
                                        <td><input type="number" class="form-control form-control-sm" id="others" name="others"></td>
                                        <td><input type="text" class="form-control form-control-sm" id="remarks" placeholder="Enter remarks..." name="remarks"></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning" colspan="2"><b>Total Quantity</b></td>
                                        <td><input type="number" class="form-control form-control-sm" id="total_quantity" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <input type="text" name="month" value="<?php echo $month; ?>" hidden>
                        <hr>
                        <center>
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
        // Function to update the "Good Pallets" value
        function updateGoodPallets() {
            // Get the values of the input fields
            const goodPallet = parseFloat(document.getElementById('good_pallet').value) || 0;
            const forRepair = parseFloat(document.getElementById('for_repair').value) || 0;
            const missing = parseFloat(document.getElementById('missing').value) || 0;
            const others = parseFloat(document.getElementById('others').value) || 0;

            // Calculate the total
            const total = forRepair + missing + others;
            const total_qty = total + goodPallet;

            // Update the "Pallets With Issue" field with the total
            document.getElementById('pallets_with_issue').value = total;

            // Optionally update the "Total Quantity" field as well
            document.getElementById('total_quantity').value = total_qty;

            // Handle the "Remarks" field based on the "Others" field value
                handleRemarksField();
            }

            function handleRemarksField() {
                const others = document.getElementById('others').value;

                if (others) {
                    document.getElementById('remarks').required = true;
                    document.getElementById('remarks').disabled = false;
                } else {
                    document.getElementById('remarks').required = false;
                    document.getElementById('remarks').disabled = true;
                    document.getElementById('remarks').value = ''; // Clear the value
                }
            }
        
        // Add event listeners to update the "Good Pallets" field when inputs change
        document.getElementById('good_pallet').addEventListener('input', updateGoodPallets);
        document.getElementById('for_repair').addEventListener('input', updateGoodPallets);
        document.getElementById('missing').addEventListener('input', updateGoodPallets);
        document.getElementById('others').addEventListener('input', updateGoodPallets);

        // Initialize the state of the "Remarks" field on page load
        document.addEventListener('DOMContentLoaded', handleRemarksField);

        $(document).on('change','.attendance-checkbox',function(){
            var id = $(this).data('id');
            // var selectRemarks = $('#remarks-' + id);
            if($(this).is(':checked')) {
                $("#remarks-" + id).prop('disabled',true).val('');
                // $("#remarks" + id).val('');
                $("#remarks-" + id).prop('required',false);
            } else {
                $("#remarks-" + id).prop('disabled',false);
                $("#remarks-" + id).prop('required',true);
            }
        });

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#AssetForm')[0].reset();
            $('#IrForm')[0].reset();
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