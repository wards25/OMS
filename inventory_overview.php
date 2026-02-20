<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(147, $permission))
{
include_once("nav_inventory.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_inventory_count");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_inventory_count';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Count Overview</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa-solid fa-user-plus"></i> Add User</button> -->
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
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> User has been updated successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
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
                        <h6 class="m-0 font-weight-bold text-light">Rack Locations</h6> 
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" <?php if(in_array(149, $permission)){ echo 'data-target="#exportModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-download"></i> Export Data</a>&nbsp;|&nbsp; 
                            <a type="button" class="btn btn-sm btn-light" <?php if(in_array(149, $permission)){ echo 'href="inventory_overview_export.php"'; }else{ echo 'data-toggle="modal" data-target="#alertModal"'; } ?>><i class="fa-solid fa-list-ol"></i> Export Count Raw</a>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th hidden></th>
                                        <th>Rack</th>
                                        <th>Fin Count</th>
                                        <th>Log Count</th>
                                        <th>Match %</th>
                                        <th>Location</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn, "SELECT id, rack, location, 
                                                (COUNT(CASE WHEN status = 'MATCH' THEN 1 END) / COUNT(status)) * 100 AS match_percentage,
                                                AVG(COALESCE(fin_count, 0)) AS avg_fin_count,
                                                AVG(COALESCE(log_count, 0)) AS avg_log_count
                                            FROM tbl_inventory_rack 
                                            WHERE location IN (" . $location . ")
                                            AND inv_status = '1'
                                            GROUP BY rack, location
                                            ORDER BY CAST(rack AS UNSIGNED), rack");

                                        while ($row = mysqli_fetch_array($result)) {

                                        echo '<tr>';
                                            echo '<td hidden>'.$row['id'].'</td>';
                                            echo '<td>Rack '.$row['rack'].'</td>';
                                            $avg_fin_count = (number_format($row['avg_fin_count'], 2) * 100);
                                            echo '<td>'.$avg_fin_count.'%</td>';
                                            $avg_log_count = (number_format($row['avg_log_count'], 2) * 100);
                                            echo '<td>'.$avg_log_count.'%</td>';
                                            if(round($row['match_percentage'], 2) == 100){
                                                echo '<td class="table-success">'.round($row['match_percentage'], 2) . '%</td>';
                                            }else{
                                                echo '<td class="table-warning">'.round($row['match_percentage'], 2) . '%</td>';
                                            }
                                            echo '<td>'.$row['location'].'</td>';
                                            echo '<td><center>
                                                    <a type="button" name="view" class="btn btn-sm btn-info" 
                                                       onclick="window.open(\'inventory_overview_view.php?rack=' . $row['rack'] . '&location=' . $row['location']. '&percentage=' . round($row['match_percentage'], 2). '\', \'_blank\')">
                                                       <i class="fa-solid fa-bars-progress"></i> View Progress
                                                    </a>
                                                  </center></td>';
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