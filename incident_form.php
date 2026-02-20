<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

//get data from checklist
$location = $_GET['location'];
$date = $_GET['date'];
$shift = $_GET['shift'];
$shift_type = $_GET['shift_type'];
$module_id = $_GET['module_id'];
$table = $_GET['table'];
$tbl_column = $_GET['tbl_column'];
$subject = $_GET['subject'];
$tag = $_GET['tag'];

mysqli_query($conn,"DELETE FROM tbl_report_involved WHERE user = '$user'");

if(isset($_SESSION['id']) && in_array(17, $permission))
{
include_once("nav_forms.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">File Report</h4>
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
                        <h6 class="m-0 font-weight-bold text-light">Incident Report Form</h6> 
                    </div>
                    <div class="card-body">
                        <form id="AddPerson">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <label>Person(s) Involved:</label>
                                    <div id="alert3"></div>
                                    <tr>
                                        <td colspan="3">
                                            <?php 
                                            $query ="SELECT * FROM tbl_employees WHERE location = '$location'";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>
                                             
                                            <select class="form-control form-control-sm" name="rolename" id="search_employee" required>
                                                <option value=""></option>
                                                <option value="3rd Party Trucker">3rd Party Trucker</option>
                                            <?php 
                                                foreach ($options as $option) {
                                            ?>
                                                <option value="<?php echo $option['employee_name'];?>"><?php echo $option['employee_name']; ?> </option>
                                            <?php 
                                                }
                                            }
                                            ?>
                                            </select>
                                        </td>
                                            <td style="width:5%;"><center><button type="submit" id="add" name="add" class="btn btn-sm btn-success"><i class="fa fa-sm fa-plus"></i></button></td>
                                    </tr>
                                    <tr class="table-info">
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="involved-list">
                                </tbody>
                            </table>
                        </div>
                        </form>
                        <form method="POST" action="report_submit.php">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>Subject:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $subject; ?>" name="subject" readonly>
                                </div>
                                <div class="col-6">
                                    <label>Tag:</label>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo $tag; ?>" name="tag" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>When:</label>
                                    <textarea class="form-control" name="ir_when" rows="3" required></textarea>
                                </div>
                                <div class="col-6">
                                    <label>Where:</label>
                                    <textarea class="form-control" name="ir_where" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6">
                                    <label>What:</label>
                                    <textarea class="form-control" name="ir_what" rows="3" required></textarea>
                                </div>
                                <div class="col-6">
                                    <label>How:</label>
                                    <textarea class="form-control" name="ir_how" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <textarea class="form-control" placeholder="Enter remarks here..." name="remarks" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="text" name="module_id" value="<?php echo $module_id; ?>" hidden>
                        <input type="text" name="table" value="<?php echo $table; ?>" hidden>
                        <input type="text" name="tbl_column" value="<?php echo $tbl_column; ?>" hidden>
                        <input type="text" name="location" value="<?php echo $location; ?>" hidden>
                        <input type="text" name="date" value="<?php echo $date; ?>" hidden>
                        <input type="text" name="shift" value="<?php echo $shift; ?>" hidden>
                        <input type="text" name="shift_type" value="<?php echo $shift_type; ?>" hidden>
                        <hr>
                        <center>
                            <a class="d-sm-inline-block btn btn-success btn-sm" data-toggle="modal" data-target="#SubmitModal"><i class="fa fa-sm fa-check"></i> Submit</a>
                        </center>
                            <!-- Submit Modal -->
                            <div class="modal fade" id="SubmitModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success">
                                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check fa-sm"></i> Submit Form</h6>
                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true"><small>×</small></span>
                                            </button>
                                        </div>
                                        <div class="modal-body">Do you want to submit this form?</div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success btn-sm" type="submit" name="submit">Submit</button>
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
        // delete item
        $(document).on('click', '.delete-btn', function(){
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "involved_delete.php",
                data: {id:id},
                success: function() {
                    InvolvedList();
                }
            });
        });

        $('#AddPerson').submit(function(e){
            e.preventDefault();
            var item = $('#AddPerson').serialize();
            $.ajax({
                type: "post",
                url: "involved_add.php",
                data: item,
                success: function(data) {
                   // $('#PlanForm')[0].reset();
                    InvolvedList();
                    if(data == '') {
                        InvolvedList();
                    } else {
                        $('#alert3').show();
                        $('#alert3').html(data);
                        window.setTimeout(function() {
                            $(".alert3").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove(); 
                            });
                        }, 2000);
                    }
                }
            });
        });

        // Add List
        function InvolvedList() {
            $.ajax({
                type: "post",
                url: "involved_list.php",
                success: function(data) {
                    $('#involved-list').html(data);
                }
            });
        }
        InvolvedList();

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#AssetForm')[0].reset();
        });
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