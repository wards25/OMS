<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && $_SESSION['role'] == 'Admin')
{
include_once("nav_settings.php");
include_once("export_modal.php");

    // delete tbl_export in db 
    mysqli_query($conn,"DELETE FROM tbl_export WHERE user = '$user'");

    $export_query = mysqli_query($conn,"DESCRIBE tbl_users");
    while($fetch_export = mysqli_fetch_array($export_query)) {
        $col_name = $fetch_export['Field'];
        $tbl_name = 'tbl_users';
        mysqli_query($conn,"INSERT INTO tbl_export VALUES (NULL,'$tbl_name','$col_name','1','0','$user')");
    }
        $export_query->free();
?>

    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">User Accounts</h4>
            <button type="button" class="d-sm-inline-block btn btn-sm btn-warning shadow-sm text-dark add-btn" data-toggle="modal" data-target="#addModal"><i class="fa-solid fa-user-plus"></i> Add User</button>
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
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> User has been added successfully.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> User has been updated successfully.';
                    break;
                case 'delete':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> User has been deleted successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Username exists.';
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

        <!-- DataTales Row -->
        <form action="account_import.php" method="post" enctype="multipart/form-data">
        <div class="card shadow mb-4">
            <div class="d-sm-flex card-header justify-content-between py-2 bg-primary">
                <h6 class="m-0 font-weight-bold text-light">Select CSV File</h6>
                <!--<a class="d-sm-inline-block btn btn-sm btn-success"><i class="fa fa-info"></i> Edit Census</a>-->
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                        <input class="form-control form-control-sm" type="file" id="formFile" name="file">
                    <div class="input-group-prepend">
                        <span class="btn btn-primary btn-sm" data-toggle="modal" data-target="#import"><i class="fa fa-upload"></i> Upload</span>
                        &nbsp;
                        <span><a onclick="window.location.href='IMPORT_USER_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
                    </div>
                </div>
            </div>
        </div>

            <!-- Upload Modal-->
            <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-upload fa-sm"></i> Upload File</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">Are you sure you want to upload this csv file?</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-success btn-sm" name="submit" value="Upload">
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </form>

            <!-- Add Modal -->
            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-user-plus"></i> Add User</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="account_add.php" id="UserForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>First Name</label>
                                            <input type="text" class="form-control form-control-sm" name="fname" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control form-control-sm" name="lname" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Username:</label>
                                            <input type="text" class="form-control form-control-sm" name="username" autocomplete="off" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Password:</label>
                                            <input type="text" class="form-control form-control-sm" name="password" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Role:</label>
                                            <?php
                                            $query = "SELECT * FROM tbl_roles GROUP BY role_name ORDER BY role_name ASC";
                                            $result = $conn->query($query);
                                            if($result->num_rows> 0){
                                                $options= mysqli_fetch_all($result, MYSQLI_ASSOC);?>

                                                <select class="form-control form-control-sm" name="role" required>
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
                                            <select class="form-control form-control-sm" name="user_status">
                                                <option value="1">ACTIVE</option>
                                                <option value="0">INACTIVE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-row">
                                        <div class="col-6">
                                            <label>Tag:</label>
                                            <select class="form-control form-control-sm" name="tag">
                                                <option value="ALL">ALL</option>
                                                <option value="ADMIN">ADMIN</option>
                                                <option value="FINANCE">FINANCE</option>
                                                <option value="HR">HR</option>
                                                <option value="LOGISTICS">LOGISTICS</option>
                                                <option value="SALES">SALES</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Hub:</label>
                                            <select class="form-control form-control-sm" name="hub">
                                                <option value="CAINTA">CAINTA</option>
                                                <option value="CDO">CDO</option>
                                                <option value="CEBU">CEBU</option>
                                                <option value="DAVAO">DAVAO</option>
                                                <option value="ILOILO">ILOILO</option>
                                                <option value="LEYTE">LEYTE</option>
                                                <option value="PANGASINAN">PANGASINAN</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" class="text-primary bg-light">Location Access</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $hub_query = mysqli_query($conn, "SELECT * FROM tbl_locations");
                                                $count = 0;

                                                while ($hub = mysqli_fetch_array($hub_query)) {
                                                    if ($count % 2 == 0) echo "<tr>";
                                                    $hub_id = $hub['id'];
                                                    $location_name = htmlspecialchars($hub['location_name']);
                                                    $is_disabled = $hub['is_active'] == 1 ? '' : 'disabled';
                                                ?>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="hidden" name="id[<?= $hub_id; ?>]" value="<?= $hub_id; ?>">
                                                            <input type="hidden" name="status[<?= $hub_id; ?>]" value="0">

                                                            <input class="form-check-input"
                                                                type="checkbox"
                                                                name="status[<?= $hub_id; ?>]"
                                                                value="1"
                                                                id="flexCheck<?= $hub_id; ?>"
                                                                <?= $is_disabled; ?>>

                                                            <label class="form-check-label" for="flexCheck<?= $hub_id; ?>">
                                                                <?= $location_name; ?>
                                                            </label>
                                                        </div>
                                                    </td>
                                                <?php
                                                    $count++;
                                                    if ($count % 2 == 0) echo "</tr>";
                                                }
                                                // Close final row if odd number
                                                if ($count % 2 != 0) echo "</tr>";
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <button class="btn btn-success btn-sm" name="submit" type="submit">Add User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Existing User List</h6> 
                    </div> -->
                    <div class="card-body">
                        <div class="d-flex flex-row-reverse">
                            <a type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#exportModal"><i class="fa fa-download"></i> Export Data</a>
                        </div>
                        <hr>
                        <?php
                        // Fetch all users and their roles in a single query
                        $query = "SELECT u.*, r.role_name FROM tbl_users u LEFT JOIN tbl_roles r ON u.role_id = r.id";
                        $result = mysqli_query($conn, $query);
                        $users = [];

                        while ($row = mysqli_fetch_assoc($result)) {
                            $users[] = $row;
                        }
                        ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm text-center" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="table-success">
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Tag</th>
                                        <th>Status</th>
                                        <th>Update</th>
                                        <th>Access</th>
                                        <?php if($_SESSION['role'] === 'Admin') echo '<th>Delete</th>'; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></td>
                                            <td><?= htmlspecialchars($row['role_name']) ?></td>
                                            <td><?= htmlspecialchars($row['tag']) ?></td>
                                            <td><center><span class="badge badge-<?= $row['is_active'] ? 'success' : 'danger' ?>"><?= $row['is_active'] ? 'Active' : 'Inactive' ?></span></center></td>
                                            <td><center><button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#update<?= $row['id'] ?>"><i class="fa fa-cog fa-sm"></i> Update</button></center></td>
                                            <td><center><a href="account_edit.php?update=<?= $row['id'] ?>" class="btn btn-sm btn-info"><i class="fa fa-info fa-sm"></i> Edit</a></center></td>
                                            <?php if ($_SESSION['role'] === 'Admin'): ?>
                                                <td><center><button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete<?= $row['id'] ?>"><i class="fa fa-trash fa-sm"></i></button></center></td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
                        // Fetch locations once
                        $locations = [];
                        $location_query = mysqli_query($conn, "SELECT * FROM tbl_locations");
                        while ($loc = mysqli_fetch_assoc($location_query)) {
                            $locations[] = $loc;
                        }
                        ?>

                        <?php foreach ($users as $user): ?>
                            <!-- Update Modal -->
                            <div class="modal fade" id="update<?= $user['id'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark">
                                            <h6 class="modal-title text-warning"><i class="fa fa-cog fa-sm"></i> Update User</h6>
                                            <button class="close" type="button" data-dismiss="modal"><span><small>&times;</small></span></button>
                                        </div>
                                        <form method="POST" action="account_update.php">
                                            <div class="modal-body">
                                                <div class="form-row">
                                                    <div class="col-6">
                                                        <label>First name:</label>
                                                        <input type="text" class="form-control form-control-sm" name="fname" value="<?= htmlspecialchars($user['fname']) ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Last name:</label>
                                                        <input type="text" class="form-control form-control-sm" name="lname" value="<?= htmlspecialchars($user['lname']) ?>">
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-6">
                                                        <label>Username:</label>
                                                        <input type="text" class="form-control form-control-sm" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Hub:</label>
                                                        <select class="form-control form-control-sm" name="hub">
                                                            <option value="<?= $user['hub'] ?>"><?= $user['hub'] ?> (Existing)</option>
                                                            <?php foreach (["CAINTA", "CDO", "CEBU", "DAVAO", "ILOILO", "LEYTE", "PANGASINAN"] as $hub): ?>
                                                                <option value="<?= $hub ?>"><?= $hub ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-6">
                                                        <label>Role:</label>
                                                        <select class="form-control form-control-sm" name="role">
                                                            <option value="<?= $user['role_id'] ?>"><?= $user['role_name'] ?> (Existing)</option>
                                                            <?php
                                                            $role_query = mysqli_query($conn, "SELECT * FROM tbl_roles GROUP BY role_name ORDER BY role_name ASC");
                                                            while ($role = mysqli_fetch_assoc($role_query)) {
                                                                echo '<option value="' . $role['id'] . '">' . $role['role_name'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label>Status:</label>
                                                        <select class="form-control form-control-sm" name="user_status">
                                                            <option value="<?= $user['is_active'] ?>"><?= $user['is_active'] ? 'ACTIVE' : 'INACTIVE' ?> (Existing)</option>
                                                            <option value="1">ACTIVE</option>
                                                            <option value="0">INACTIVE</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tag:</label>
                                                    <select class="form-control form-control-sm" name="tag">
                                                        <option value="<?= $user['tag'] ?>"><?= $user['tag'] ?> (Existing)</option>
                                                        <?php foreach (["ALL", "ADMIN", "FINANCE", "HR", "LOGISTICS", "SALES"] as $tag): ?>
                                                            <option value="<?= $tag ?>"><?= $tag ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead><tr><th colspan="4" class="text-primary bg-light">Location Access</th></tr></thead>
                                                        <tbody>
                                                        <?php
                                                        $temp = 1;
                                                        $x = 200;
                                                        foreach ($locations as $location):
                                                            $stmt = $conn->prepare("SELECT 1 FROM tbl_user_locations WHERE user_id = ? AND location_id = ? LIMIT 1");
                                                            $stmt->bind_param('ii', $user['id'], $location['id']);
                                                            $stmt->execute();
                                                            $has_access = $stmt->get_result()->num_rows > 0;

                                                            $disabled = $location['is_active'] == 0 ? 'disabled' : '';
                                                            if ($temp == 1) echo "<tr>";
                                                        ?>
                                                        <td>
                                                            <div class="form-check">
                                                                <input type="hidden" name="id[<?= $location['id'] ?>]" value="<?= $location['id'] ?>">
                                                                <input type="hidden" name="status[<?= $location['id'] ?>]" value="0">
                                                                <input class="form-check-input" type="checkbox" name="status[<?= $location['id'] ?>]" value="1" id="loc<?= $location['id'] . $x ?>" <?= $has_access ? 'checked' : '' ?> <?= $disabled ?>>
                                                                <label class="form-check-label" for="loc<?= $location['id'] . $x ?>"><?= htmlspecialchars($location['location_name']) ?></label>
                                                            </div>
                                                        </td>
                                                        <?php
                                                            if ($temp == 2) {
                                                                echo "</tr>";
                                                                $temp = 0;
                                                            }
                                                            $temp++;
                                                        endforeach;
                                                        if ($temp != 1) echo '</tr>';
                                                        ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="update_id" value="<?= $user['id'] ?>">
                                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                <button class="btn btn-success btn-sm" name="update" type="submit">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="delete<?= $user['id'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h6 class="modal-title text-light"><i class="fa fa-trash fa-sm"></i> Delete User</h6>
                                            <button class="close" type="button" data-dismiss="modal"><span><small>&times;</small></span></button>
                                        </div>
                                        <form method="POST" action="account_delete.php">
                                            <div class="modal-body">
                                                Are you sure you want to delete <?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?>?
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                                                <input type="hidden" name="account" value="<?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?>">
                                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                <button class="btn btn-danger btn-sm" name="update" type="submit">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
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
        // Export List 1
        function ExportList1() {
            $.ajax({
                type: "post",
                url: "export_list1.php",
                success: function(data) {
                    $('#export-list1').html(data);
                }
            });
        }
        ExportList1();   

        // Export List 2
        function ExportList2() {
            $.ajax({
                type: "post",
                url: "export_list2.php",
                success: function(data) {
                    $('#export-list2').html(data);
                }
            });
        }
        ExportList2(); 

        // Update Item
        $(document).on('click', '.btn-update', function(){
            var id = $(this).data("id");
            $.ajax({
                type: "post",
                url: "export_update.php",
                data: {id:id},
                success: function() {
                    ExportList1();
                    ExportList2();
                }
            });
        });

        // Reset add modal button
        $('.add-btn').click(function(){
            $('#UserForm')[0].reset();
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