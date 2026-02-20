<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

//get data from checklist
$location = $_GET['location'];
$month = $_GET['month'];

if(isset($_SESSION['id']) && in_array(143, $permission))
{
include_once("nav_inventory.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Pallet Inventory Report: <?php echo $month; ?></h4>
            <a type="button" href="javascript:window.close()" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-times"></i> Close</a>
        </div>
        <hr>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Pallet Count Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <?php
                                $details_query = mysqli_query($conn,"SELECT * FROM tbl_pallets_raw WHERE month='$month' AND location='$location'");
                                $fetch_details = mysqli_fetch_array($details_query);
                                ?>
                                <tbody>
                                    <tr>
                                        <td class="table-primary"><b>Location</b></td>
                                        <td><?php echo $location; ?></td>
                                        <td class="table-primary"><b>Date</b></td>
                                        <td><?php echo date("F d, Y", strtotime($fetch_details['date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Submitted By</b></td>
                                        <td><?php echo $fetch_details['submitted_by']; ?></td>
                                        <td class="table-warning"><b>Submitted At</b></td>
                                        <td><?php echo $fetch_details['submitted_at']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Validated By</b></td>
                                        <td><?php if(empty($fetch_details['validated_by'])){ echo '<i class="text-danger">Not Validated</i>'; }else{ echo $fetch_details['validated_by']; }?></td>
                                        <td class="table-warning"><b>Validated At</b></td>
                                        <td><?php if($fetch_details['validated_at'] == ''){ echo '<i class="text-danger">Not Validated</i>'; }else{ echo $fetch_details['validated_at']; }?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        $pallet_query = mysqli_query($conn,"SELECT * FROM tbl_pallets_raw WHERE month='$month' AND location='$location'");
                        $fetch_pallet = mysqli_fetch_array($pallet_query);
                        ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Good Pallets</b></td>
                                        <td><input type="number" class="form-control form-control-sm" value="<?php echo $fetch_pallet['good_pallets']; ?>" id="good_pallet" readonly></td>
                                    </tr>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Pallets With Issue</b></td>
                                        <td><input type="number" class="form-control form-control-sm" id="pallets_with_issue" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>For Repair</td>
                                        <td colspan="2"><input type="number" class="form-control form-control-sm" value="<?php echo $fetch_pallet['for_repair']; ?>" id="for_repair" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>Missing</td>
                                        <td colspan="2"><input type="number" class="form-control form-control-sm" value="<?php echo $fetch_pallet['missing']; ?>" id="missing" readonly></td>
                                    </tr>
                                    <tr>
                                        <td>Others</td>
                                        <td><input type="number" class="form-control form-control-sm" value="<?php echo $fetch_pallet['others']; ?>" id="others" readonly></td>
                                        <td><input type="text" class="form-control form-control-sm" value="<?php echo $fetch_pallet['remarks']; ?>" id="remarks" readonly></td>
                                    </tr>
                                    <tr>
                                        <td class="table-success" colspan="2"><b>Total Quantity</b></td>
                                        <td><input type="number" class="form-control form-control-sm" id="total_quantity" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td class="text-danger font-weight-bold"><i>Total Good Pallet(s)</i></td>
                                        <td><center><i><div class="text-danger font-weight-bold" id="average"></div></i></center></div></td>
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
        // Function to update the "Good Pallets" value
        function updateGoodPallets() {
            // Get the values of the input fields
            const goodPallet = parseFloat(document.getElementById('good_pallet').value) || 0;
            const forRepair = parseFloat(document.getElementById('for_repair').value) || 0;
            const missing = parseFloat(document.getElementById('missing').value) || 0;
            const others = parseFloat(document.getElementById('others').value) || 0;

            // Calculate the total and total quantity
            const total = forRepair + missing + others;
            const totalQty = total + goodPallet;
            const average = goodPallet + ' Qty';
            // const average = ((goodPallet / totalQty) * 100).toFixed(2) + '%';

            // Update the "Pallets With Issue" field with the total
            document.getElementById('pallets_with_issue').value = total;

            // Update the "Total Quantity" field with the total quantity
            document.getElementById('total_quantity').value = totalQty;

            // Update the "Average" field with the total quantity
            document.getElementById('average').innerText = average;

            // Handle the "Remarks" field based on the "Others" field value
            handleRemarksField();
        }

        function handleRemarksField() {
            const others = parseFloat(document.getElementById('others').value) || 0;

            if (others > 0) {
                document.getElementById('remarks').required = true;
                document.getElementById('remarks').disabled = false;
            } else {
                document.getElementById('remarks').required = false;
                document.getElementById('remarks').disabled = true;
                document.getElementById('remarks').value = ''; // Clear the value
            }
        }

        // Add event listeners to update fields when inputs change
        document.getElementById('good_pallet').addEventListener('input', updateGoodPallets);
        document.getElementById('for_repair').addEventListener('input', updateGoodPallets);
        document.getElementById('missing').addEventListener('input', updateGoodPallets);
        document.getElementById('others').addEventListener('input', updateGoodPallets);

        // Initialize the state of the "Remarks" field on page load
        document.addEventListener('DOMContentLoaded', () => {
            handleRemarksField(); // Call once to set initial state
            updateGoodPallets(); // Call to ensure calculations are correct on page load
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