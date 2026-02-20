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

if(isset($_SESSION['id']) && in_array(59, $permission))
{
include_once("nav_admin.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Facility Report</h4>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Facility has been added successfully.';
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
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Facility name exists.';
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
                        <h6 class="m-0 font-weight-bold text-light">General Facilities Form</h6> 
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <?php
                                $details_query = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' GROUP BY submitted_by");
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
                                        <th>Facility</th>
                                        <th>Condition</th>
                                        <th>Findings</th>
                                        <th>Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type'");
                                    while($row = mysqli_fetch_assoc($result)){
                                        echo '<tr>';
                                        echo '<td class="align-middle">'.$row['location_name'].'<a href="#" class="text-warning float-right" data-toggle="modal" data-target="#zone'.$row['id'].'"><i class="fa fa-info-circle fa-sm"></i></a></td>';

                                        if($row['cond'] == 1){
                                            echo '<td class="align-middle"><center><i class="fa fa-check fa-sm text-success"></i></td>';
                                        }else{
                                            echo '<td class="align-middle"><center><i class="fa fa-times fa-sm text-danger"></i></td>';
                                        }
                                        echo '<td class="align-middle"><center>'.$row['findings'].'</td>';

                                        if(empty($row['path'])){
                                            echo '<td class="align-middle"><center><button class="d-sm-inline-block btn btn-info btn-sm" disabled><i class="fa fa-sm fa-paperclip"></i> Image</button></center></td>';
                                        }else{
                                            echo '<td class="align-middle"><center><a class="d-sm-inline-block btn btn-info btn-sm" data-toggle="modal" data-target="#ImageModal'.$row['id'].'"><i class="fa fa-sm fa-paperclip"></i> Image</a></center></td>';
                                        }

                                        $subject = $row['location_name'];
                                        $year = '/'.date('Y', strtotime($date));
                                        $month = '/'.date('F', strtotime($date));
                                        $date_folder = '/'.date('mdY', strtotime($date));

                                        if($shift == 1){
                                            $shift_folder = '/1st Shift';
                                        }else{
                                            $shift_folder = '/2nd Shift';
                                        }

                                        if($location == 'CAINTA'){
                                            $location_name = 'CNT';
                                        }else if($location == 'CDO'){
                                            $location_name = 'CDO';
                                        }else if($location == 'CEBU'){
                                            $location_name = 'CEB';    
                                        }else if($location == 'DAVAO'){
                                            $location_name = 'DAV';
                                        }else if($location == 'ILOILO'){
                                            $location_name = 'ILO';
                                        }else if($location == 'PANGASINAN'){
                                            $location_name = 'PAG';
                                        }else if($location == 'MARIKINA'){
                                            $location_name = 'MAR';
                                        }else{

                                        }

                                        if($shift_type == 1){
                                            $shift_type_folder = 'BEG';
                                        }else{
                                            $shift_type_folder = 'END';
                                        }

                                        $new_file_name = $subject.'-'.$location_name.'_'.$date.'-'.$shift.'_'.$shift_type_folder.'.jpg';
                                        //$filelocation = "C:/public/www/f325.ramosco.net/filepicture/dbapps/";
                                        $filelocation = 'upload/'.$location.''.$year.''.$month.''.$date_folder.''.$shift_folder.'/generalfacilities/';
                                        $image_path = $filelocation.$new_file_name;
                                        ?>

                                        <div class="modal fade" id="ImageModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info">
                                                        <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> Attachment</h6>
                                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true"><small>×</small></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body"><img class="img-fluid" src="<?php echo $image_path; ?>"></div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
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
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                <tbody>
                                    <?php
                                    //average total
                                    $good_query = mysqli_query($conn,"SELECT * FROM tbl_facility_location WHERE location='$location' AND is_active=1");
                                    $count_good = mysqli_num_rows($good_query);
                                    $score_query = mysqli_query($conn,"SELECT * FROM tbl_facility_raw WHERE date='$date' AND location='$location' AND shift='$shift' AND shift_type='$shift_type' AND cond=1");
                                    $count_score = mysqli_num_rows($score_query);
                                    $average = (($count_score / $count_good) * 100);
                                    $average = number_format($average,2);
                                    ?>
                                    <tr>
                                        <td class="text-danger font-weight-bold"><i>Total Facility Score</i></td>
                                        <?php
                                        $kpi_query = mysqli_query($conn,"SELECT * FROM tbl_kpi WHERE metric = 'General Facilities'");
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

                <?php
                $desc_query = mysqli_query($conn,"SELECT * FROM tbl_facility_location");
                while($fetch_desc = mysqli_fetch_array($desc_query)){
                ?>
                <!-- c1 Modal-->
                <div class="modal fade" id="zone<?php echo $fetch_desc['id'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa fa-info fa-sm"></i> <?php echo $fetch_desc['location_name']; ?></h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                    <tbody>
                                        <?php
                                        $location_id = $fetch_desc['id'];
                                        $number = 0;
                                        $info_query = mysqli_query($conn,"SELECT * FROM tbl_facility_description WHERE location_id = '$location_id'");
                                        while($fetch_info = mysqli_fetch_array($info_query)){
                                            $number ++;
                                            echo '<tr>';
                                            echo '<td>'.$number.'. '.$fetch_info['description'].'</td>';
                                        }
                                        ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
                ?>
                
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