<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url2 = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(133, $permission))
{
include_once("nav_clearing.php");

$tmno = $_GET['tmno'];
$dtr = $_GET['dtr'];

$details_query = mysqli_query($conn, "SELECT * FROM tbl_trips_tm WHERE tmno = '$tmno'");
$fetch_details = mysqli_fetch_assoc($details_query);
?>

    <!-- Begin Page Content -->
    <div class="container-fluid position-relative">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3"> 
            <h4 class="mb-0 text-gray-800">
                <?php 
                echo "TM-".$tmno; 

                if (!empty($fetch_details['dispatch_date'])) {
                    echo ' <span class="badge badge-sm badge-success">DISPATCHED</span>';
                }

                if ($fetch_details['clearing_status'] == 'CLEARED'){
                    echo '<div class="stamped-overlay">CLEARED</div>';
                }
                ?>
            </h4>
            <button class="input-group-addon btn btn-secondary btn-sm" onclick='window.close()'><i class="fa fa-sm fa-times"></i> Close</button>
        </div>

        <style>
            .stamped-overlay {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-20deg);
                color: red;
                font-size: 4rem;
                font-weight: bold;
                text-transform: uppercase;
                border: 6px solid red;
                padding: 20px 40px;
                background: rgba(255, 255, 255, 0.5);
                opacity: 0.15;
                mix-blend-mode: multiply;
                z-index: 999;
                pointer-events: none;
                border-radius: 20px; /* Makes edges rounded */
            }

            /* Adjust for smaller screens */
            @media (max-width: 768px) {
                .stamped-overlay {
                    font-size: 3rem; /* Smaller font size */
                    padding: 10px 20px; /* Smaller padding */
                    border: 4px solid red; /* Thinner border */
                    border-radius: 10px; /* More subtle rounding for small screens */
                }
            }
        </style>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Sorter has been assigned successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Trip number exists.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> PO has been updated successfully.';
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
                        <h6 class="m-0 font-weight-bold text-light">Trip Summary</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td style="background: #f2f2f2;"><b>Plate No:</td>
                                        <td><?php if(empty($fetch_details['plateno'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['plateno']; } ?></td>
                                        <td style="background: #f2f2f2;"><b>Truck Type:</td>
                                        <td><?php if(empty($fetch_details['trucktype'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['trucktype']; } ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f2f2f2;"><b>Driver:</td>
                                        <td><?php if(empty($fetch_details['driver'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['driver']; } ?></td>
                                        <td style="background: #f2f2f2;"><b>Sorter:</td>
                                        <td><?php if(empty($fetch_details['sorter'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['sorter']; } ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f2f2f2;"><b>Helper 1:</td>
                                        <td><?php if(empty($fetch_details['helper1'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['helper1']; } ?></td>
                                        <td style="background: #f2f2f2;"><b>Helper 2:</td>
                                        <td><?php if(empty($fetch_details['helper2'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['helper2']; } ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f2f2f2;"><b>Sorter:</td>
                                        <td><?php if(empty($fetch_details['sorter'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['sorter']; } ?></td>
                                        <td style="background: #f2f2f2;"><b>Dispatcher:</td>
                                        <td><?php if(empty($fetch_details['dispatcher'])){ echo '<i>Not Assigned</i>'; }else{ echo $fetch_details['dispatcher']; } ?></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f2f2f2;"><b>Dispatch Date:</td>
                                        <td><?php if(empty($fetch_details['dispatch_date'])){ echo '<i>Not Dispatched</i>'; }else{ echo $fetch_details['dispatch_date']; } ?></td>
                                        <td style="background: #f2f2f2;"><b>Return Date:</td>
                                        <td><?php if(empty($fetch_details['return_date'])){ echo '<i></i>'; }else{ echo $fetch_details['return_date']; } ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-danger"><center>*drop number is automatically created based on branch coordinates.*</center></small>
                    </div>

                    <!-- DataTales Example -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th hidden>Brcode</th>
                                        <th>SO #</th>
                                        <th>SI #</th>
                                        <th>Branch</th>
                                        <th>Cluster</th>
                                        <th>Status</th>
                                        <th>Clear</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_trips_raw WHERE tmno = '$tmno' AND dtr = '$dtr' GROUP BY sono,sino ORDER BY brcode");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                             echo '<td hidden><center>'.$row['brcode'].'</td>';
                                            echo '<td><center>'.$row['sono'].'</td>';
                                            echo '<td><center>'.$row['sino'].'</td>';
                                            echo '<td><center>'.$row['brname'].'</td>';
                                            echo '<td><center>'.$row['cluster'].'</td>';

                                            $sino = $row['sino'];
                                            $sino_query = mysqli_query($conn,"SELECT clearing_status FROM tbl_invoice WHERE sino = '$sino'");
                                            $fetch_sino = mysqli_fetch_assoc($sino_query);

                                            if(empty($fetch_details['clearing_status'])){
                                                echo '<td><center><span class="badge badge-warning">TO CLEAR</span></center></td>';
                                            }else{
                                                echo '<td><center><span class="badge badge-success">CLEARED</span></center></td>';
                                            }
                                            ?>
                                            <td>
                                                <center>
                                                    <a class="d-sm-inline-block btn btn-sm btn-info" name="update" type="button" 
                                                       <?php 
                                                            if (in_array(132, $permission)) {
                                                               echo 'onclick="window.open(\'clearing_summary.php?tmno=' . $row['tmno'] . '&sono=' . $row['sono'] . '&sino=' . $row['sino'] . '\', \'_blank\');"';
                                                            } else {
                                                               echo 'data-toggle="modal" data-target="#alertModal"';
                                                            }
                                                       ?>>
                                                       <i class="fa fa-stamp fa-sm"></i>
                                                    </a>
                                                </center>
                                            </td>
                                    <?php   
                                        }
                                    ?>
                                        </tr>
                                </tbody>
                            </table>
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
        // Reset add modal button
        $('.assign-btn').click(function(){
            $('#AssignForm')[0].reset();
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