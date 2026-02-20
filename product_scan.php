<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(79, $permission))
{
include_once("nav_product.php");
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Scan Product</h4>
        </div>
        <hr>
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Select Level</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="alert-container"></div>
                        <!-- Product Details -->
                        <div id="product-section">
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
                        <div class="table-responsive scanned-product-table" style="display: none;">
                            <table class="table table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="font-weight-bold bg-info text-white align-middle">PRODUCT INFORMATION</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">SKU Code</td>
                                        <td class="product-code"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">UOM</td>
                                        <td class="product-uom"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">Barcode</td>
                                        <td class="product-barcode"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">Item Barcode</td>
                                        <td class="product-itembarcode"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">Description</td>
                                        <td class="product-description"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">Principal</td>
                                        <td class="product-principal"></td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold table-warning align-middle">Company</td>
                                        <td class="product-vendorcode"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="product-update-wrapper mt-3 text-center" style="display: none;">
                            <a class="btn btn-sm btn-outline-warning product-update-btn">
                                <i class="fa fa-cog fa-sm"></i> Update
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <center><small><i>*only ACTIVE products can be scanned*</i></small></center>

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
        $(document).ready(function () {
            let barcodeTimer;

            $(document).on('input', '.barcode-scan', function () {
                clearTimeout(barcodeTimer);

                let inputField = $(this);
                let barcode = inputField.val().trim();
                let modalBody = inputField.closest('.modal-body').length ? inputField.closest('.modal-body') : $('body');

                if (barcode.length < 5) return;

                barcodeTimer = setTimeout(() => {
                    fetchBarcodeDetails(barcode, modalBody);
                }, 500);
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

                            $('.product-id').text(response.id);
                            $('.product-code').text(`${response.itemcode}`);
                            $('.product-uom').text(`${response.uom}`);
                            $('.product-description').text(response.description);
                            $('.product-itembarcode').text(response.itembarcode);
                            $('.product-barcode').text(response.barcode);
                            $('.product-vendorcode').text(response.vendorcode);
                            $('.product-principal').text(response.principal);
                            $('.product-isactive').text(response.is_active == '1' ? 'Yes' : 'No');

                            //Update the external button
                            const hasPermission = <?php echo json_encode(in_array(78, $permission)); ?>;
                            if (hasPermission) {
                                $('.product-update-btn')
                                    .attr('href', `product_update.php?update_id=${response.id}&type='scan'`)
                                    .removeAttr('data-toggle data-target');
                            } else {
                                $('.product-update-btn')
                                    .removeAttr('href')
                                    .attr('data-toggle', 'modal')
                                    .attr('data-target', '#alertModal');
                            }

                            $('.product-update-wrapper').show();   // Show the button
                            $('.scanned-product-table').show();    // Show the table
                        } else {
                            showAlert(response.message, 'warning', modalBody);
                            clearTableFields();
                        }
                    },
                    error: function (xhr) {
                        showAlert(`AJAX error: ${xhr.responseText}`, "danger", modalBody);
                        clearTableFields();
                    }
                });
            }

            function clearTableFields() {
                $('.product-id, .product-code, .product-description, .product-itembarcode, .product-barcode, .product-vendorcode, .product-principal, .product-isactive').text('');
                $('.scanned-product-table, .product-update-wrapper').hide(); // Hide table & button
            }


            $(document).on('click', '.clear-btn', function () {
                let modalBody = $(this).closest('.modal-body').length ? $(this).closest('.modal-body') : $('body');
                modalBody.find('.barcode-scan, .sku-id, .sku-itemcode, .sku-uom').val('');
                modalBody.find('.alert-container').html('');
                clearTableFields();
                modalBody.find('.barcode-scan').focus();
            });

            function showAlert(message, type, container) {
                const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b>
                    ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>`;
                container.find('.alert-container').html(alertHtml);
            }
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