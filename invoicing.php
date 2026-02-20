<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(119, $permission))
{
include_once("nav_trips.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">To Invoice</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(77, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add SKU</button> -->
        </div>
        <hr>

        <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 2000);

        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form"); 
            const inputs = form.querySelectorAll("input[type='text'], input[type='number']");
            
            // Prevent form submission when Enter is pressed inside any input field
            inputs.forEach(input => {
                input.addEventListener("keypress", function (event) {
                    if (event.key === "Enter") {
                        event.preventDefault(); // Stop form submission
                        let index = Array.from(inputs).indexOf(event.target);
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus(); // Move to next input field
                        }
                    }
                });
            });

            // Ensure form only submits via the button, not by pressing Enter
            form.addEventListener("submit", function(event) {
                if (document.activeElement.tagName === "INPUT") {
                    event.preventDefault(); // Prevent accidental submits
                }
            });
        });
        </script>

        <?php
        // Get status message
        if(!empty($_GET['status'])){
            switch($_GET['status']){
                case 'succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> SO/s has been invoiced successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> No selected SO.';
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
                        <h6 class="m-0 font-weight-bold text-light">Invoicing Details</h6> 
                    </div>
                    <form method="POST" action="invoicing_submit.php">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Upload Date</th>
                                        <th>TM #</th>
                                        <th>PO #</th>
                                        <th>SO #</th>
                                        <th>Invoice Status</th>
                                        <th>SI #</th>
                                        <th>Details</th>
                                        <!-- <th class="text-center">
                                            <input type="checkbox" id="selectAll">
                                        </th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT *, count(sku) as totalcount, sum(finalqty) as totalsum FROM tbl_trips_raw WHERE invoicing_status = 'FOR INVOICING' GROUP BY sono ORDER BY dtr DESC");
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td><center>' . $row['dtr'] . '</td>';
                                            echo '<td><center>' . $row['tmno'] . '</td>';
                                            echo '<td><center>' . $row['pono'] . '</td>';
                                            echo '<td><center>' . $row['sono'] . '</td>';

                                            if (!empty($row['invoicing_status'])) {
                                                echo '<td><center><span class="badge badge-warning">' . $row['invoicing_status'] . '</span></center></td>';
                                            } else {
                                                echo '<td></td>';
                                            }

                                            echo '<td><input type="number" class="form-control form-control-sm" name="invoice['.$row['sono'].']"></td>';
                                            echo '<input type="text" name="pono['.$row['sono'].']" value="'.$row['pono'].'" hidden>';
                                            echo '<input type="text" name="sono['.$row['sono'].']" value="'.$row['sono'].'" hidden>';
                                            echo '<input type="text" name="dtr['.$row['sono'].']" value="'.$row['dtr'].'" hidden>';
                                            echo '<input type="text" name="tmno['.$row['sono'].']" value="'.$row['tmno'].'" hidden>';

                                            echo '<td><center>
                                                    <a type="button" name="view" class="btn btn-sm btn-info" 
                                                       onclick="window.open(\'invoicing_view.php?sono=' . $row['sono'] . '\', \'_blank\')">
                                                       <i class="fa fa-list"></i>
                                                    </a>
                                                  </center></td>';

                                            // echo '<td class="align-middle"><center>
                                            //         <input type="checkbox" name="selected_sono[]" class="selectItem" value="' . $row['sono'] . '">
                                            //       </center></td>';
                                            echo '</tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                    <!-- End Table -->
                    <center><a type="button" class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#invoiceModal"><i class="fa fa-sm fa-check"></i> Mark as Invoiced</a></center>
                    <br>

                    <!-- Picking Modal-->
                    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-success">
                                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-receipt"></i> Invoicing</h6>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true"><small>×</small></span>
                                    </button>
                                </div>
                                <div class="modal-body">Do you want to mark sales orders as invoiced?</div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                    <button type="submit" name="submit" class="btn btn-success btn-sm"> Mark as Invoiced</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    </form>

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
        document.getElementById('selectAll').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.selectItem');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
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