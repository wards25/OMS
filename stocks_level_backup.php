<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(158, $permission))
{
include_once("nav_stocks.php");
include_once("export_modal.php");

$location = $_GET['location'];
$rack = $_GET['rack'];
$column = $_GET['column'];
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Rack <?php echo $rack; ?> - Column <?php echo $column; ?></h4>
            <?php
                if(isset($_GET['update'])){
                    echo '<a type="button" href="javascript:window.close();" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-times"></i> Close</a>';
                } else {
                    echo '<a type="button" href="javascript:history.go(-1)" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>';
                }
            ?>
        </div>
        <hr>
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
                                        <th>SKU/Update</th>
                                        <th>Scan/Add</th>
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

        <!-- View Modal-->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<span id="view-racklocation"></span></h6>
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
                        <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<span id="scan-racklocation"></span></h6>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close" id="closeModalButton">
                            <span aria-hidden="true"><small>×</small></span>
                        </button>
                    </div>
                    <div class="alert-container"></div>
                    <div class="modal-body">
                        <!-- Product Details -->
                        <div id="product-section">
                            <div class="form-group text-center table-warning">
                                <h6 class="text-dark"><b>Product:</b></h6>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-8">
                                        <input type="text" class="form-control form-control-sm barcode-scan" placeholder="Scan/Encode here..." autofocus>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-sm btn-danger btn-block clear-btn">Clear</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <!-- End Product Details -->
                        <form class="inventoryForm">
                        <!-- Move Stock -->
                        <div id="move-rack-section" style="display:none;">
                            <div class="form-group text-center table-warning">
                                <h6 class="text-dark"><b>Move Stock To:</b></h6>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-12">
                                        <?php 
                                            $rack_query = "SELECT * FROM tbl_inventory_rack WHERE location = '$location' AND (sku = 'NO SKU' OR sku = '') ORDER BY racklocation";
                                            $rack_result = $conn->query($rack_query);
                                            if($rack_result->num_rows> 0){
                                                $options= mysqli_fetch_all($rack_result, MYSQLI_ASSOC);?>
                                             
                                            <select class="form-control form-control-sm" name="move_location" id="search_rack" style="width:100%;" required>
                                                <!-- <option value=""></option> -->
                                            <?php 
                                                foreach ($options as $option) {
                                            ?>
                                                <option value="<?php echo $option['racklocation'];?>"><?php echo $option['racklocation']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            echo '</select>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Move Stock -->
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12 d-flex align-items-center">
                                    <label class="mr-2">SKU:</label>
                                    <input type="text" class="form-control form-control-sm sku-id" readonly>
                                    <input type="text" class="form-control form-control-sm sku-itemcode" name="sku" hidden required>
                                    <input type="text" class="form-control form-control-sm sku-uom" name="uom" hidden>
                                    <?php 
                                    $query ="SELECT * FROM tbl_product ORDER BY itemcode";
                                    $result = $conn->query($query);
                                    if($result->num_rows> 0){
                                        $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
                                     
                                    <select class="form-control form-control-sm search_sku" class="search_sku" name="sku" style="width:100%;" required hidden disabled>
                                        <option value=""></option>
                                    <?php 
                                        foreach ($options as $option) {
                                    ?>
                                        <option value="<?php echo $option['itemcode'];?>"><?php echo $option['itemcode'].' - '.$option['description']; ?> </option>
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
                            <h6 class="text-dark"><b>Receiving Details</b></h6>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-4 d-flex align-items-center">
                                    <label class="mr-2">M:</label>
                                    <select class="form-control form-control-sm received-month" name="received_month" required>
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
                                <div class="col-4 d-flex align-items-center">
                                    <label class="mr-2">D:</label>
                                    <select class="form-control form-control-sm received-day" name="received_day" required></select>
                                </div>
                                <div class="col-4 d-flex align-items-center">
                                    <label class="mr-2">Y:</label>
                                    <select class="form-control form-control-sm received-year" name="received_year" required></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-12 d-flex align-items-center">
                                    <label for="year" class="mr-2">By:</label>
                                    <?php
                                    $query ="SELECT * FROM tbl_employees WHERE position IN ('Ob Checker', 'Ib Checker', 'Utility Picker') ORDER BY employee_name ASC";
                                    $result = $conn->query($query);
                                    if($result->num_rows> 0){
                                        $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                        <select class="form-control form-control-sm received-by" name="received_by" required>
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
                        <hr>
                        <div class="form-group text-center table-warning">
                            <h6 class="text-dark"><b>Expiration Date</b></h6>
                        </div>
                        <div class="form-group">
                            <div class="form-row">
                                <div class="col-4 d-flex align-items-center">
                                    <label for="month" class="mr-2">M:</label>
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
                                <div class="col-4 d-flex align-items-center">
                                    <label for="day" class="mr-2">D:</label>
                                    <select class="form-control form-control-sm day" name="day" required>
                                        <!-- Days will be dynamically added here -->
                                    </select>
                                </div>
                                <div class="col-4 d-flex align-items-center">
                                    <label for="year" class="mr-2">Y:</label>
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
                                <div class="col-5 d-flex align-items-center">
                                    <label for="month" class="mr-2">CS:</label>
                                    <input type="number" class="form-control form-control-sm scan-case" name="cases" value="0">
                                </div>
                                <div class="col-7 d-flex align-items-center">
                                    <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                    <input type="number" class="form-control form-control-sm scan-pcs" name="pieces" value="0">
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
                                            <option value="FREE GOODS/PREMIUM">FREE GOODS/PREMIUM</option>
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
                            <button class="btn btn-sm btn-success" name="submit"><i class="fa-solid fa-check"></i> Submit/Update</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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

    <script>
        $('#search_rack').select2({
            theme: "bootstrap",
            dropdownParent: $("#scanModal"),
        });

        $(document).ready(function () {
            let barcodeTimer;

            $(document).on('input', '.barcode-scan', function () {
                clearTimeout(barcodeTimer);
                let inputField = $(this);
                let barcode = inputField.val().trim();
                let modalBody = inputField.closest('.modal-body');

                if (barcode.length < 5) return;

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

            $(document).on('click', '.clear-btn', function () {
                let modalBody = $(this).closest('.modal-body');
                modalBody.find('.barcode-scan, .sku-id, .sku-itemcode, .sku-uom').val('');
                modalBody.find('.alert-container').html('');
                modalBody.find('.barcode-scan').focus();
            });

            // $(document).on('submit', '.inventoryForm', function (event) {
            //     event.preventDefault();
            //     let modalBody = $(this).closest('.modal-body');

            //     $.ajax({
            //         type: "POST",
            //         url: "stocks_level_submit.php",
            //         data: $(this).serialize(),
            //         dataType: "json",
            //         success: function (response) {
            //             let message = response.status === "insert" ? "Inventory added successfully!" :
            //                           response.status === "update" ? "Inventory updated successfully!" :
            //                           `Error: ${response.message}`;
            //             let type = response.status === "insert" || response.status === "update" ? "success" : "warning";

            //             showAlert(message, type, modalBody);
            //             InventoryLevelList();

            //             setTimeout(() => {
            //                 modalBody.closest('.modal').modal('hide');
            //                 modalBody.find('.barcode-scan, .sku-id, .scan-case, .scan-pcs').val('');
            //             }, 1000);
            //         },
            //         error: function (xhr) {
            //             showAlert(`AJAX error: ${xhr.responseText}`, "danger", modalBody);
            //         }
            //     });
            // });

            // function showAlert(message, type, modalBody) {
            //     let alertContainer = modalBody.find('.alert-container');
            //     if (!alertContainer.length) {
            //         modalBody.prepend('<div class="alert-container"></div>');
            //         alertContainer = modalBody.find('.alert-container');
            //     }

            //     alertContainer.html(`
            //         <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            //             <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} fa-sm"></i>
            //             <b>${type === 'success' ? 'Success!' : 'Error!'}</b> ${message}
            //         </div>
            //     `);

            //     setTimeout(() => alertContainer.find(".alert").fadeOut(300, function () { $(this).remove(); }), 3000);
            // }

            $(document).on('submit', '.inventoryForm', function (event) {
                event.preventDefault();
                let modalBody = $(this).closest('.modal-body');

                $.ajax({
                    type: "POST",
                    url: "stocks_level_submit.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        let message = "";
                        let type = "success";

                        switch(response.status) {
                            case "add_success":
                                message = "Inventory added successfully!";
                                break;
                            case "add_summed":
                                message = "Inventory added and quantities summed with existing SKU + BBD!";
                                break;
                            case "add_new_bbd":
                                message = "Inventory added as a new BBD line!";
                                break;
                            case "update_summed":
                                message = "Inventory updated and quantities summed with existing SKU + BBD!";
                                break;
                            case "update_new_bbd":
                                message = "Inventory updated with a new BBD line!";
                                break;
                            case "move_success":
                                message = "Inventory moved successfully!";
                                break;
                            case "empty_success":
                                message = "Inventory cleared successfully!";
                                break;
                            case "error":
                                message = `Error: ${response.message}`;
                                type = "warning";
                                break;
                            default:
                                message = "Unknown response from server";
                                type = "warning";
                                break;
                        }

                        showAlert(message, type, modalBody);
                        InventoryLevelList();

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

            function showAlert(message, type, modalBody) {
                let alertContainer = modalBody.find('.alert-container');
                if (!alertContainer.length) {
                    modalBody.prepend('<div class="alert-container"></div>');
                    alertContainer = modalBody.find('.alert-container');
                }

                alertContainer.html(`
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} fa-sm"></i>
                        <b>${type === 'success' ? 'Success!' : 'Notice!'}</b> ${message}
                    </div>
                `);

                setTimeout(() => alertContainer.find(".alert").fadeOut(300, function () { $(this).remove(); }), 3000);
            }

            // ==== Expiration Date Logic ====
            function isLeapYear(year) {
                return (year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0));
            }

            function updateDays(element = null) {
                let modal = element ? $(element).closest('.modal') : $('#scanModal');
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
                updateDays();
            }

            // ==== Receiving Date Logic ====
            function updateReceivedDays() {
                let month = parseInt($('.received-month').val());
                let year = parseInt($('.received-year').val());
                let daySelect = $('.received-day');
                let daysInMonth = [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

                let dayOptions = '';
                for (let i = 1; i <= daysInMonth[month - 1]; i++) {
                    dayOptions += `<option value="${i}">${i}</option>`;
                }
                daySelect.html(dayOptions);
            }

            function populateReceivedYears() {
                let currentYear = new Date().getFullYear();
                let yearOptions = '';
                for (let year = currentYear - 10; year <= currentYear + 6; year++) {
                    yearOptions += `<option value="${year}">${year}</option>`;
                }
                $('.received-year').html(yearOptions).val(currentYear).on('change', updateReceivedDays);
            }

            function populateReceivedMonths() {
                $('.received-month').val("1").on('change', updateReceivedDays);
            }

            function initializeReceivedDays() {
                updateReceivedDays();
            }

            // Call on ready
            populateYears();
            populateMonths();
            initializeDays();

            populateReceivedYears();
            populateReceivedMonths();
            initializeReceivedDays();

            // ==== Inventory Load Functions ====
            function InventoryLevelList() {
                $.post("stocks_level_table.php", {
                    rack: "<?php echo $_GET['rack']; ?>",
                    column: "<?php echo $_GET['column']; ?>",
                    hub: "<?php echo $_GET['location']; ?>"
                }, function (data) {
                    $('#inventory-level-list').html(data);
                });
            }

            function viewInventory() {
                let rackloc = $(this).data('rackloc');
                let location = "<?php echo $_GET['location']; ?>";
                $('#viewModal').modal('show');
                InventoryLevelList();
                $.post("stocks_level_view.php", { rackloc, location }, function (data) {
                    $('#view-racklocation').html(data.racklocation);
                    $('#view-sku').val(data.sku + ' - ' + data.description);
                    $('#view-bbd').val(data.bbd);
                    $('#view-qty').val(data.qty + ' - ' + data.uom);
                    $('#view-status').val(data.status);
                }, "json");
            }

            function scanInventory() {
                let location = "<?php echo $_GET['location']; ?>";
                let rack = "<?php echo $_GET['rack']; ?>";
                let column = "<?php echo $_GET['column']; ?>";
                let level = $(this).data('level');
                let pos = $(this).data('pos');

                $('#scanModal').modal('show');

                $.post("stocks_level_scan_view.php", { location, rack, column, level, pos }, function (data) {
                    $('#scan-racklocation').html(data.racklocation);
                    $('#scan-racklocation-input').val(data.racklocation);
                    $('#scan-sku').val(data.sku);
                    $('#scan-rack').val(data.rack);
                    $('#scan-column').val(data.col);
                    $('#scan-level').val(data.level);
                    $('#scan-pos').val(data.pos);
                    $('#scan-location').val(data.location);
                    $('#scan-groupno').val(data.groupno);

                    $('.sku-id').val(`${data.sku} - ${data.description}`);
                    $('.sku-itemcode').val(data.sku);
                    $('.scan-case').val(data.cases || 0);
                    $('.scan-pcs').val(data.pieces || 0);
                    $('.received-by').val(data.received_by);

                    if (data.bbd) {
                        let d = data.bbd.split("-");
                        $('.year').val(d[0]);
                        $('.month').val(parseInt(d[1]));
                        updateDays();
                        $('.day').val(parseInt(d[2]));
                    }

                    if (data.received_date) {
                        let rd = data.received_date.split("-");
                        $('.received-year').val(rd[0]);
                        $('.received-month').val(parseInt(rd[1]));
                        updateReceivedDays();
                        $('.received-day').val(parseInt(rd[2]));
                    }

                    $('.scan-status').val(data.status || "ACTIVE");
                }, "json");
            }

            $(document).on('change', '.scan-status', function () {
                let status = $(this).val();
                let modalBody = $(this).closest('.modal-body');
                let formInputs = $(this).closest('form').find('input, select');

                if (status === "MOVE") {
                    modalBody.find('#move-rack-section').show();
                    modalBody.find('#product-section').hide();
                } else {
                    modalBody.find('#move-rack-section').hide();
                    modalBody.find('#product-section').show();
                }

                if (status === "EMPTY") {
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
                event.preventDefault();
                $(".barcode-scan").prop("disabled", true);
                $(".sku-id").prop("hidden", true);
                $(".sku-itemcode").prop("disabled", true).prop("required", false);
                $(".search_sku").prop("hidden", false).prop("disabled", false);
            });

            $(".clear-btn").click(function () {
                $(".barcode-scan").prop("disabled", false);
                $(".sku-id").prop("hidden", false);
                $(".sku-itemcode").prop("disabled", false).prop("required", true);
                $(".search_sku").prop("hidden", true).prop("disabled", true);
            });

            $(document).on('click', '.view-btn', viewInventory);
            $(document).on('click', '.scan-btn', scanInventory);

            InventoryLevelList();

            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.barcode-scan').focus();
            });

            $('.scan-btn').click(function () {
                $('#scanModal')[0].reset();
            });

            $('#closeModalButton').click(function () {
                $('#scanModal').modal('hide');
            });

            $('#closeModalButton2').click(function () {
                $('#viewModal').modal('hide');
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
// }else{
//     header("Location: denied.php");
// }