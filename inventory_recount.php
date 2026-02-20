<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(143, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");
?>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Inventory Recon</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa-solid fa-plus"></i> Add SKU Rack</button> -->
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
                        <h6 class="m-0 font-weight-bold text-light">Select SKU</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-info">
                                        <th>SKU</th>
                                        <th>Count %</th>
                                        <th>Match %</th>
                                        <th>Enter</th>
                                    </tr>
                                </thead>
                                <tbody id="rackTable">
                                    <tr><td colspan="4">Loading data...</td></tr>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        function fetchMatchPercentage() {
            $.ajax({
                url: "inventory_count_table.php",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let tableBody = $("#rackTable");
                    tableBody.empty(); // Clear existing table rows

                    if (data.length > 0) {
                        data.forEach(row => {
                            let percentage = row.percentage + "%"; // Match %
                            let countPercentage = row.count_percentage ? row.count_percentage + "%" : "0%"; // Count %
                            let rack = row.rack;
                            let link = `<a class="btn btn-sm btn-success" href="inventory_column.php?groupno=${encodeURIComponent(row.groupno)}&rack=${encodeURIComponent(row.rack)}">
                                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                        </a>`;

                            // Apply class based on percentage
                            let rowClass = (row.percentage == 100) ? "table-success" : "table-warning";

                            tableBody.append(`<tr>
                                <td>${rack}</td>
                                <td>${countPercentage}</td>
                                <td class="${rowClass}">${percentage}</td>
                                <td>${link}</td>
                            </tr>`);
                        });
                    } else {
                        tableBody.append('<tr><td colspan="4">No rack assignment</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        }

        // Fetch data initially and every 5 seconds
        $(document).ready(function() {
            fetchMatchPercentage();
            setInterval(fetchMatchPercentage, 5000);
        });

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#UserForm')[0].reset();
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