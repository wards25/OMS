<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']))
{
?>

<!-- Page Wrapper -->
<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column min-vh-100" style="background-color: #edfaf4;">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar shadow p-2">
            <!-- Left-aligned OMS Logo & Text -->
            <div class="d-flex align-items-center me-auto" style="margin-left: 15px;">
                <span class="text-success d-flex align-items-center">
                    <img src="img/oms.png" style="width: 45px; height: 45px; margin-right: 5px;">
                    <div class="d-none d-sm-flex flex-column" style="line-height: 20px;">
                        <b>ORGANIZATIONAL</b>
                        <b>MGMT SYSTEM</b>
                    </div>
                </span>
            </div>

            <!-- Right-aligned User Info -->
            <ul class="navbar-nav ml-auto">

                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="menu.php">
                        <i class="fa-solid fa-grip text-success"></i>
                    </a>
                </li>

                <li class="nav-item dropdown no-arrow mx-1">

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Info -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php echo $_SESSION['name']; ?>
                        </span>
                        <img class="img-profile rounded-circle" src="img/profile.png">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item text-dark">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-success"></i> Hi, <b><?php echo $_SESSION['name']; ?></b>
                        </a>
                        <a href="activitylog.php" class="dropdown-item">
                            <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log
                        </a>
                        <a href="changepass.php" class="dropdown-item">
                            <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i> Change Password
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

    <br>
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0 text-gray-800">Change Password</h4>
            <a type="button" href="javascript:history.go(-1)" class="d-sm-inline-block btn btn-sm btn-secondary shadow-sm"><i class="fa fa-arrow-left"></i> Back</a>
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
                case 'pass':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Old password is wrong.';
                    break;
                case 'err':
                    $statusType = 'alert-warning';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> Password doesnt match.';
                    break;
                case 'update':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Password has been updated successfully.';
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
                <form method="POST" action="changepass_submit.php">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2 bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">Enter New Password</h6> 
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-12">
                                        <label>Old Password:</label>
                                        <div class="input-group mb-3">
                                            <input type="password" class="form-control form-control-sm" name="oldpass" autocomplete="off" id="old-password" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-sm fa fa-eye" type="button" id="toggle-old"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col-6">
                                        <label>New Password:</label>
                                        <div class="input-group mb-3">
                                            <input type="password" class="form-control form-control-sm" name="newpass1" autocomplete="off" id="new-password" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-sm fa fa-eye" type="button" id="toggle-new"></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label>Confirm Password:</label>
                                        <div class="input-group mb-3">
                                            <input type="password" class="form-control form-control-sm" name="newpass2" autocomplete="off" id="confirm-password" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-sm fa fa-eye" type="button" id="toggle-confirm"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                                <center><button class="btn btn-success btn-sm" name="update" type="submit"><i class="fa-solid fa-lock"></i> Change Password</button>
                        </div>
                    </div>
                </form>
                <!-- End Table -->

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>

        <?php
        include_once("footer.php");
        ?>
    </div>

    <script>
        // Toggle visibility for old password
        const oldPasswordEle = document.getElementById('old-password');
        const toggleOldEle = document.getElementById('toggle-old');
        
        toggleOldEle.addEventListener('click', function () {
            const type = oldPasswordEle.getAttribute('type');
            toggleOldEle.classList.toggle('fa-eye-slash');
            toggleOldEle.classList.toggle('fa-eye');
            oldPasswordEle.setAttribute('type', type === 'password' ? 'text' : 'password');
        });

        // Toggle visibility for new password
        const newPasswordEle = document.getElementById('new-password');
        const toggleNewEle = document.getElementById('toggle-new');

        toggleNewEle.addEventListener('click', function () {
            const type = newPasswordEle.getAttribute('type');
            toggleNewEle.classList.toggle('fa-eye-slash');
            toggleNewEle.classList.toggle('fa-eye');
            newPasswordEle.setAttribute('type', type === 'password' ? 'text' : 'password');
        });

        // Toggle visibility for confirm password
        const confirmPasswordEle = document.getElementById('confirm-password');
        const toggleConfirmEle = document.getElementById('toggle-confirm');

        toggleConfirmEle.addEventListener('click', function () {
            const type = confirmPasswordEle.getAttribute('type');
            toggleConfirmEle.classList.toggle('fa-eye-slash');
            toggleConfirmEle.classList.toggle('fa-eye');
            confirmPasswordEle.setAttribute('type', type === 'password' ? 'text' : 'password');
        });


        // Reset add modal button
        $('.add-btn').click(function(){
            $('#ModuleForm')[0].reset();
        });
    </script>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-sign-out-alt fa-sm"></i> Ready to Leave?</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-success btn-sm" href="logout.php?logout">Logout</a>
                </div>
            </div>
        </div>
    </div>

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