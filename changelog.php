<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']))
{
?>

<style>
    .version-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }

    .changelog-item {
        position: relative;
        padding-left: 20px;
    }

    .changelog-item::before {
        content: "";
        position: absolute;
        left: 0;
        top: 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #0d6efd;
    }

    .timeline-line {
        position: absolute;
        left: 3px;
        top: 20px;
        bottom: -8px;
        width: 2px;
        background-color: #dee2e6;
    }
    .changelog-item p {
        margin: 0;
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>

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
            <h1 class="h3 mb-0 text-gray-800 d-flex justify-content-between align-items-center">
                <span class="text-success"><b>What's New at OMS?</b></span>
            </h1>
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
                case 'succ':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Rack has been added successfully.';
                    break;
                case 'import':
                    $statusType = 'alert-success';
                    $statusMsg = '<i class="fa fa-check-circle fa-sm"></i>&nbsp;<b>Success!</b> Data has been imported successfully.';
                    break;
                case 'err':
                    $statusType = 'alert-danger';
                    $statusMsg = '<i class="fa fa-exclamation-triangle fa-sm"></i>&nbsp;<b>Error!</b> You can only submit once per day.';
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
        <?php
        if($_SESSION['name'] == 'Aimes Barlis'){
        ?>
        <form action="changelog_import.php" method="post" enctype="multipart/form-data">
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
                            <span><a onclick="window.location.href='IMPORT_CHANGELOG_TEMPLATE.csv';" class="btn btn-success btn-sm text-light"><i class="fa fa-download"></i> Template</a></span>
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
        <?php
        }
        ?>
                <!-- DataTales Example -->
                <div class="card shadow mb-4">
                    <!-- <div class="card-header py-2 bg-primary">
                        <h6 class="m-0 font-weight-bold text-light">Log List</h6> 
                    </div> -->
                    <div class="card-body">

                        <?php
                        $version_query = mysqli_query($conn, "
                            SELECT version 
                            FROM tbl_changelog 
                            GROUP BY version 
                            ORDER BY 
                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 1), 'v', -1) AS UNSIGNED) DESC,
                                CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                                CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC
                        ");

                        $is_first = true; // Flag to identify the most recent version
                        while ($fetch_version = mysqli_fetch_assoc($version_query)) {
                            $version = $fetch_version['version'];
                            $badge_class = $is_first ? 'bg-primary' : 'bg-secondary'; // Assign appropriate badge class
                            $is_first = false; // After the first version, all others are old
                        ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="badge <?php echo $badge_class; ?> me-2 text-light"><?php echo $version; ?></span>
                                    &nbsp;<small class="text-muted">Changelog</small>
                                </div>

                                <div class="position-relative">
                                    <?php
                                    $action_query = mysqli_query($conn, "SELECT action FROM tbl_changelog WHERE version = '$version' GROUP BY action");
                                    while ($fetch_action = mysqli_fetch_assoc($action_query)) {

                                        if ($fetch_action['action'] == 'Added') {
                                            $h6_class = 'fw-bold text-success';
                                        } else if ($fetch_action['action'] == 'Fixed') {
                                            $h6_class = 'fw-bold text-danger';
                                        } else if ($fetch_action['action'] == 'Changed'){
                                            $h6_class = 'fw-bold text-warning';
                                        }

                                        echo '<div class="changelog-item mb-3">
                                            <div class="timeline-line"></div>
                                            <h6 class="' . $h6_class . '">' . $fetch_action['action'] . '</h6>';

                                        $action = $fetch_action['action'];
                                        $log_query = mysqli_query($conn, "SELECT log FROM tbl_changelog WHERE version = '$version' AND action = '$action'");
                                        while ($fetch_log = mysqli_fetch_assoc($log_query)) {
                                            echo '<p>' . $fetch_log['log'] . '</p>';
                                        }

                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <!-- End Table -->
            </div>
            <!-- /.container-fluid -->

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

        document.querySelector(".sidebar-toggle").addEventListener("click", function() {
            document.querySelector(".sidebar").classList.toggle("collapsed");
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