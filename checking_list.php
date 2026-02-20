<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(101, $permission))
{
include_once("nav_trips.php");

$checker = $_GET['checker'];
$picklistno = $_GET['picklistno'];
$dtr = $_GET['dtr'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Checking: <?php echo $picklistno; ?></h4>
            <a href="checking.php" type="button" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm" ><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

        <style>
            thead tr {
                position: sticky;
                top: 0;
                z-index: 1; /* Ensure the header stays above table body rows */
            }
        </style>

                <!-- Alert Message -->
                <div id="barcode-alert" class="alert alert-danger fade show" style="display: none;" role="alert">
                    <i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<strong>Error!</strong> Barcode not found in this picklist.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead style="position: sticky; top: 0; z-index: 1; background-color: white;">
                                    <tr>
                                        <!-- Duration -->
                                        <?php
                                        $duration_query = mysqli_query($conn, "SELECT * FROM tbl_trips_picklist WHERE checker = '$checker' AND picklistno = '$picklistno' AND dtr = '$dtr'");
                                        $fetch_duration = mysqli_fetch_assoc($duration_query);
                                        $givenTime = $fetch_duration['checker_start'];
                                        ?>
                                        <th class="bg-danger" colspan="3">
                                            <center class="duration text-light" data-start-time="<?php echo $givenTime; ?>">
                                        </th>
                                    </tr>
                                    <tr>
                                        <!-- Barcode Scanning Input -->
                                        <th colspan="1">
                                            <input type="text" id="barcode-scan" class="form-control form-control-sm" placeholder="Tap here to scan..." autofocus>
                                        </th>
                                        <th colspan="2">
                                            <button type="button" id="clear-btn" class="btn btn-sm btn-primary btn-block">Scan/Clear</button>
                                        </th>
                                    </tr>
                                    <tr class="table-success text-center">
                                        <th>To Check</th>
                                        <th>Check Qty</th>
                                    </tr>
                                </thead>
                                <form id="ItemForm">
                                <tbody id="picking-table-body">
                                    <?php
                                        $result = mysqli_query($conn, "
                                            SELECT * FROM tbl_trips_picking 
                                            WHERE checker = '$checker' AND picklistno = '$picklistno' AND dtr = '$dtr' 
                                            ORDER BY 
                                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 1), 'R', -1) AS UNSIGNED),
                                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 2), 'C', -1) AS UNSIGNED),
                                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 3), 'L', -1) AS UNSIGNED),
                                                racklocation
                                        ");
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr data-barcode="' . $row['barcode'] . '">';
                                            echo '<td class="text-center">'.$row['sku'].'<br>'.$row['description'].'<br>'.$row['barcode'];
                                            echo '<td class="text-center" hidden>'.$row['sysqty'].'</td>';
                                    ?>
                                    <td class="text-center align-middle">
                                        <input type="text" class="form-control form-control-sm" name="sku[<?php echo $row['id']; ?>]" value="<?php echo $row['sku']; ?>" hidden>
                                        <input type="number" class="form-control form-control-sm picker-qty" name="pickerqty[<?php echo $row['id']; ?>]" required maxlength="2" oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" disabled>
                                    </td>
                                    <?php
                                        echo '</tr>';
                                    } 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <center>
                    <button type="button" class="d-sm-inline-block btn btn-sm btn-success" data-toggle="modal" data-target="#submitModal"><i class="fa fa-check"></i> Submit Count</button>
                </center>

                <!-- Submit Modal-->
                <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">                                                                                         
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-check"></i> Submit Checking</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Do you want to submit the following checked quantities?
                            </div>
                            <div class="modal-footer">
                                <input type="text" name="checker" value="<?php echo $checker; ?>" hidden>
                                <input type="text" name="picklistno" value="<?php echo $picklistno; ?>" hidden>
                                <input type="text" name="dtr" value="<?php echo $dtr; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" id="submit-picking-btn" name="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>

                <script>
                    // Submit item
                    $('#ItemForm').submit(function(e) {
                        e.preventDefault(); // Prevent default form submission

                        let allValid = true; // Assume all inputs are valid at the start

                        // Validate each picker-qty input field
                        $('#picking-table-body .picker-qty').each(function() {
                            const qtyValue = $(this).val().trim(); // Get the trimmed value of the input

                            if (!qtyValue || isNaN(qtyValue) || parseInt(qtyValue) < 0) {
                                // Mark invalid fields
                                $(this).addClass('is-invalid');
                                allValid = false;
                            } else {
                                // Remove the invalid class for valid inputs
                                $(this).removeClass('is-invalid');
                            }
                        });

                        if (!allValid) {
                            alert('Please ensure all Picker Quantity fields are correctly filled before submitting.');
                            return; // Exit without submitting if validation fails
                        }

                        // Serialize the form data
                        const itemData = $('#ItemForm').serialize();

                        // AJAX submission
                        $.ajax({
                            type: "POST",
                            url: "checking_submit.php",
                            data: itemData,
                            dataType: "json",
                            success: function(data) {
                                console.log(data); // Debugging response
                                if (data.status == 'success') {
                                    window.location.href = "checking.php?status=succ";
                                } else {
                                    alert(data.status || 'An error occurred. Please try again.');
                                }
                            }
                        });
                    });

                    // Add functionality to clear the input field, refocus it, and scroll it into view
                    document.getElementById('clear-btn').addEventListener('click', function () {
                        const barcodeInput = document.getElementById('barcode-scan');
                        if (barcodeInput) {
                            barcodeInput.value = ''; // Clear the input field
                            barcodeInput.focus();   // Refocus the input field
                            barcodeInput.scrollIntoView({ behavior: 'smooth', block: 'center' }); // Scroll the input field into the center
                        }
                    });

                    // Barcode scan input functionality
                    document.getElementById('barcode-scan').addEventListener('input', function () {
                        const barcodeInput = this.value.trim();
                        let found = false;

                        // Hide the alert initially
                        const alert = document.getElementById('barcode-alert');
                        if (alert) {
                            alert.style.display = 'none';
                        }

                        if (barcodeInput) {
                            // Find the row with the matching barcode
                            const rows = document.querySelectorAll('#picking-table-body tr');
                            rows.forEach(row => {
                                if (row.dataset.barcode === barcodeInput) {
                                    found = true;
                                    const qtyInput = row.querySelector('.picker-qty');
                                    if (qtyInput) {
                                        qtyInput.disabled = false; // Enable the picker quantity input
                                        qtyInput.focus();         // Focus the picker quantity input
                                    }
                                    this.value = ''; // Clear the barcode input field
                                }
                            });
                        }

                        // Show the alert if barcode is not found
                        if (!found && alert) {
                            alert.style.display = 'block';
                            setTimeout(() => {
                                $(alert).fadeOut(500).slideUp(500);
                            }, 3500);
                        }
                    });

                    // Validate picker quantity against sysqty
                    document.querySelectorAll('.picker-qty').forEach(input => {
                        input.addEventListener('input', function () {
                            const row = this.closest('tr'); // Find the row of the input
                            const sysQtyCell = row.querySelector('td:nth-child(2)'); // Adjust the column index to match sysqty's location
                            if (!sysQtyCell) return; // Exit if sysqty cell is not found

                            const sysQty = parseInt(sysQtyCell.textContent.trim(), 10); // Get the sysqty value
                            const pickerQty = parseInt(this.value.trim(), 10) || 0; // Get the pickerqty value, default to 0 if empty or invalid

                            // Check if pickerqty exceeds sysqty
                            if (pickerQty > sysQty) {
                                alert('Picker Quantity Cannot Exceed System Quantity!');
                                this.value = ''; // Clear the invalid input
                                this.focus();    // Refocus the input
                            }
                        });
                    });

                    // Add and remove 'table-warning' class on focus and blur
                    document.querySelectorAll('.picker-qty').forEach(input => {
                        input.addEventListener('focus', function () {
                            const row = this.closest('tr'); // Get the parent <tr> of the input
                            if (row) {
                                row.classList.add('table-warning'); // Add the class
                            }
                        });

                        input.addEventListener('blur', function () {
                            const row = this.closest('tr'); // Get the parent <tr> of the input
                            if (row) {
                                row.classList.remove('table-warning'); // Remove the class
                            }
                        });
                    });

                    // Realtime duration clock
                    function updateDurations() {
                        const durationElements = document.querySelectorAll('.duration');

                        durationElements.forEach(el => {
                            const startTime = el.getAttribute('data-start-time');
                            if (startTime) {
                                const startDateTime = new Date(startTime);
                                const now = new Date();

                                // Calculate the time difference
                                const diffMs = now - startDateTime;
                                const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                                const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                                // Update the element
                                el.textContent = `Duration: ${diffHrs} Hr(s) & ${diffMins} Min(s)`;
                            }
                        });
                    }

                    // Update durations every minute
                    setInterval(updateDurations, 60000);

                    // Initial update
                    updateDurations();
                </script>

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