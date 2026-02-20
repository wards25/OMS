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

if(isset($_SESSION['id']) && in_array(35, $permission))
{
include_once("nav_whse.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">WH Zone Report</h4>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Zone has been added successfully.';
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
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Zone name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">Zone Assignment Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <?php
                                $details_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' GROUP BY submitted_by");
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
                                        <td><?php if($shift == 1){ echo '1st Shift'; }else{ echo '2nd Shift'; } ?></td>
                                        <td class="table-primary"><b>Shift Type</b></td>
                                        <td><?php if($shift_type == 1){ echo 'OPENING'; }else{ echo 'CLOSING'; } ?></td>
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
                                        <th>Zone Name</th>
                                        <th>Assigned to</th>
                                        <th colspan="2">Segregated Items <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c1Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">FEFO <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c2Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Cleanliness <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c3Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Racks <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c4Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                        <th colspan="2">Space <a href="#" class="text-warning float-right" data-toggle="modal" data-target="#c5Modal"><i class="fa fa-info-circle fa-sm"></i></a></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn, "SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td class="align-middle">' . $row['location_name'] . '</td>';
                                        echo '<td class="align-middle">' . $row['employee_name'] . '</td>';

                                        // Loop through checklist columns (c1 to c5)
                                        for ($i = 1; $i <= 5; $i++) {
                                            $col = 'c' . $i;
                                            $remarkCol = $col . '_remarks';

                                            if ($row[$col] == 1) {
                                                echo '<td class="align-middle text-center" colspan="2"><i class="fa fa-check fa-sm text-success"></i></td>';
                                            } else {
                                                echo '<td class="align-middle text-center"><i class="fa fa-times fa-sm text-danger"></i></td>';
                                                echo '<td class="text-center">' . htmlspecialchars($row[$remarkCol]) . '</td>';
                                            }
                                        }

                                        echo '</tr>';
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
                                    //average per position
                                    $pos_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' GROUP BY location_name");
                                    while($fetch_pos = mysqli_fetch_array($pos_query)){
                                        $count_goodpos = $fetch_pos['c1'] + $fetch_pos['c2'] + $fetch_pos['c3'] + $fetch_pos['c4'] + $fetch_pos['c5'];

                                    $averagepos = (($count_goodpos / 5) * 100);
                                    $averagepos = number_format($averagepos,2);
                                    ?>
                                    <tr>
                                        <td class="text-danger font-weight-bold"><i><?php echo $fetch_pos['location_name'].' Score'; ?></i></td>
                                        <?php
                                        $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Zone Pos'");
                                        $fetch_kpi = mysqli_fetch_array($kpi_query);

                                        if($averagepos <= $fetch_kpi['danger']){
                                            echo '<td class="font-weight-bold text-light bg-danger"><center><i>'.$averagepos.'%</i></td>';
                                        }else if($averagepos <= $fetch_kpi['warning']){
                                            echo '<td class="font-weight-bold text-dark bg-warning"><center><i>'.$averagepos.'%</i></td>';
                                        }else{
                                            echo '<td class="font-weight-bold text-light bg-success"><center><i>'.$averagepos.'%</i></td>';
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                    }
                                    //average total
                                    $good_query = mysqli_query($conn,"SELECT * FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
                                    $count_good = mysqli_num_rows($good_query);
                                    $count_total = $count_good * 5;
                                    $score_query = mysqli_query($conn,"SELECT sum(c1+c2+c3+c4+c5) as totalscore FROM tbl_zone_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
                                    $fetch_score = mysqli_fetch_array($score_query);
                                    $count_score = $fetch_score['totalscore'];
                                    $average = (($count_score / $count_total) * 100);
                                    $average = number_format($average,2);
                                    ?>
                                    <tr>
                                        <td class="text-danger font-weight-bold"><i>Total WH Zone Score</i></td>
                                        <?php
                                        $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'WH Zone'");
                                        $fetch_kpi = mysqli_fetch_array($kpi_query);

                                        if($average <= $fetch_kpi['danger']){
                                            echo '<td class="font-weight-bold text-light bg-danger"><center><i>'.$average.'%</i></td>';
                                        }else if($average <= $fetch_kpi['warning']){
                                            echo '<td class="font-weight-bold text-dark bg-warning"><center><i>'.$average.'%</i></td>';
                                        }else{
                                            echo '<td class="font-weight-bold text-light bg-success"><center><i>'.$average.'%</i></td>';
                                        }
                                        ?>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <!-- c1 Modal-->
                <div class="modal fade" id="c1Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Segregated Items</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Non Conformance Items (Products with Leaks and Damage)<br>
                                2. Break Case
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c2 Modal-->
                <div class="modal fade" id="c2Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> FEFO</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                First Expiration First Out
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c3 Modal-->
                <div class="modal fade" id="c3Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Cleanliness</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. No trash in the area<br>
                                2. Waste bin empty<br>
                                3. Pallet and all equipments stored away<br>
                                4. Items are free from dust<br>
                                5. No leaks on floor & on products
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c4 Modal-->
                <div class="modal fade" id="c4Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Racks</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Intact and Safe to hold stocks<br>
                                2. Free from weather or mechanical damage
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- c5 Modal-->
                <div class="modal fade" id="c5Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-info-circle fa-sm"></i> Space</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                1. Adequate space to maneuver<br>
                                2. Free from weather or mechanical damage<br>
                                3. Lighting sufficient<br>
                                4. Leaks or cracks on the facilities
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                
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