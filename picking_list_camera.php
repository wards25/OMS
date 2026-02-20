<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(101, $permission))
{
include_once("nav_trips.php");

$picker = $_GET['picker'];
$picklistno = $_GET['picklistno'];
$dtr = $_GET['dtr'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Picklist: <?php echo $picklistno; ?></h4>
            <a href="picking.php" type="button" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm" ><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <hr>

        <style>
            thead tr {
                position: sticky;
                top: 0;
                z-index: 1; /* Ensure the header stays above table body rows */
            }
        </style>

                <!-- Include QuaggaJS -->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

                <!-- Alert Message -->
                <div id="barcode-alert" class="alert alert-warning alert-dismissible fade show" style="display: none;" role="alert">
                    <i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<strong>Warning!</strong> Barcode not found in this picklist.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div id="camera-container" style="width: 100%; max-height: 400px; display: none; background: black;"></div>
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                <thead style="position: sticky; top: 0; z-index: 1; background-color: white;">
                                    <tr>
                                        <th colspan="6">
                                            <button type="button" id="start-scan" class="btn btn-sm btn-primary btn-block">Start Scanning</button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="6">
                                            <button type="button" id="stop-scan" class="btn btn-sm btn-danger btn-block" style="display: none;">Stop Scanning</button>
                                        </th>
                                    </tr>
                                    <tr class="table-success text-center">
                                        <th>SKU</th>
                                        <th>Bar Code</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Rack</th>
                                        <th style="width:90px;">Picker Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="picking-table-body">
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_trips_picking WHERE picker = '$picker' AND picklistno = '$picklistno' AND dtr = '$dtr'");
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr data-barcode="' . $row['barcode'] . '">';
                                        echo '<td class="text-center">' . $row['sku'] . '</td>';
                                        echo '<td class="text-center">' . $row['barcode'] . '</td>';
                                        echo '<td class="text-center">' . $row['description'] . '</td>';
                                        echo '<td class="text-center">' . $row['sysqty'] . '</td>';
                                        echo '<td class="text-center">' . $row['racklocation'] . '</td>';
                                    ?>
                                    <td class="text-center">
                                        <input type="text" class="form-control form-control-sm" name="sku[<?php echo $row['id']; ?>]" value="<?php echo $row['sku']; ?>" hidden>
                                        <input type="number" class="form-control form-control-sm picker-qty" name="pickerqty[<?php echo $row['id']; ?>]" required maxlength="2" oninput="if(this.value.length > 2) this.value = this.value.slice(0, 2);" >
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

                <script>
                    const cameraContainer = document.getElementById('camera-container');
                    const alert = document.getElementById('barcode-alert');
                    const startScanButton = document.getElementById('start-scan');
                    const stopScanButton = document.getElementById('stop-scan');

                    // Start Barcode Scanning
                    startScanButton.addEventListener('click', function () {
                        cameraContainer.style.display = 'block';
                        stopScanButton.style.display = 'block';
                        startScanButton.style.display = 'none';

                        Quagga.init({
                            inputStream: {
                                type: 'LiveStream',
                                target: cameraContainer,
                                constraints: {
                                    facingMode: 'environment' // Use rear camera
                                }
                            },
                            decoder: {
                                readers: ['code_128_reader', 'ean_reader', 'ean_8_reader', 'upc_reader'] // Supported formats
                            }
                        }, function (err) {
                            if (err) {
                                console.error('Quagga initialization error:', err);
                                return;
                            }
                            console.log('Quagga initialized.');
                            Quagga.start();
                        });

                        // Barcode Detected
                        Quagga.onDetected(function (result) {
                            const barcode = result.codeResult.code.trim();
                            let found = false;

                            // Match barcode with table data
                            const rows = document.querySelectorAll('#picking-table-body tr');
                            rows.forEach(row => {
                                if (row.dataset.barcode === barcode) {
                                    found = true;
                                    const qtyInput = row.querySelector('.picker-qty');
                                    if (qtyInput) {
                                        qtyInput.focus(); // Focus the picker quantity input
                                    }
                                }
                            });

                            // Show alert if barcode not found
                            if (!found) {
                                alert.style.display = 'block';
                                setTimeout(() => {
                                    $(alert).fadeOut(500).slideUp(500);
                                }, 1000);
                            }
                        });
                    });

                    // Stop Barcode Scanning
                    stopScanButton.addEventListener('click', function () {
                        Quagga.stop();
                        cameraContainer.style.display = 'none';
                        stopScanButton.style.display = 'none';
                        startScanButton.style.display = 'block';
                    });
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