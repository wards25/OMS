<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_SERVER['REQUEST_URI'];

unset($_SESSION['previous_pages']);

//get data from checklist
$location = $_GET['location'];
$date = $_GET['date'];
$shift = $_GET['shift'];
$shift_type = $_GET['shift_type'];

if(isset($_SESSION['id']) && in_array(39, $permission))
{
include_once("nav_whse.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Office Attendance Report</h4>
            <a type="button" href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
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
                case 'add':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Asset has been added successfully.';
                    break;
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'incident':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been filed successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Asset exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Office Attendance Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <?php
                                $details_query = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' GROUP BY submitted_by");
                                $fetch_details = mysqli_fetch_array($details_query);
                                ?>
                                <tbody>
                                    <tr>
                                        <td class="table-primary"><b>Location</b></td>
                                        <td><?php echo $location; ?></td>
                                        <td class="table-primary"><b>Date</b></td>
                                        <td><?php echo date("F d, Y", strtotime($date)); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-primary"><b>Shift</b></td>
                                        <td colspan="3"><?php if($shift == 1){ echo '1st Shift'; }else{ echo '2nd Shift'; } ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Submitted By</b></td>
                                        <td><?php echo $fetch_details['submitted_by']; ?></td>
                                        <td class="table-warning"><b>Submitted At</b></td>
                                        <td><?php echo $fetch_details['submitted_at']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="table-warning"><b>Validated By</b></td>
                                        <td><?php if(empty($fetch_details['validated_by'])){ echo '<i class="text-danger">Not Validated</i>'; }else{ echo $fetch_details['validated_by']; }?></td>
                                        <td class="table-warning"><b>Validated At</b></td>
                                        <td><?php if($fetch_details['validated_at'] == ''){ echo '<i class="text-danger">Not Validated</i>'; }else{ echo $fetch_details['validated_at']; }?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Attendance</th>
                                        <th>Reason</th>
                                        <th>Uniform</th>
                                        <th>ID</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn,"SELECT * FROM tbl_ofattendance_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo '<tr>';
                                        echo '<td>'.$row['employee_name'].'</td>';
                                        echo '<td>'.$row['position'].'</td>';

                                        if($row['attendance'] == 1){
                                            echo '<td><center><span class="badge badge-success">Present</span></center></td>';
                                        }else{
                                            echo '<td><center><span class="badge badge-danger">Absent</span></center></td>';
                                        }
                                        echo '<td><center>'.$row['reason'].'</center></td>';

                                        if($row['uniform'] == 1){
                                            echo '<td><center><i class="fa fa-check fa-sm text-success"></i></center></td>';
                                        }else{
                                            echo '<td><center><i class="fa fa-times fa-sm text-danger"></i></center></td>';
                                        }

                                        if($row['identification'] == 1){
                                            echo '<td><center><i class="fa fa-check fa-sm text-success"></i></center></td>';
                                        }else{
                                            echo '<td><center><i class="fa fa-times fa-sm text-danger"></i></center></td>';
                                        }

                                        echo '<td><center>'.$row['remarks'].'</center></td>';
                                    }
                                    ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                <?php
                                // Average per position
                                $totalpos_query = mysqli_query($conn, "
                                    SELECT *,COUNT(*) as count FROM tbl_ofattendance_raw 
                                    WHERE date='$date' AND location='$location' 
                                        AND shift='$shift' AND shift_type='$shift_type'
                                    GROUP BY position
                                ");

                                while ($row = mysqli_fetch_array($totalpos_query)) {
                                    $position = $row['position'];

                                    // Attendance filtered by date/location/shift/shift_type/position and attendance=1
                                    $pos_query = mysqli_query($conn, "
                                        SELECT position, attendance 
                                        FROM tbl_ofattendance_raw 
                                        WHERE date='$date' AND location='$location' 
                                            AND shift='$shift' AND shift_type='$shift_type' 
                                            AND attendance='1' AND position='$position'
                                    ");
                                    $count_goodpos = mysqli_num_rows($pos_query);
                                    $count_totalpos = $row['count'];

                                    // Calculate and cap at 100%
                                    $averagepos = ($count_totalpos > 0) ? min(($count_goodpos / $count_totalpos) * 100, 100) : 0;
                                    $averagepos = number_format($averagepos, 2);
                                ?>
                                <tr>
                                    <td class="text-danger font-weight-bold"><i><?php echo $position . ' Score'; ?></i></td>
                                    <?php
                                    $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance Pos'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if ($averagepos <= $fetch_kpi['danger']) {
                                        echo '<td class="font-weight-bold text-light bg-danger"><center><i>' . $averagepos . '%</i></center></td>';
                                    } elseif ($averagepos <= $fetch_kpi['warning']) {
                                        echo '<td class="font-weight-bold text-dark bg-warning"><center><i>' . $averagepos . '%</i></center></td>';
                                    } else {
                                        echo '<td class="font-weight-bold text-light bg-success"><center><i>' . $averagepos . '%</i></center></td>';
                                    }
                                    ?>
                                </tr>
                                <?php
                                }
                                // Average total attendance score based on tbl_ofattendance_raw only
                                $good_query = mysqli_query($conn, "
                                    SELECT * FROM tbl_ofattendance_raw 
                                    WHERE date='$date' AND location='$location' 
                                        AND shift='$shift' AND shift_type='$shift_type' 
                                        AND attendance = '1'
                                ");
                                $count_good = mysqli_num_rows($good_query);

                                $total_query = mysqli_query($conn, "
                                    SELECT * FROM tbl_ofattendance_raw 
                                    WHERE date='$date' AND location='$location' 
                                        AND shift='$shift' AND shift_type='$shift_type'
                                ");
                                $count_total = mysqli_num_rows($total_query);

                                // Cap average at 100%
                                $average = ($count_total > 0) ? min(($count_good / $count_total) * 100, 100) : 0;
                                $average = number_format($average, 2);
                                ?>
                                <tr>
                                    <td class="text-danger font-weight-bold"><i>Total Attendance Score</i></td>
                                    <?php
                                    $kpi_query = mysqli_query($conn, "SELECT * FROM tbl_kpi WHERE metric = 'Office Attendance'");
                                    $fetch_kpi = mysqli_fetch_array($kpi_query);

                                    if ($average <= $fetch_kpi['danger']) {
                                        echo '<td class="font-weight-bold text-light bg-danger"><center><i>' . $average . '%</i></center></td>';
                                    } elseif ($average <= $fetch_kpi['warning']) {
                                        echo '<td class="font-weight-bold text-dark bg-warning"><center><i>' . $average . '%</i></center></td>';
                                    } else {
                                        echo '<td class="font-weight-bold text-light bg-success"><center><i>' . $average . '%</i></center></td>';
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
            $('#AssetForm')[0].reset();
            $('#IrForm')[0].reset();
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