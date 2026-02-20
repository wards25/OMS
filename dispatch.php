<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
// include_once("dbconnect_ctms.php");
$user = $_SESSION['name'];
$url2 = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(124, $permission))
{
include_once("nav_trips.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">For Dispatch</h4>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Driver has been assigned/updated successfully.';
                    echo "<script>
                            setTimeout(function(){
                                window.location.href = window.location.pathname;
                            }, 2000); // Refresh after 2 seconds
                          </script>";
                    break;
                case 'dispatch':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Trip has been dispatched successfully.';
                    echo "<script>
                            setTimeout(function(){
                                window.location.href = window.location.pathname;
                            }, 2000); // Refresh after 2 seconds
                          </script>";
                    break;
                case 'err':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Sorting has been ended successfully.';
                    echo "<script>
                            setTimeout(function(){
                                window.location.href = window.location.pathname;
                            }, 2000); // Refresh after 2 seconds
                          </script>";
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
                        <h6 class="m-0 font-weight-bold text-light">Dispatch Details</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Upload Date</th>
                                        <th>TM</th>
                                        <th>Plate #</th>
                                        <th>Truck</th>
                                        <th>Total Qty</th>
                                        <th>TM Status</th>
                                        <th>Details</th>
                                        <th>Assign</th>
                                        <th>Dispatch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_trips_tm WHERE return_date = '' ORDER BY dtr");
                                        while($row = mysqli_fetch_assoc($result)) {

                                            echo '<tr>';
                                            echo '<td><center>'.$row['dtr'].'</td>';
                                            echo '<td><center>'.$row['tmno'].'</td>';
                                            echo '<td class="table-warning"><center>'.$row['plateno'].'</td>';

                                            $tmno = $row['tmno'];
                                            $picklist_query = mysqli_query($conn,"SELECT count(picklistno),sum(totalqty) FROM tbl_trips_picklist WHERE tmno = '$tmno'");
                                            $fetch_picklist = mysqli_fetch_assoc($picklist_query);

                                            echo '<td><center>'.$row['trucktype'].'</td>';
                                            echo '<td><center>'.$fetch_picklist['sum(totalqty)'].'</td>';

                                            echo '<td><center>';
                                                if (!empty($row['driver'])) {
                                                    echo '<span class="badge badge-success d-inline">ASSIGNED</span> ';
                                                } else {
                                                    echo '<span class="badge badge-danger d-inline">NOT ASSIGNED</span> ';
                                                }

                                                if (!empty($row['dispatch_date'])) {
                                                    echo '<span class="badge badge-success d-inline">DISPATCHED</span>';
                                                } else {
                                                    echo '<span class="badge badge-danger d-inline">NOT DISPATCHED</span>';
                                                }
                                            echo '</center></td>';

                                            echo '<td><a class="d-sm-inline-block btn btn-sm btn-info" name="update" type="button"';
                                                   if (in_array(123, $permission)) {
                                                       echo 'onclick="window.open(\'trips_summary.php?tmno=' . $row['tmno'] . '&dtr=' . $row['dtr'] . '\', \'_blank\');"';
                                                   } else {
                                                       echo 'data-toggle="modal" data-target="#alertModal"';
                                                   }
                                            echo '><i class="fa fa-list fa-sm"></i></a></td>';

                                            if(in_array(124, $permission)){
                                                echo '<td><button class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#assignModal"><i class="fa-solid fa-id-card"></i> Assign</button></td>';
                                            }else{
                                                echo '<td><button class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#alertModal"><i class="fa-solid fa-id-card"></i> Assign</button></td>';
                                            }

                                            if(empty($row['ic_end']) && empty($row['driver'])){
                                                echo '<td><center><button class="d-sm-inline-block btn btn-sm btn-secondary" disabled><i class="fa fa-truck fa-sm"></i> Dispatch</button></td>';
                                            }else{
                                            ?>
                                                <td>
                                                    <center>
                                                        <a class="d-sm-inline-block btn btn-sm btn-warning text-dark" name="update" type="button" 
                                                           <?php 
                                                               if (in_array(123, $permission)) {
                                                                   echo 'onclick="window.open(\'trips_summary.php?tmno=' . $row['tmno'] . '&dtr=' . $row['dtr'] . '\', \'_blank\');"';
                                                               } else {
                                                                   echo 'data-toggle="modal" data-target="#alertModal"';
                                                               }
                                                           ?>>
                                                           <i class="fa fa-truck-fast fa-sm"></i> Dispatch
                                                        </a>
                                                    </center>
                                                </td>
                                    <?php       
                                            }
                                    ?>

                                    <!-- Dispatch Modal -->
                                    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-id-card"></i> Assign Dispatch</h6>
                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true"><small>×</small></span>
                                                    </button>
                                                </div>
                                                <form method="POST" action="trips_dispatch_assign.php" id="AssignForm">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <div class="form-row">
                                                                <div class="col-6">
                                                                    <label for="exampleInputAddress mb-1">Plate No.: </label>
                                                                    <?php
                                                                    if(empty($row['dispatch_date'])){

                                                                        if($row['trucktype'] == '6 Wheeler' || $row['trucktype'] == '6 WHEELER'){
                                                                            $tt = '6W';
                                                                        }else if ($row['trucktype'] == '4 Wheeler' || $row['trucktype'] == '4 WHEELER'){
                                                                            $tt = '4W';
                                                                        }else{
                                                                            $tt = 'WV';
                                                                        }

                                                                        $plateno_query = "SELECT * FROM trucks WHERE truck_type = '$tt' AND status = 'ACTIVE' GROUP BY plate_no ORDER BY plate_no";
                                                                        $result_plateno = $ctms_conn->query($plateno_query);
                                                                        if ($result_plateno->num_rows > 0) {
                                                                            $options = mysqli_fetch_all($result_plateno, MYSQLI_ASSOC);
                                                                            echo '<select class="form-control form-control-sm" name="plateno" required>';
                                                                            foreach ($options as $option) {
                                                                                echo '<option value="' . $option['plate_no'] . '">' . $option['plate_no'] . '</option>';
                                                                            }
                                                                            echo '</select>';
                                                                        }
                                                                        
                                                                    }else{
                                                                        echo '<input type="text" class="form-control form-control-sm" name="plateno" value="'.$row['plateno'].'" disabled>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label for="exampleInputAddress mb-1">Profiles: </label>
                                                                    <br>
                                                                    <button type="button" class="btn btn-block btn-secondary btn-sm" href="trips_trucks.php" disabled>View Trucks</button>
                                                                </div>
                                                            </div>
                                                            <div class="form-row">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-row">
                                                            <div class="col-12">
                                                                <label for="exampleInputAddress mb-1">Driver: </label>
                                                                <?php
                                                                if(empty($row['dispatch_date'])){
                                                                    $driver_query = "SELECT * FROM tbl_employees WHERE position = 'Driver' GROUP BY employee_name ORDER BY employee_name";
                                                                    $result_driver = $conn->query($driver_query);
                                                                    if ($result_driver->num_rows > 0) {
                                                                        $options = mysqli_fetch_all($result_driver, MYSQLI_ASSOC);
                                                                        echo '<select class="form-control form-control-sm" name="driver" required>';
                                                                        foreach ($options as $option) {
                                                                            echo '<option value="' . $option['employee_name'] . '">' . $option['employee_name'] . '</option>';
                                                                        }
                                                                        echo '</select>';
                                                                    }
                                                                }else{
                                                                    echo '<input type="text" class="form-control form-control-sm" name="driver" value="'.$row['driver'].'" disabled>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="form-row">
                                                            <div class="col-6">
                                                                <label for="exampleInputAddress mb-1">Helper 1: </label>
                                                                <?php
                                                                if(empty($row['dispatch_date'])){
                                                                    $helper_query = "SELECT * FROM tbl_employees WHERE position = 'Helper' GROUP BY employee_name ORDER BY employee_name";
                                                                    $result_helper = $conn->query($helper_query);
                                                                    if ($result_helper->num_rows > 0) {
                                                                        $options = mysqli_fetch_all($result_helper, MYSQLI_ASSOC);
                                                                        echo '<select class="form-control form-control-sm" name="helper1" required>';
                                                                        foreach ($options as $option) {
                                                                            echo '<option value="' . $option['employee_name'] . '">' . $option['employee_name'] . '</option>';
                                                                        }
                                                                        echo '</select>';
                                                                    }
                                                                }else{
                                                                    echo '<input type="text" class="form-control form-control-sm" name="helper" value="'.$row['helper1'].'" disabled>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="col-6">
                                                                <label for="exampleInputAddress mb-1">Helper 2: </label>
                                                                <?php
                                                                if(empty($row['dispatch_date'])){
                                                                    $helper_query = "SELECT * FROM tbl_employees WHERE position = 'Helper' GROUP BY employee_name ORDER BY employee_name";
                                                                    $result_helper = $conn->query($helper_query);
                                                                    if ($result_helper->num_rows > 0) {
                                                                        $options = mysqli_fetch_all($result_helper, MYSQLI_ASSOC);
                                                                        echo '<select class="form-control form-control-sm" name="helper2">';
                                                                        echo '<option value=""></option>';
                                                                        foreach ($options as $option) {
                                                                            echo '<option value="' . $option['employee_name'] . '">' . $option['employee_name'] . '</option>';
                                                                        }
                                                                        echo '</select>';
                                                                    }
                                                                }else{
                                                                    echo '<input type="text" class="form-control form-control-sm" name="helper" value="'.$row['helper2'].'" disabled>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="text" value="<?php echo $row['tmno']; ?>" name="tmno" hidden>
                                                    <input type="text" value="<?php echo $row['dtr']; ?>" name="dtr" hidden>
                                                    <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url2; ?>" hidden>
                                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                    <button class="btn btn-warning btn-sm text-dark" name="submit" type="submit">Assign</button>
                                                </div>
                                            </form>
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

        // Reset add modal button
        $('.assign-btn').click(function(){
            $('#AssignForm')[0].reset();
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