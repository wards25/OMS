<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$hub = $_SESSION['hub'];

if (isset($_SESSION['id']) && in_array(143, $permission)) {
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
            <a type="button" href="javascript:history.go(-1)"
                class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

        <script>
            window.setTimeout(function () {
                $(".alert").fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 2000);
        </script>

        <?php
        // Get status message
        if (!empty($_GET['status'])) {
            switch ($_GET['status']) {
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
        <?php if (!empty($statusMsg)) { ?>
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
                            <tr class="table-info">
                                <th>Level-Pos</th>
                                <th>SKU</th>
                                <th>Scan</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-level-list">

                        </tbody>
                    </table>
                    <?php
}
?>
            </div>
        </div>
    </div>
    <!-- End Table -->

</div>
<!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

<!-- View Modal-->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<span
                        id="view-racklocation"></span></h6>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="closeModalButton2">
                    <span aria-hidden="true"><small>×</small></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-12 d-flex align-items-center">
                            <label class="mr-2">SKU:</label>
                            <input type="text" class="form-control form-control-sm" id="view-sku" readonly>
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
                            <input type="text" class="form-control form-control-sm" id="view-bbd" readonly>
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
                            <label class="mr-2">QTY:</label>
                            <input type="text" class="form-control form-control-sm" id="view-qty" readonly>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="form-group">
                    <div class="form-row">
                        <div class="col-12 d-flex align-items-center">
                            <label class="mr-2">Status:</label>
                            <input type="text" class="form-control form-control-sm" id="view-status" readonly>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Scan Modal-->
<div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i>
                    &nbsp;<span id="scan-racklocation"></span></h6>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="closeModalButton">
                    <span aria-hidden="true"><small>×</small></span>
                </button>
            </div>
            <div class="alert-container"></div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="form-row">
                        <div class="col-8">
                            <input type="text" class="form-control form-control-sm barcode-scan"
                                placeholder="Scan/Encode here..." autofocus>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-sm btn-danger btn-block clear-btn">Clear</button>
                        </div>
                        <!-- <div class="col-3">
                            <button class="btn btn-sm btn-primary btn-block btn-manual">Manual</button>
                        </div> -->
                    </div>
                </div>
                <hr>
                <form class="inventoryForm">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label class="mr-2">SKU:</label>
                                <input type="text" class="form-control form-control-sm sku-id" readonly>
                                <input type="text" class="form-control form-control-sm sku-itemcode" name="sku" hidden
                                    required>
                                <input type="text" class="form-control form-control-sm sku-uom" name="uom" hidden>
                                <?php
                                $query = "SELECT * FROM tbl_product ORDER BY itemcode";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    $options = mysqli_fetch_all($result, MYSQLI_ASSOC); ?>

                                    <select class="form-control form-control-sm search_sku" class="search_sku" name="sku"
                                        style="width:100%;" required hidden disabled>
                                        <option value=""></option>
                                        <?php
                                        foreach ($options as $option) {
                                            ?>
                                            <option value="<?php echo $option['itemcode']; ?>">
                                                <?php echo $option['itemcode'] . ' - ' . $option['description']; ?>
                                            </option>
                                            <?php
                                        }
                                }
                                ?>
                                </select>
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
                                    <?php for ($i = 1; $i <= 31; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
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
                                <input type="number" class="form-control form-control-sm scan-case" name="cases"
                                    value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                <input type="number" class="form-control form-control-sm scan-pcs" name="pieces"
                                    value="0">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-6">
                                <div class="col-12 d-flex align-items-center">
                                    <label class="mr-2">Status:</label>
                                    <select class="form-control form-control-sm scan-status" name="status" required>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="HOLD">HOLD</option>
                                        <option value="EMPTY">EMPTY</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="col-12 d-flex align-items-center">
                                    <label class="mr-2">Action:</label>
                                    <select class="form-control form-control-sm" name="action" required>
                                        <option value="UPDATE">UPDATE</option>
                                        <option value="ADD">ADD</option>
                                        <option value="MOVE">MOVE</option>
                                        <option value="EMPTY">EMPTY</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center">
                        <input type="text" name="groupno" id="scan-groupno" hidden>
                        <input type="text" name="rack" id="scan-rack" hidden>
                        <input type="text" name="column" id="scan-column" hidden>
                        <input type="text" name="level" id="scan-level" hidden>
                        <input type="text" name="pos" id="scan-pos" hidden>
                        <input type="text" name="racklocation" id="scan-racklocation-input" hidden>
                        <input type="text" name="location" id="scan-location" hidden>
                        <button class="btn btn-sm btn-success" name="submit"><i class="fa-solid fa-check"></i>
                            Submit/Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
            clearTimeout(barcodeTimer);

            let inputField = $(this);
            let barcode = inputField.val().trim();
            let modalBody = inputField.closest('.modal-body');

            if (barcode.length < 5) return; // Wait until at least 7 characters

            barcodeTimer = setTimeout(() => fetchBarcodeDetails(barcode, modalBody), 500);
        });

        function fetchBarcodeDetails(barcode, modalBody) {
            $.ajax({
                url: 'inventory_product.php',
                type: 'POST',
                data: { barcode },
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        modalBody.find('.sku-id').val(`${response.itemcode} - ${response.description}`);
                        modalBody.find('.sku-itemcode').val(response.itemcode);
                        modalBody.find('.sku-uom').val(response.uom);
                    } else {
                        showAlert(response.message, 'warning', modalBody);
                    }
                },
                error: function (xhr) {
                    showAlert(`AJAX error: ${xhr.responseText}`, "danger", modalBody);
                }
            });
        }

        // Clear button handler
        $(document).on('click', '.clear-btn', function () {
            let modalBody = $(this).closest('.modal-body');
            modalBody.find('.barcode-scan, .sku-id, .sku-itemcode, .sku-uom').val('');
            modalBody.find('.alert-container').html('');
            modalBody.find('.barcode-scan').focus();
        });

        // Inventory Form Submit with tbody refresh
        $(document).on('submit', '.inventoryForm', function (event) {
            event.preventDefault();
            let modalBody = $(this).closest('.modal-body');

            $.ajax({
                type: "POST",
                url: "inventory_level_submit.php",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    let message = response.status === "insert" ? "Inventory added successfully!" :
                        response.status === "update" ? "Inventory updated successfully!" :
                            `Error: ${response.message}`;
                    let type = response.status === "insert" || response.status === "update" ? "success" : "warning";

                    showAlert(message, type, modalBody);
                    InventoryLevelList();

                    // Close modal and reset fields
                    setTimeout(() => {
                        modalBody.closest('.modal').modal('hide');
                        modalBody.find('.barcode-scan, .sku-id, .scan-case, .scan-pcs').val('');
                    }, 1000);
                },
                error: function (xhr) {
                    showAlert(`AJAX error: ${xhr.responseText}`, "danger", modalBody);
                }
            });
        });

        // Show alert inside the correct modal
        function showAlert(message, type, modalBody) {
            let alertContainer = modalBody.find('.alert-container');
            if (!alertContainer.length) {
                modalBody.prepend('<div class="alert-container"></div>');
                alertContainer = modalBody.find('.alert-container');
            }

            alertContainer.html(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} fa-sm"></i>
                        <b>${type === 'success' ? 'Success!' : 'Error!'}</b> ${message}
                    </div>
                `);

            setTimeout(() => alertContainer.find(".alert").fadeOut(300, function () { $(this).remove(); }), 3000);
        }

        // Initialize date fields
        populateYears();
        populateMonths();
        // initializeDays();

        function populateYears() {
            let currentYear = new Date().getFullYear();
            let yearOptions = '';

            for (let year = currentYear - 10; year <= currentYear + 6; year++) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }

            $('.year').html(yearOptions).val(currentYear).on('change', updateDays);
        }

        function populateMonths() {
            $('.month').val("1").on('change', updateDays);
        }

        function initializeDays() {
            $('.month, .year').each(function () {
                updateDays(this);
            });
        }

        function updateDays(element) {
            let modal = $(element).closest('.modal');
            let month = parseInt(modal.find('.month').val());
            let year = parseInt(modal.find('.year').val());
            let daySelect = modal.find('.day');
            let daysInMonth = [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

            let dayOptions = '';
            for (let i = 1; i <= daysInMonth[month - 1]; i++) {
                dayOptions += `<option value="${i}">${i}</option>`;
            }

            daySelect.html(dayOptions);
        }

        function isLeapYear(year) {
            return (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0));
        }

        // Inventory level list
        function InventoryLevelList() {
            $.post("inventory_level_table.php", {
                groupno: "<?php echo $_GET['groupno']; ?>",
                rack: "<?php echo $_GET['rack']; ?>",
                column: "<?php echo $_GET['column']; ?>",
                hub: "<?php echo $_SESSION['hub']; ?>"
            }, function (data) {
                $('#inventory-level-list').html(data);
            });
        }

        // View inventory
        function viewInventory() {
            let rackloc = $(this).data('rackloc');
            $('#viewModal').modal('show');
            InventoryLevelList();
            $.post("inventory_level_view.php", { rackloc }, function (data) {
                $('#view-racklocation').html(data.racklocation);
                $('#view-sku').val(data.sku + ' - ' + data.description);
                $('#view-bbd').val(data.bbd);
                $('#view-qty').val(data.qty + ' - ' + data.uom);
                $('#view-status').val(data.status);
            }, "json");
        }

        // Scan inventory
        function scanInventory() {
            let groupno = "<?php echo $_GET['groupno']; ?>";
            let rack = "<?php echo $_GET['rack']; ?>";
            let column = "<?php echo $_GET['column']; ?>";
            let level = $(this).data('level');
            let pos = $(this).data('pos');

            $('#scanModal').modal('show');
            InventoryLevelList();

            $.post("inventory_level_scan_view.php", { groupno, rack, column, level, pos }, function (data) {
                $('#scan-racklocation').html(data.racklocation);
                $('#scan-racklocation-input').val(data.racklocation);
                $('#scan-sku').val(data.sku);
                $('#scan-rack').val(data.rack);
                $('#scan-column').val(data.col);
                $('#scan-level').val(data.level);
                $('#scan-pos').val(data.pos);
                $('#scan-location').val(data.location);
                $('#scan-groupno').val(data.groupno);

                // New values
                $('.sku-id').val(`${data.sku} - ${data.description}`);
                $('.sku-itemcode').val(data.sku);
                $('.scan-case').val(data.cases || 0);
                $('.scan-pcs').val(data.pieces || 0);

                if (data.bbd) {
                    let dateParts = data.bbd.split("-");
                    $('.year').val(dateParts[0]);
                    $('.month').val(parseInt(dateParts[1]));
                    updateDays($('.month')); // Recalculate days for the selected month
                    $('.day').val(parseInt(dateParts[2]));
                }
                $('.scan-status').val(data.status || "ACTIVE");
            }, "json");
        }

        // Remove required attributes if status is EMPTY
        $(document).on('change', '.scan-status', function () {
            let formInputs = $(this).closest('form').find('input, select');

            if ($(this).val() === "EMPTY") {
                formInputs.each(function () {
                    if ($(this).attr("name") !== "status") {
                        $(this).removeAttr("required");
                    }
                });
            } else {
                formInputs.each(function () {
                    if (!$(this).prop("hidden") && !$(this).prop("disabled") && $(this).attr("name") !== "status") {
                        $(this).attr("required", "required");
                    }
                });
            }
        });

        $(".btn-manual").click(function (event) {
            event.preventDefault(); // Prevent form submission if inside form
            // Disable barcode scan input
            $(".barcode-scan").prop("disabled", true);
            $(".sku-id").prop("hidden", true);
            // Hide and disable SKU text input
            $(".sku-itemcode").prop("disabled", true).prop("required", false);
            // Show and require the SKU dropdown
            $(".search_sku").prop("hidden", false).prop("disabled", false);
        });

        $(".clear-btn").click(function () {
            // Re-enable barcode scan input
            $(".barcode-scan").prop("disabled", false);
            $(".sku-id").prop("hidden", false);
            // Show SKU text input and make it required
            $(".sku-itemcode").prop("disabled", false).prop("required", true);
            // Hide and remove required from SKU dropdown
            $(".search_sku").prop("hidden", true).prop("disabled", true);
        });

        // Event delegation for dynamically added buttons
        $(document).on('click', '.view-btn', viewInventory);
        $(document).on('click', '.scan-btn', scanInventory);

        InventoryLevelList();

        // Autofocus modal
        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.barcode-scan').focus();
        });

        // Reset add modal button
        $('.scan-btn').click(function () {
            $('#scanModal')[0].reset();
        });

        $('#closeModalButton').click(function () {
            $('#scanModal').modal('hide');
        });

        $('#closeModalButton2').click(function () {
            $('#viewModal').modal('hide');
        });

        $('.search_sku').select2({
            theme: "bootstrap",
            dropdownParent: $("#scanModal"),
        });
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
/*
}else{
    header("Location: denied.php");
}
*/
?>