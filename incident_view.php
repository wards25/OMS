<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$url = $_SERVER['REQUEST_URI'];

if(isset($_SESSION['id']) && in_array(19, $permission))
{
include_once("nav_forms.php");

if(isset($_POST['incident_id'])){
    $incident_id = $_POST['incident_id'];
    $incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE id = '$incident_id'");
}else if(isset($_GET['ir'])){
    $incident_id = $_GET['ir'];
    $incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE ref_no = '$incident_id'");
}else{
    $subject = $_GET['subject'];
    $incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE subject = '$subject' ORDER BY id DESC LIMIT 1");
}
$fetch_incident = mysqli_fetch_array($incident_query);
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800"><?php echo $fetch_incident['ref_no']; ?></h4>
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
                case 'irsucc':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> IR has been resolved successfully.';
                    break;
                case 'comment':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Comment has been posted successfully.';
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
                    <div class="card-header py-2 bg-danger">
                        <h6 class="m-0 font-weight-bold text-light">Incident Report Details</h6> 
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 d-flex justify-content-start">
                                <label>Reported By: </label>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <i><u><?php echo date("F d, Y", strtotime($fetch_incident['report_date'])); ?></u></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_incident['reported_by']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>Subject:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_incident['subject']; ?>" readonly>
                                </div>
                                <div class="col-6">
                                    <label>Location:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_incident['location']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12 d-flex justify-content-start">
                                <label>Person(s) Involved: </label>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-info">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $fetch_incident['person_involved']; ?></td>
                                        <td><?php echo $fetch_incident['position']; ?></td>
                                        <td><?php echo $fetch_incident['department']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>When:</label>
                                    <textarea class="form-control" rows="3" readonly><?php echo $fetch_incident['ir_when']; ?></textarea>
                                </div>
                                <div class="col-6">
                                    <label>Where:</label>
                                    <textarea class="form-control" rows="3" readonly><?php echo $fetch_incident['ir_where']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>What:</label>
                                    <textarea class="form-control" rows="3" readonly><?php echo $fetch_incident['ir_what']; ?></textarea>
                                </div>
                                <div class="col-6">
                                    <label>How:</label>
                                    <textarea class="form-control" rows="3" readonly><?php echo $fetch_incident['ir_how']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <textarea class="form-control" readonly><?php echo $fetch_incident['remarks']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php
                        if($fetch_incident['status'] == 1){
                        ?>
                        <hr>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>Resolved By:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_incident['resolved_by']; ?>" readonly>
                                </div>
                                <div class="col-6">
                                    <label>Resolved Date:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_incident['resolve_date']; ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <label>Resolution:</label>
                                    <textarea class="form-control" rows="2" readonly><?php echo $fetch_incident['resolution']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <center>
                            <a onclick="window.open('incident_print.php?incident_id=<?php echo $fetch_incident['id']; ?>')" class="d-sm-inline-block btn btn-warning btn-sm text-dark"><i class="fa fa-sm fa-print"></i> Print IR</a>
                        </center>
                        <?php
                        }else{
                            if(in_array(18, $permission)){
                            ?>
                                <hr>
                                <form method="POST" action="incident_resolve.php">
                                <div class="form-group">
                                <div class="row">
                                    <div class="col-12">
                                        <label>Resolution:</label>
                                        <textarea class="form-control" name="resolution" rows="2" required></textarea>
                                    </div>
                                </div>
                                </div>
                                <hr>
                                <center>
                                    <input type="text" name="id" value="<?php echo $fetch_incident['id']; ?>" hidden>
                                    <a onclick="window.open('incident_print.php?incident_id=<?php echo $fetch_incident['id']; ?>')" class="d-sm-inline-block btn btn-warning btn-sm text-dark"><i class="fa fa-sm fa-print"></i> Print IR</a>
                                    <a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-stamp"></i> Resolve</a>
                                </center>
                        <?php
                            }else{
                                
                            }
                        }
                        ?>

                            <!-- Submit Modal -->
                            <div class="modal fade" id="SubmitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success">
                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-stamp fa-sm"></i> &nbsp;<?php echo $fetch_incident['ref_no']; ?></h6>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><small>×</small></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Do you want to resolve this Incident Report?</div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Resolve</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit modal end -->
                        </center>
                        </form>
                    </div>
                </div>
                <!-- End Table -->

                <?php
                $ref_no = $fetch_incident['ref_no'];
                $comment_query = mysqli_query($conn,"SELECT * FROM tbl_report_comment WHERE ref_no = '$ref_no'");
                $comment_count = mysqli_num_rows($comment_query);
                ?>
                <h5>Comments (<?php echo $comment_count; ?>)</h5>
                <?php
                while($fetch_comment = mysqli_fetch_array($comment_query)){
                ?>     
                    <div class="card shadow mb-3 bg-secondary">
                        <div class="card-body">
                            <div class="d-flex flex-start">
                                <img class="img-profile rounded-circle" src="img/profile.png" width="30" height="30">
                                <div class="w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-1 p-1">
                                        <h6 class="text-warning fw-bold mb-0">&nbsp;&nbsp;<b><?php echo $fetch_comment['comment_by']; ?>:</b>
                                        <span class="text-light ms-2"><?php echo $fetch_comment['comment']; ?></span>
                                        </h6>
                                        <small class="mb-0 text-light"><?php echo date("F m, Y", strtotime($fetch_comment['comment_date'])); ?></small>
                                    </div>
                                </div>                       
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="card shadow mb-4 bg-secondary">
                    <div class="card-body">  
                    <form method="POST" action="comment_add.php">
                        <input type="text" name="url" value="<?php echo $url;?>" hidden>
                        <input type="text" name="ref_no" value="<?php echo $fetch_incident['ref_no'];?>" hidden>
                        <textarea class="form-control" name="comment" rows="2" placeholder="Type comment..." required></textarea>
                        <button type="submit" class="btn btn-sm btn-primary" name="submit" style="margin-top:10px; float:right;">Post Comment</button>
                    </form>
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
        $('#search_employee').select2({
            theme: "bootstrap"
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