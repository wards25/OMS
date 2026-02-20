<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(147, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

$rack = $_GET['rack'];
$location = $_GET['location'];
$percentage = $_GET['percentage'];
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Rack <?php echo $rack.': '; ?>
            <?php
            if($percentage == 100){
                echo '<span class="badge badge-sm badge-success">'.$percentage.'%</span>';
            }else{
                echo '<span class="badge badge-sm badge-warning">'.$percentage.'%</span>';
            }
            ?>
            </h4>
            <button class="input-group-addon btn btn-secondary btn-sm" onclick='window.close()'><i class="fa fa-sm fa-times"></i> Close</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> User has been added successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Username exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Count Progress</h6> 
                    </div>
                    <div class="card-body">
                        <!-- <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-download"></i> Export Data</a>
                        </div> -->
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-info">
                                        <th>Rack</th>
                                        <th>Col</th>
                                        <th>Level</th>
                                        <th>Pos</th>
                                        <th>Racklocation</th>
                                        <th>Fin Counter</th>
                                        <th>Log Counter</th>
                                        <th>Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT *
                                            FROM tbl_inventory_rack 
                                            WHERE rack = '$rack' 
                                            AND location = '$location'");

                                        while ($row = mysqli_fetch_array($result)) {

                                        if($row['status'] == 'MATCH'){
                                            echo '<tr class="table-success">';
                                        }else{
                                            echo '<tr class="table-warning">';
                                        }
                                            $groupno = $row['groupno'];

                                            $group_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_group WHERE groupno = '$groupno'");

                                            if (!$group_query) {
                                                echo '<td colspan="8" class="table-danger">Error fetching group data: ' . mysqli_error($conn) . '</td>';
                                                echo '</tr>';
                                                continue;
                                            }

                                            $fetch_group = mysqli_fetch_assoc($group_query);

                                            echo '<td>'.$row['rack'].'</td>';
                                            echo '<td>'.$row['col'].'</td>';
                                            echo '<td>'.$row['level'].'</td>';
                                            echo '<td>'.$row['pos'].'</td>';
                                            echo '<td>'.$row['racklocation'].'</td>';
                                            echo '<td>' . (!empty($fetch_group['fin_name']) ? htmlspecialchars($fetch_group['fin_name']) : '<i>Not Assigned</i>');
                                                if ($row['fin_count'] == '0') {
                                                    echo ' <span class="badge badge-danger d-inline">NOT ENCODED</span>';
                                                } else {
                                                    echo ' <span class="badge badge-success d-inline">ENCODED</span>';
                                                }
                                            echo '</td>';
                                            echo '<td>' . (!empty($fetch_group['log_name']) ? htmlspecialchars($fetch_group['log_name']) : '<i>Not Assigned</i>');
                                                if ($row['log_count'] == '0') {
                                                    echo ' <span class="badge badge-danger d-inline">NOT ENCODED</span>';
                                                } else {
                                                    echo ' <span class="badge badge-success d-inline">ENCODED</span>';
                                                }
                                            echo '</td>';
                                            echo '<td>'.$row['location'].'</td>';
                                        echo '</tr>';
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