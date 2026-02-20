<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$hub = $_SESSION['hub'];

if(isset($_SESSION['id']) && in_array(143, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

$groupno = $_GET['groupno'];
$rack = $_GET['rack'];
$column = $_GET['column'];
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Rack <?php echo $rack; ?> - Column <?php echo $column; ?></h4>
            <a type="button" href="javascript:history.go(-1)" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
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

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Select Level</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Level-Pos</th>
                                        <th>Scan</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // SQL query to calculate match percentage per rack
                                    $query = "SELECT *
                                              FROM tbl_inventory_rack 
                                              WHERE rack = '$rack'
                                              AND col = '$column'
                                              AND groupno = '$groupno'
                                              AND location = '$hub'";

                                    $result = mysqli_query($conn, $query);

                                    // Loop through each rack result and display in table
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $level = $row['level'];
                                        $racklocation = $row['racklocation'];

                                        // Check if this racklocation exists in tbl_inventory_count
                                        $checking_query = mysqli_query($conn, "SELECT racklocation FROM tbl_inventory_count WHERE racklocation = '$racklocation'");
                                        $count_checking = mysqli_num_rows($checking_query);
                                        $fetch_checking = mysqli_fetch_assoc($checking_query);

                                        // Determine the class based on the count_checking result
                                        if ($count_checking == 1) {
                                            $row_class = 'table-warning'; // Only one match
                                        } elseif ($count_checking == 0) {
                                            $row_class = 'table-danger';  // No match
                                        } else {
                                            $row_class = 'table-success'; // Data matches
                                        }

                                        echo '<tr class="' . $row_class . '">';
                                        echo '<td>Level ' . $level;
                                        
                                        // Check if pos is empty or not
                                        if (empty($row['pos'])) {
                                            echo '</td>';
                                        } else {
                                            echo '-' . $row['pos'] . '</td>';
                                        }

                                        echo '<td><a href="#" class="btn btn-sm btn-info scan-btn" data-toggle="modal" data-target="#scanModal'.$row['racklocation'].'"><i class="fa-solid fa-barcode"></i></a></td>';
                                        echo '<td><a href="#" class="btn btn-sm btn-warning text-dark" data-toggle="modal" data-target="#viewModal'.$row['racklocation'].'"><i class="fa-solid fa-info"></i></a></td>';
                                        echo '</tr>';
                                    ?>  
                                        <!-- View Modal-->
                                        <div class="modal fade" id="viewModal<?php echo $row['racklocation'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<?php echo $row['racklocation'];?></h6>
                                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true"><small>×</small></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php
                                                        // Fetch rack location
                                                        $racklocation = $row['racklocation'];
                                                        $user_id = $_SESSION['id'];  // Get user ID safely

                                                        // Use prepared statements to prevent SQL injection
                                                        $existing_stmt = $conn->prepare("SELECT * FROM tbl_inventory_count WHERE racklocation = ? AND user_id = ?");
                                                        $existing_stmt->bind_param("si", $racklocation, $user_id);
                                                        $existing_stmt->execute();
                                                        $fetch_existing = $existing_stmt->get_result()->fetch_assoc();

                                                        // Ensure data exists before using it
                                                        $sku = $fetch_existing['sku'] ?? ''; 

                                                        // Fetch product details
                                                        $product_stmt = $conn->prepare("SELECT itemcode, description FROM tbl_product WHERE itemcode = ?");
                                                        $product_stmt->bind_param("s", $sku);
                                                        $product_stmt->execute();
                                                        $fetch_product = $product_stmt->get_result()->fetch_assoc();

                                                        $item_code = $fetch_product['itemcode'] ?? '';
                                                        $description = $fetch_product['description'] ?? '';
                                                        $bbd = $fetch_existing['bbd'] ?? '';
                                                        $cases = $fetch_existing['cases'] ?? '';
                                                        $pieces = $fetch_existing['pieces'] ?? '';
                                                        $status = $fetch_existing['status'] ?? '';

                                                        ?>

                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label class="mr-2">SKU:</label>
                                                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($item_code . ' - ' . $description); ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <div class="form-group text-center table-warning">
                                                            <h6 class="text-dark"><b>Expiration Date</b></h6>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="month" class="mr-2">BBD:</label>
                                                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($bbd); ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <div class="form-group text-center table-warning">
                                                            <h6 class="text-dark"><b>Quantity</b></h6>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="month" class="mr-2">CS:</label>
                                                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($cases); ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($pieces); ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label class="mr-2">Status:</label>
                                                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($status); ?>" readonly>
                                                                </div>
                                                            </div>
                                                        </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <!-- Scan Modal-->
                                        <div class="modal fade" id="scanModal<?php echo $row['racklocation'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success">
                                                        <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<?php echo $row['racklocation'];?></h6>
                                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true"><small>×</small></span>
                                                        </button>
                                                    </div>
                                                    <div class="alert-container"></div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-6">
                                                                    <input type="text" class="form-control form-control-sm barcode-scan" placeholder="Tap here to scan..." autofocus>
                                                                </div>
                                                                <div class="col-3">
                                                                    <button type="button" class="btn btn-sm btn-danger btn-block clear-btn">Clear</button>
                                                                </div>
                                                                <div class="col-3">
                                                                    <button class="btn btn-sm btn-primary btn-block">Manual</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <form class="inventoryForm">
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label class="mr-2">SKU:</label>
                                                                    <input type="text" class="form-control form-control-sm sku-id" readonly>
                                                                    <input type="text" class="form-control form-control-sm sku-itemcode" name="sku" hidden required>
                                                                    <input type="text" class="form-control form-control-sm sku-uom" name="uom" hidden>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group text-center table-warning">
                                                            <h6 class="text-dark"><b>Expiration Date</b></h6>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-6 d-flex align-items-center">
                                                                    <label for="month" class="mr-2">Month:</label>
                                                                    <select class="form-control form-control-sm month" name="month" required>
                                                                        <option value="1">January</option>
                                                                        <option value="2">February</option>
                                                                        <option value="3">March</option>
                                                                        <option value="4">April</option>
                                                                        <option value="5">May</option>
                                                                        <option value="6">June</option>
                                                                        <option value="7">July</option>
                                                                        <option value="8">August</option>
                                                                        <option value="9">September</option>
                                                                        <option value="10">October</option>
                                                                        <option value="11">November</option>
                                                                        <option value="12">December</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 d-flex align-items-center">
                                                                    <label for="day" class="mr-2">Day:</label>
                                                                    <select class="form-control form-control-sm day" name="day" required>
                                                                        <!-- Days will be dynamically added here -->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="year" class="mr-2">Year:</label>
                                                                    <select class="form-control form-control-sm year" name="year" required>
                                                                        <!-- Years will be dynamically added here -->
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group text-center table-warning">
                                                            <h6 class="text-dark"><b>Quantity</b></h6>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="month" class="mr-2">CS:</label>
                                                                    <input type="number" class="form-control form-control-sm" name="cases" value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                                                    <input type="number" class="form-control form-control-sm" name="pieces" value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-12 d-flex align-items-center">
                                                                    <label class="mr-2">Status:</label>
                                                                    <select class="form-control form-control-sm" name="status" required>
                                                                        <option value="ACTIVE">ACTIVE</option>
                                                                        <option value="HOLD">HOLD</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="form-group text-center">
                                                            <input type="text" name="groupno" value="<?php echo $row['groupno'];?>" hidden>
                                                            <input type="text" name="rack" value="<?php echo $row['rack'];?>" hidden>
                                                            <input type="text" name="column" value="<?php echo $row['col'];?>" hidden>
                                                            <input type="text" name="level" value="<?php echo $row['level'];?>" hidden>
                                                            <input type="text" name="pos" value="<?php echo $row['pos'];?>" hidden>
                                                            <input type="text" name="racklocation" value="<?php echo $row['racklocation'];?>" hidden>
                                                            <input type="text" name="location" value="<?php echo $row['location'];?>" hidden>
                                                            <button class="btn btn-sm btn-success" name="submit"><i class="fa-solid fa-check"></i> Submit/Update</button>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        $(document).ready(function () {
            let barcodeTimer; // Timer variable for debounce

            // Listen for barcode scan input with debounce
            $(document).on('input', '.barcode-scan', function () {
                clearTimeout(barcodeTimer); // Reset timer if user keeps typing

                let inputField = $(this);
                let barcode = inputField.val().trim();
                let modalBody = inputField.closest('.modal-body');

                if (barcode === '') return;

                barcodeTimer = setTimeout(function () {
                    console.log("Scanned Barcode:", barcode);

                    $.ajax({
                        url: 'inventory_product.php', // Update with your actual API endpoint
                        type: 'POST',
                        data: { barcode: barcode },
                        dataType: "json",
                        success: function (response) {
                            console.log("AJAX Response:", response);

                            if (response.status === 'success') {
                                modalBody.find('.sku-id').val(response.itemcode + ' - ' + response.description);
                                modalBody.find('.sku-itemcode').val(response.itemcode);
                                modalBody.find('.sku-uom').val(response.uom);
                            } else {
                                showAlert(response.message, 'warning', modalBody);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log("AJAX Error:", xhr.responseText);
                            showAlert("AJAX error: " + error, "danger", modalBody);
                        }
                    });
                }, 500); // 500ms delay before firing AJAX request
            });

            // Clear button handler
            $(document).on('click', '.clear-btn', function () {
                let modalBody = $(this).closest('.modal-body');
                modalBody.find('.barcode-scan').val('').focus();
                modalBody.find('.sku-id, .sku-itemcode, .sku-uom').val('');
                modalBody.find('.alert-container').html('');
            });

            // Inventory Form Submit with tbody refresh
            $(document).on('submit', '.inventoryForm', function (event) {
                event.preventDefault(); // Stop the form from reloading

                let modalBody = $(this).closest('.modal-body');
                let formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "inventory_level_submit.php",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        console.log("Inventory Submit Response:", response);

                        if (response.status === "insert") {
                            showAlert("Inventory added successfully!", "success", modalBody);
                        } else if (response.status === "update") {
                            showAlert("Inventory updated successfully!", "success", modalBody);
                        } else {
                            showAlert("Error: " + response.message, "warning", modalBody);
                        }

                        // Close modal after submission
                        setTimeout(function () {
                            modalBody.closest('.modal').modal('hide');
                        }, 1000);

                        // Refresh tbody after submission
                        // refreshTable();
                    },
                    error: function (xhr, status, error) {
                        console.log("Submit AJAX Error:", xhr.responseText);
                        showAlert("AJAX error: " + error, "danger", modalBody);
                    }
                });
            });

            // Show alert inside the correct modal
            function showAlert(message, type = 'warning', modalBody) {
                let alertContainer = modalBody.find('.alert-container');

                if (alertContainer.length === 0) {
                    modalBody.prepend('<div class="alert-container"></div>');
                    alertContainer = modalBody.find('.alert-container');
                }

                let alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} fa-sm"></i>
                        <b>${type === 'success' ? 'Success!' : 'Error!'}</b> ${message}
                    </div>
                `;

                alertContainer.html(alertHtml);

                // Auto-hide alert after 3 seconds
                setTimeout(function () {
                    alertContainer.find(".alert").fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        });


        // Date
        document.addEventListener('DOMContentLoaded', function () {
            populateYears(); // Populate year dropdowns initially
            populateMonths(); // Populate month dropdowns initially
            initializeDays(); // Set default days for January

            // Attach event listeners for each instance of month and year dropdown
            document.querySelectorAll('.month').forEach(monthSelect => {
                monthSelect.value = "1"; // Default to January
                monthSelect.addEventListener('change', function () {
                    updateDays(this);
                });
            });

            document.querySelectorAll('.year').forEach(yearSelect => {
                yearSelect.addEventListener('change', function () {
                    updateDays(this);
                });
            });

            function updateDays(element) {
                var modal = element.closest('.modal'); // Find the closest modal container
                var monthSelect = modal.querySelector('.month');
                var yearSelect = modal.querySelector('.year');
                var daySelect = modal.querySelector('.day');

                var month = parseInt(monthSelect.value);
                var year = parseInt(yearSelect.value);

                // Number of days in each month
                var daysInMonth = {
                    1: 31, 2: 28, 3: 31, 4: 30, 5: 31, 6: 30,
                    7: 31, 8: 31, 9: 30, 10: 31, 11: 30, 12: 31
                };

                // Adjust for leap years
                if (month === 2 && isLeapYear(year)) {
                    daysInMonth[2] = 29;
                }

                // Clear current day options
                daySelect.innerHTML = '';

                // Populate day dropdown
                for (var i = 1; i <= daysInMonth[month]; i++) {
                    var option = document.createElement('option');
                    option.value = i;
                    option.text = i;
                    daySelect.appendChild(option);
                }
            }

            function isLeapYear(year) {
                return (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0));
            }

            function populateYears() {
                document.querySelectorAll('.year').forEach(yearSelect => {
                    var currentYear = new Date().getFullYear();
                    var startYear = currentYear - 10;
                    var endYear = currentYear + 6;

                    yearSelect.innerHTML = ''; // Clear existing options

                    for (var year = startYear; year <= endYear; year++) {
                        var option = document.createElement('option');
                        option.value = year;
                        option.text = year;
                        yearSelect.appendChild(option);
                    }

                    yearSelect.value = currentYear; // Default to current year
                });
            }

            function populateMonths() {
                document.querySelectorAll('.month').forEach(monthSelect => {
                    monthSelect.value = "1"; // Default to January
                });
            }

            function initializeDays() {
                document.querySelectorAll('.month, .year').forEach(element => {
                    updateDays(element);
                });
            }
        });

        //autofocus modal
        $(document).ready(function () {
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.barcode-scan').focus();
            });
        });

        // Reset add modal button
        $('.scan-btn').click(function(){
            $('#scanModal')[0].reset();
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