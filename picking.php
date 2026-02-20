<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(106, $permission))
{
include_once("nav_trips.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">For Picking</h4>
            <!-- <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" <?php if(in_array(77, $permission)){ echo 'data-target="#addModal"'; }else{ echo 'data-target="#alertModal"'; } ?>><i class="fa fa-plus"></i> Add SKU</button> -->
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Pick List has been submitted successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> You dont have access to this module.';
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
                        <h6 class="m-0 font-weight-bold text-light">Picking Details</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Upload Date</th>
                                        <th>Picklist No</th>
                                        <th>SKU</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Start DateTime</th>
                                        <th>Duration</th>
                                        <th>View</th>
                                        <th>Pick</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_trips_picklist WHERE picker = '$user' AND picker_end='' GROUP BY picklistno ORDER BY dtr,picker_assign DESC");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td><center>'.$row['dtr'].'</td>';
                                            echo '<td><center>'.$row['picklistno'].'</td>';
                                            
                                            $picklist = $row['picklistno'];
                                            $sku_query = mysqli_query($conn,"SELECT count(sku) as totalcount,sum(sysqty) as totalsum FROM tbl_trips_picking WHERE picklistno = '$picklist'");
                                            $fetch_sku = mysqli_fetch_assoc($sku_query);

                                            echo '<td><center>'.$fetch_sku['totalcount'].'</td>';
                                            echo '<td><center>'.$fetch_sku['totalsum'].'</td>';

                                            if(!empty($row['status'])){
                                                echo '<td><center><span class="badge badge-warning">' . $row['status'] . '</span></center></td>';
                                            }else{
                                                echo '<td></td>';
                                            }

                                            echo '<td><center>'.$row['picker_start'].'</td>';

                                            $givenTime = $row['picker_start'];

                                            // Check if picker_start is empty
                                            if (empty($givenTime)) {
                                                echo '<td><center>Not Yet Started</center></td>';
                                            } else {
                                                echo '<td class="table-warning"><center class="duration" data-start-time="' . $givenTime . '">Calculating...</center></td>';
                                            }
                                            ?>
                                            <td><center><a type="button" name="view" class="btn btn-sm btn-outline-warning" onclick='window.open("trips_view.php?picklistno=<?php echo $row['picklistno'];?>&dtr=<?php echo $row['dtr'];?>", "_blank")'><i class="fa fa-eye"></i></a></center></td>

                                            <td><center><a class="d-sm-inline-block btn btn-sm btn-info" name="update" type="button" 
                                                <?php 
                                                if(empty($row['picker_start'])){
                                                    if (in_array(104, $permission)){ 
                                                        echo 'data-toggle="modal" data-target="#pickingModal'.$row['picklistno'].$row['dtr'].'"'; 
                                                    }else{  
                                                        echo 'data-toggle="modal" data-target="#alertModal"'; 
                                                    } 
                                                }else{
                                                    echo 'href="picking_start.php?picker='.$user.'&picklistno='.$row['picklistno'].'&dtr='.$row['dtr'].'"';
                                                }
                                                ?>
                                                ><i class="fa-solid fa-people-carry-box"></i> Pick</a></center></td>

                                            <!-- Picking Modal-->
                                            <div class="modal fade" id="pickingModal<?php echo $row['picklistno'].$row['dtr'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-people-carry-box"></i>  Ready to Pick?</h6>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true"><small>×</small></span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">Select "Pick" below if you are ready to start the time of picking.</div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                            <a class="d-sm-inline-block btn btn-sm btn-info" type="button" href="picking_start.php?picker=<?php echo $user; ?>&picklistno=<?php echo $row['picklistno']; ?>&dtr=<?php echo $row['dtr']; ?>">Pick</a></center></td>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php   
                                        }
                                    ?>
                                        </tr>
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
        // Reset add modal button
        $('.add-btn').click(function(){
            $('#ModuleForm')[0].reset();
        });

        // Realtime duration clock
        function updateDurations() {
            const durationElements = document.querySelectorAll('.duration');

            durationElements.forEach(el => {
                const startTime = el.getAttribute('data-start-time');
                if (startTime) {
                    const startDateTime = new Date(startTime);
                    const now = new Date();

                    // Calculate the time difference
                    const diffMs = now - startDateTime;
                    const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
                    const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                    // Update the element
                    el.textContent = `${diffHrs} Hr(s) & ${diffMins} Min(s)`;
                }
            });
        }

        // Update durations every minute
        setInterval(updateDurations, 60000);

        // Initial update
        updateDurations();
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