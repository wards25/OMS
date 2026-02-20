<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url2 = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(101, $permission))
{
include_once("nav_trips.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">For Sorting</h4>
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
                case 'start':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Sorting has been started successfully.';
                    echo "<script>
                            setTimeout(function(){
                                window.location.href = window.location.pathname;
                            }, 2000); // Refresh after 2 seconds
                          </script>";
                    break;
                case 'succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Sorter has been assigned successfully.';
                    echo "<script>
                            setTimeout(function(){
                                window.location.href = window.location.pathname;
                            }, 1500);
                          </script>";
                    break;
                case 'end':
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
                        <h6 class="m-0 font-weight-bold text-light">Sorting Details</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Upload Date</th>
                                        <th>TM</th>
                                        <th>Truck Type</th>
                                        <th>Total Qty</th>
                                        <th>Start DateTime</th>
                                        <th>Duration</th>
                                        <th>Sorter</th>
                                        <th>Status</th>
                                        <th>Print</th>
                                        <th>Assign</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $result = mysqli_query($conn,"SELECT * FROM tbl_trips_tm WHERE sorter_end = '' ORDER BY dtr DESC");
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td><center>'.$row['dtr'].'</td>';
                                            echo '<td><center>'.$row['tmno'].'</td>';
                                            echo '<td><center>'.$row['trucktype'].'</td>';

                                            $tmno = $row['tmno'];
                                            $sku_query = mysqli_query($conn,"SELECT *,sum(finalqty) as totalsum FROM tbl_trips_raw WHERE tmno = '$tmno'");
                                            $fetch_sku = mysqli_fetch_assoc($sku_query);

                                            echo '<td><center>'.$fetch_sku['totalsum'].'</td>';

                                            echo '<td><center>'.$row['sorter_start'].'</td>';

                                            $givenTime = $row['sorter_start'];

                                            // Check if picker_start is empty
                                            if (empty($givenTime)) {
                                                echo '<td><center>Not Yet Started</center></td>';
                                            } else {
                                                echo '<td class="table-warning"><center class="duration" data-start-time="' . $givenTime . '">Calculating...</center></td>';
                                            }

                                            echo '<td><center>'.$row['sorter'].'</td>';

                                            if(!empty($row['sorter_start'])){
                                                echo '<td><center><a class="d-sm-inline-block btn btn-sm btn-info" name="update" type="button" data-toggle="modal" data-target="#EndModal'.$row['tmno'].$row['dtr'].'"><i class="fa-solid fa-stopwatch"></i> End Sort</a></center></td>';
                                            }else{
                                                echo '<td><center><a class="d-sm-inline-block btn btn-sm btn-info" name="update" type="button"';
                                                if (in_array(104, $permission)){ 
                                                    echo 'data-toggle="modal" data-target="#pickingModal'.$row['tmno'].$row['dtr'].'"'; 
                                                }else{  
                                                    echo 'data-toggle="modal" data-target="#alertModal"'; 
                                                } 
                                                echo '><i class="fa-solid fa-stopwatch"></i> Start Sort</a></center></td>';
                                            }

                                            if(empty($row['sorter'])){
                                                echo '<td><center><button class="btn btn-sm btn-secondary" disabled><i class="fa fa-print"></i></button></center></td>';
                                            }else{
                                                if (in_array(116, $permission)) { 
                                                    echo '<td><center><a type="button" name="view" class="btn btn-sm btn-success" onclick=\'window.open("sorting_print.php?tmno='.$row['tmno'].'&dtr='.$row['dtr'].'&sorter='.$row['sorter'].'", "_blank")\'><i class="fa fa-print"></i></a></center></td>';
                                                } else {
                                                    echo '<td><center><button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#alertModal"><i class="fa fa-print"></i></button></td></center>';
                                                }
                                            }

                                            if(empty($row['sorter_start'])){
                                                echo '<td><button class="d-sm-inline-block btn btn-info btn-sm" data-toggle="modal" data-target="#SorterModal"><i class="fa fa-sm fa-boxes"></i></button></td>';
                                            }else{
                                                echo '<td><button class="d-sm-inline-block btn btn-secondary btn-sm" disabled><i class="fa fa-sm fa-boxes"></i></button></td>';
                                            }
                                            ?>

                                            <!-- Assign Modal -->
                                            <div class="modal fade" id="SorterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-boxes fa-sm"></i> Assign / Update Sorter</h6>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true"><small>×</small></span>
                                                            </button>
                                                        </div>
                                                        <form method="POST" action="trips_assign.php" id="AssignForm">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <div class="form-row">
                                                                        <div class="col-12">
                                                                            <label for="exampleInputAddress mb-1">Sorter: </label>
                                                                            <?php
                                                                            $dtnow = date("Y-m-d");
                                                                            if(empty($row['sorter_start'])){
                                                                                $picker_query = "SELECT * FROM tbl_attendance_raw WHERE date = '$dtnow' AND position = 'Picker' AND attendance = '1' AND is_validated = '1' ORDER BY employee_name";
                                                                                $result_picker = $conn->query($picker_query);
                                                                                if ($result_picker->num_rows > 0) {
                                                                                    $options = mysqli_fetch_all($result_picker, MYSQLI_ASSOC);
                                                                                    echo '<select class="form-control form-control-sm" name="sorter">';
                                                                                    if(empty($row['sorter'])){
                                                                                        echo '<option value=""></option>';
                                                                                    }else{
                                                                                        echo '<option value="'.$row['sorter'].'">'.$row['sorter'].' (Assigned)</option>';
                                                                                    }
                                                                                        foreach ($options as $option) {
                                                                                            echo '<option value="' . $option['employee_name'] . '">' . $option['employee_name'] . '</option>';
                                                                                        }
                                                                                    echo '</select>';
                                                                                    }
                                                                            }else{
                                                                                echo '<input type="text" class="form-control form-control-sm" name="sorter" value="'.$row['sorter'].'" disabled>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="text" value="<?php echo $tmno; ?>" name="tmno" hidden>
                                                                <input type="text" class="form-control form-control-sm" name="url" value="<?php echo $url2; ?>" hidden>
                                                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                                <button class="btn btn-info btn-sm" name="submit" type="submit">Assign / Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Picking Modal-->
                                            <div class="modal fade" id="pickingModal<?php echo $row['tmno'].$row['dtr'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-stopwatch"></i>  Ready to Sort?</h6>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true"><small>×</small></span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">Select "Sort" below if you are ready to start the time of sorting.</div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                            <a class="d-sm-inline-block btn btn-sm btn-info" type="button" href="sorting_start.php?sorter=<?php echo $user; ?>&tmno=<?php echo $row['tmno']; ?>&dtr=<?php echo $row['dtr']; ?>&start=1">Sort</a></center></td>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
 
                                            <!-- End Modal-->
                                            <div class="modal fade" id="EndModal<?php echo $row['tmno'].$row['dtr'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info">
                                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-stopwatch"></i>  Ready to End Sort?</h6>
                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true"><small>×</small></span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">Select "End Sort" below if you are ready to end the time of sorting.</div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                            <a class="d-sm-inline-block btn btn-sm btn-info" type="button" href="sorting_start.php?sorter=<?php echo $user; ?>&tmno=<?php echo $row['tmno']; ?>&dtr=<?php echo $row['dtr']; ?>&start=0">End Sort</a></center></td>
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