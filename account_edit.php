<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

$url = $_SERVER['REQUEST_URI'];

unset($_SESSION['previous_pages']);

// Store the current page URL
$_SESSION['previous_pages'][] = $_SERVER['REQUEST_URI'];

// Keep only the last two pages in the session
if (count($_SESSION['previous_pages']) > 2) {
    array_shift($_SESSION['previous_pages']);
}

if(isset($_SESSION['id']) && $_SESSION['role'] == 'Admin')
{
include_once("nav_settings.php");
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Update Permissions</h4>
            <a type="button" href="account.php" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm add-user-btn"><i class="fa fa-arrow-left"></i> Back</a>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Persmission has been copied successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> No account selected.';
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

        <?php } 
        $update_id = $_GET['update'];
        $_SESSION['update_id'] = $update_id;
        $detail_query = mysqli_query($conn,"SELECT * FROM tbl_users WHERE id = '$update_id'");
        $row = mysqli_fetch_array($detail_query);
        ?>
        
            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-primary">
                    <h6 class="m-0 font-weight-bold text-light"><i class="fa fa-info fa-sm"></i> Active Permissions</h6> 
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-6">
                                <label>Name:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $row['fname'].' '.$row['lname']; ?>" disabled>
                            </div>
                            <div class="col-6">
                                <label>Username:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $row['username']?>" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-6">
                                <label>Role:</label>
                                <?php
                                $roleid = $row['role_id'];
                                $query = "SELECT * FROM tbl_roles GROUP BY role_name ORDER BY role_name ASC";
                                $result = $conn->query($query);
                                if($result->num_rows > 0){
                                    $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                    <select class="form-control form-control-sm" disabled>
                                    <?php 
                                    $role_query = mysqli_query($conn,"SELECT * FROM tbl_roles WHERE id = '$roleid'");
                                    $fetch_role = mysqli_fetch_array($role_query);
                                    ?>
                                        <option value="<?php echo $fetch_role['id'];?>"><?php echo $fetch_role['role_name']; ?> (Existing)</option>
                                    <?php    
                                    foreach ($options as $option) {
                                    ?>
                                        <option value="<?php echo $option['id'];?>"><?php echo $option['role_name']; ?> </option>
                                <?php 
                                    }
                                }
                                ?>
                                    </select>
                            </div>
                            <div class="col-6">
                                <label>Status:</label>
                                <select class="form-control form-control-sm" disabled>
                                    <?php
                                    if($row['is_active'] == 0){
                                        echo '<option value="0">INACTIVE (Existing)</option>';
                                    }else{
                                        echo '<option value="1">ACTIVE (Existing)</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="permission_copy.php">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-9">
                                <label>Copy Permission From:</label>
                                <?php
                                $query = "SELECT * FROM tbl_users WHERE id != '$update_id' ORDER BY fname ASC";
                                $result = $conn->query($query);
                                if($result->num_rows > 0){
                                    $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                    <select class="form-control form-control-sm" name="copy_id">
                                        <option value=""></option>
                                    <?php    
                                    foreach ($options as $option) {
                                    ?>
                                        <option value="<?php echo $option['id'];?>"><?php echo $option['fname'].' '.$option['lname']; ?> </option>
                                <?php 
                                    }
                                }
                                ?>
                                    </select>
                            </div>
                            <div class="col-3">
                                <label>&nbsp;</label>
                                <?php
                                $url = $_SERVER['REQUEST_URI'];
                                ?>
                                <input type="text" name="update_id" value="<?php echo $update_id; ?>" hidden>
                                <input type="text" name="url" value="<?php echo $url; ?>" hidden>
                                <button type="submit" class="btn btn-block btn-success btn-sm"><i class="fa fa-sm fa-clone"></i> Copy</button>
                            </div>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <div class="form-group">
                        <div class="input-group">
                            Search: &nbsp;<input type="text" class="form-control form-control-sm search-permission1">
                        </div>
                        <br>
                        <div class="table-responsive" style="height:400px; overflow: auto;">
                            <table class="table table-bordered table-striped table-sm text-center" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-info" style="position: sticky;top: 0;">
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="permission-list">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                        <tr>
                            <td><center><a type="button" class="btn btn-primary btn-sm add-btn" href="#" data-toggle="modal" data-target="#AddModal"><i class="fa fa-sm fa-list"></i> Add Permission(s)</a></center></td>
                            <center><small class="text-danger"><i>*real time editing of permission*</i></small>
                        </tr>
                    </div>
                </div>
            </div>

        <!-- Add Modal-->
        <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-sm fa-plus"></i> Add Permission</h6>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form id="AddPermissionForm">
                        <div class="modal-body">
                            <div id="alert"></div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group">
                                        Search: &nbsp;<input type="text" class="form-control form-control-sm search-permission2">
                                    </div>
                                    <br>
                                    <div class="table-responsive" style="height:400px; overflow: auto;">
                                        <table class="table table-bordered table-sm text-center">
                                            <thead class="table-warning table-sm" style="position: sticky;top: 0;">
                                                <tr>
                                                    <th>Action</th>
                                                    <th>Module</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="add-list">
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
<?php
}
?>
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
                url: "permission_delete.php",
                data: {id:id},
                success: function() {
                    ItemList();
                    PermissionList();
                }
            });
        });

        // add item
        $(document).on('click', '.add-btn2', function(){
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "permission_add.php",
                data: {id:id},
                success: function() {
                    ItemList();
                    PermissionList();
                }
            });
        });

        // Permission List
        function PermissionList() {
            const term = $('.search-permission1').val();
            $.ajax({
                type: "post",
                url: "permission_list.php",
                data: {
                    term
                },
                success:function(data) {
                    $('#permission-list').html(data);
                }
            });
        }
        PermissionList();

        $(document).on('keyup','.search-permission1',function(){
            PermissionList();
        });

        // Permission List
        function AddList() {
            const term = $('.search-permission2').val();
            $.ajax({
                type: "post",
                url: "permission_system.php",
                data: {
                    term
                },
                success:function(data) {
                    $('#add-list').html(data);
                }
            });
        }
        AddList();

        $(document).on('keyup','.search-permission2',function(){
            AddList();
        });

        // Update ItemList to maintain the search filter
        function ItemList() {
            const term = $('.search-permission2').val(); // Get current search term
            $.ajax({
                type: "post",
                url: "permission_system.php",
                data: { term }, // Pass the search term
                success: function(data) {
                    $('#add-list').html(data);
                }
            });
        }

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#AddPermissionForm')[0].reset();
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