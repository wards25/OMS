<?php
    $uri = $_SERVER['REQUEST_URI'];
?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #21c275;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon">
                </div>
                <div class="sidebar-brand-text mx-3"><img src="img/logo.png" class="img-fluid" style="height:30px;">
                    <br><small>Org Mgmt System</small>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Daily Checklist
            </div>

            <li class="nav-item">
                <a class="nav-link" href="checklist.php">
                <i class="fas fa-fw fa-check-square"></i>
                <span>Daily Checklist</span></a>
            </li>

            <?php
            if(in_array(19, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="incident_raw.php">
                    <i class="fas fa-fw fa-exclamation-triangle"></i>
                    <span>Incident Report</span></a>
                </li>';
            }else{
            }

            if(in_array(3, $permission) || in_array(59, $permission) || in_array(67, $permission) || in_array(75, $permission)){
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAdmin"
                        aria-expanded="true" aria-controls="collapseAdmin">
                        <i class="fas fa-fw fa-building"></i>
                        <span>HR/Admin</span>
                    </a>
                    <div id="collapseAdmin" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                                $links = [
                                    3 => 'employee.php',
                                    59 => 'facility_raw.php',
                                    67 => 'fireext_raw.php',
                                    75 => 'mousetrap_raw.php'
                                ];

                                $labels = [
                                    3 => 'Employee List',
                                    59 => 'General Facilities',
                                    67 => 'Fire Extinguisher',
                                    75 => 'Mouse Trap'
                                ];

                                foreach ($links as $key => $url) {
                                    if (in_array($key, $permission)) {
                                        echo '<a href="' . $url . '" class="collapse-item">' . $labels[$key] . '</a>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }else{
            }

            if(in_array(3, $permission) || in_array(16, $permission) || in_array(39, $permission) || in_array(43, $permission || in_array(31, $permission) || in_array(27, $permission) || in_array(35, $permission))){
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWH"
                        aria-expanded="true" aria-controls="collapseWH">
                        <i class="fas fa-fw fa-warehouse"></i>
                        <span>Warehouse</span>
                    </a>
                    <div id="collapseWH" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                                $links = [
                                    3 => 'employee.php',
                                    16 => 'asset_raw.php',
                                    39 => 'ofattendance_raw.php',
                                    43 => 'temp_raw.php',
                                    31 => 'attendance_raw.php',
                                    27 => 'warehouselock_raw.php',
                                    35 => 'zone_raw.php'
                                ];

                                $labels = [
                                    3 => 'Employee List',
                                    16 => 'Asset Assignment',
                                    39 => 'Office Attendance',
                                    43 => 'Temp Monitoring',
                                    31 => 'WH Attendance',
                                    27 => 'Warehouse Lock',
                                    35 => 'Warehouse Zone'
                                ];

                                foreach ($links as $key => $url) {
                                    if (in_array($key, $permission)) {
                                        echo '<a href="' . $url . '" class="collapse-item">' . $labels[$key] . '</a>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }else{
            }
            ?>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Inbound
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseInbound"
                    aria-expanded="true" aria-controls="collapseInbound">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Purchase Order</span>
                </a>
                <div id="collapseInbound" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Modules:</h6>
                            <a href="product.php" class="collapse-item">Product List</a>
                            <a href="po.php" class="collapse-item">Purchase Orders</a>
                            <a href="po_pending.php" class="collapse-item">Pending PO</a>
                            <a href="po_forreceiving.php" class="collapse-item">For Receiving</a>
                    </div>
                </div>
            </li>

            <?php
            if(in_array(101, $permission) || in_array(109, $permission) || in_array(106, $permission) || in_array(112, $permission) || in_array(115, $permission) || in_array(119, $permission) || in_array(124, $permission) || in_array(129, $permission) || in_array(124, $permission)){
            ?>
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    Outbound
                </div>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOutbound"
                        aria-expanded="true" aria-controls="collapseOutbound">
                        <i class="fas fa-fw fa-truck"></i>
                        <span>
                            Trips 
                            <?php
                            // Initialize the counts
                            $picker_query = mysqli_query($conn, "SELECT picker, picker_end FROM tbl_trips_picklist WHERE picker = '$user' AND picker_end = ''");
                            $count_picker = mysqli_num_rows($picker_query);

                            $checker_query = mysqli_query($conn, "SELECT checker, checker_end FROM tbl_trips_picklist WHERE checker = '$user' AND (status='FOR CHECKING' OR status='CHECKING STARTED')");
                            $count_checker = mysqli_num_rows($checker_query);

                            $sorter_query = mysqli_query($conn, "SELECT sorter, sorter_end FROM tbl_trips_tm WHERE sorter = '$user' AND sorter_start=''");
                            $count_sorter = mysqli_num_rows($sorter_query);

                            $invoicing_query = mysqli_query($conn, "SELECT sono FROM tbl_trips_raw GROUP BY sono HAVING SUM(invoicing_status NOT IN ('FOR INVOICING')) = 0");
                            $count_invoicing = mysqli_num_rows($invoicing_query);

                            $loading_query = mysqli_query($conn, "SELECT * FROM tbl_trips_tm WHERE sorter_end != '' AND ic_end = ''");
                            $count_loading = mysqli_num_rows($loading_query);

                            $dispatch_query = mysqli_query($conn, "SELECT * FROM tbl_trips_tm WHERE ic_end != '' AND dispatcher = ''");
                            $count_dispatch = mysqli_num_rows($dispatch_query);

                            $count_sorting = 0; // Default count for sorting
                            $count_validation = 0; // Default count for validation

                            // Only count validation if the user is Admin or Supervisor
                            if (in_array(111, $permission)) {
                                $validation_query = mysqli_query($conn, "SELECT status FROM tbl_trips_picklist WHERE status = 'FOR VALIDATION'");
                                $count_validation = mysqli_num_rows($validation_query);

                                $sorting_query = mysqli_query($conn, "SELECT tmno FROM tbl_trips_picklist GROUP BY tmno HAVING SUM(status NOT IN ('FOR SORTING')) = 0");
                                $count_sorting = mysqli_num_rows($sorting_query);
                            }

                            // Check if any count is greater than 1 (only include count_validation for Admin/Supervisor)
                            if ($count_sorting > 0 || $count_picker > 0 || $count_checker > 0 || $count_validation > 0 || $count_sorter > 0 || $count_invoicing > 0 || $count_loading > 0) {
                                echo '&nbsp;<i class="fas fa-bell text-warning"></i>'; // Active bell icon
                            }else{
                                echo '&nbsp;<i class="fas fa-bell"></i>';
                            }
                            ?>
                        </span>
                    </a>
                    <div id="collapseOutbound" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                                $links = [
                                    101 => 'trips.php',
                                    109 => 'picking.php',
                                    106 => 'checking.php',
                                    112 => 'validation.php',
                                    115 => 'sorting.php',
                                    119 => 'invoicing.php',
                                    129 => 'loading.php',
                                    124 => 'dispatch.php'
                                ];

                                $labels = [
                                    101 => 'Trip List',
                                    109 => 'For Picking',
                                    106 => 'For Checking',
                                    112 => 'For Validation',
                                    115 => 'For Sorting',
                                    119 => 'To Invoice',
                                    129 => 'For Loading',
                                    124 => 'Dispatch'
                                ];

                                $counts = [
                                    101 => $count_sorting, // Placeholder, not calculated in this script
                                    109 => $count_picker,
                                    106 => $count_checker,
                                    112 => $count_validation,
                                    115 => $count_sorter,
                                    119 => $count_invoicing,
                                    129 => $count_loading,
                                    124 => $count_dispatch
                                ];

                                foreach ($links as $key => $url) {
                                    if (in_array($key, $permission)) {
                                        echo '<a href="' . $url . '" class="collapse-item">' . $labels[$key] . 
                                             ' <b class="text-danger">[' . $counts[$key] . ']</b></a>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }else{
            }
            ?>

            <?php
            if(in_array(89, $permission) || in_array(93, $permission) || in_array(97, $permission)){
            ?>
                <!-- Divider -->
                <hr class="sidebar-divider">
                <!-- Heading -->
                <div class="sidebar-heading">
                    Forms
                </div>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseForm"
                        aria-expanded="true" aria-controls="collapseForm">
                        <i class="fas fa-fw fa-list-alt"></i>
                        <span>RGC Forms</span>
                    </a>
                    <div id="collapseForm" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                                <?php
                                $links = [
                                    89 => 'pvf.php',
                                    93 => 'rvf.php',
                                    97 => 'lvf.php'
                                ];

                                $labels = [
                                    89 => 'Picking Variance',
                                    93 => 'Redel Variance',
                                    97 => 'Loading Variance'
                                ];

                                foreach ($links as $key => $url) {
                                    if (in_array($key, $permission)) {
                                        echo '<a href="' . $url . '" class="collapse-item">' . $labels[$key] . '</a>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }else{
            }
            ?>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <?php
            if($_SESSION['role'] == 'Admin'){
            ?>

            <!-- Heading -->
            <div class="sidebar-heading">
                Admin
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Configuration</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Configuration:</h6>
                            <a href="#" class="collapse-item" data-toggle="modal" data-target="#addroleModal">Add Position</a>
                            <a href="company.php" class="collapse-item">Company Setting</a>
                            <a href="deadline.php" class="collapse-item">Deadline Setting</a>
                            <a href="kpi.php" class="collapse-item">KPI Setting</a>
                            <a href="#" class="collapse-item location-btn" data-toggle="modal" data-target="#locationModal">Location Setting</a>
                            <a href="permission.php" class="collapse-item">Permission Map</a>
                            <a class="collapse-item" href="account.php">User Accounts</a>
                            <a href="#" class="collapse-item" data-toggle="modal" data-target="#backupModal">Backup Database</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

        <?php
        }else{

        } 
        ?>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="menu.php" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-th-large fa-fw"></i>
                            </a>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <li class="nav-item dropdown no-arrow mx-1">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['name']; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/profile.png">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <?php
                                    echo '<a class="dropdown-item text-dark">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-success"></i>
                                        Hi ! <b>'.$_SESSION['name'].'</b>
                                    </a>';
                                ?>
                                <a href="activitylog.php" class="dropdown-item"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log</a>
                                <a href="changepass.php" class="dropdown-item"><i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i> Change Password</a>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

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

    <!-- Alert Modal-->
    <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-exclamation-triangle fa-sm"></i> Error!</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">You don't have access to this action.</div>
            </div>
        </div>
    </div>

    <!-- Deadline Modal-->
    <div class="modal fade" id="DeadlineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-exclamation-triangle fa-sm"></i> Error!</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">The cut-off time has passed.</div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addroleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-sm fa-plus"></i> Add New Position</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="AddRoleForm">
                    <div class="modal-body">
                        <div id="alert"></div>
                        <label>Position Name:</label>
                        <input type="text" class="form-control form-control-sm" name="rolename" required autocomplete="off">
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="table-warning text-center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Position Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="role-list">
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="add" name="add" class="btn btn-sm btn-success">Add Position</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Location Setting Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-sm fa-map-pin"></i> Location Setting</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="LocationForm">
                    <div class="modal-body">
                        <div id="alert2"></div>
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
                                            $hub_query = mysqli_query($conn,"SELECT * FROM tbl_locations");
                                            $count_hub = mysqli_num_rows($hub_query);

                                            $temp =1;   
                                            for($i = 1 ; $i<= $count_hub; $i++){ if($temp == 1) { echo "<tr>"; } 
                                                $fetch_hub = mysqli_fetch_array($hub_query);
                                        ?>
                                                <td>
                                                    <div class="form-check">
                                                        <input type="hidden" name="id[<?php echo $fetch_hub['id']; ?>]" value="<?php echo $fetch_hub['id']; ?>">
                                                        <?php
                                                            echo '<input class="form-check-input" type="hidden" name="status['.$fetch_hub['id'].']" value="0">';
                                                            if($fetch_hub['is_active'] == 1){
                                                                echo '<input class="form-check-input" type="checkbox" name="status['.$fetch_hub['id'].']" checked value="1"  id="flexCheckDefault'.$fetch_hub['id'].'">';
                                                            }else{
                                                                echo '<input class="form-check-input" type="checkbox" name="status['.$fetch_hub['id'].']" value="1" id="flexCheckDefault'.$fetch_hub['id'].'">';
                                                            }
                                                        ?>
                                                            <label class="form-check-label" for="flexCheckDefault<?php echo $fetch_hub['id']; ?>"><?php echo $fetch_hub['location_name'];?></label>
                                                    </div>
                                                </td>
                                        <?php 
                                            if($temp == 2){ echo "</tr>"; $temp = 0; }
                                                $temp++;
                                            }
                                            if($temp-1 != 0 ){ echo '</tr>'; }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="save" name="save" class="btn btn-sm btn-success">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Backup Modal-->
    <div class="modal fade" id="backupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-database fa-sm"></i> Backup Database</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">Do you want to download/backup your current database state?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-success btn-sm" href="backup.php">Backup</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add Role
       $('#AddRoleForm').submit(function(e){
            e.preventDefault();
            var item = $('#AddRoleForm').serialize();
            $.ajax({
                type: "post",
                url: "role_add.php",
                data: item,
                success: function(data) {
                   // $('#PlanForm')[0].reset();
                    RoleList();
                    if(data == '') {
                        RoleList();
                    } else {
                        $('#alert').show();
                        $('#alert').html(data);
                        window.setTimeout(function() {
                            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                                $(this).remove(); 
                            });
                        }, 2000);
                    }
                }
            });
        });

        // Role list
        function RoleList() {
            $.ajax({
                type: "post",
                url: "role_list.php",
                success: function(data) {
                    $('#role-list').html(data);
                }
            });
        }
        RoleList();  

        // Location Setting
       $('#LocationForm').submit(function(e){
            e.preventDefault();
            var item = $('#LocationForm').serialize();
            $.ajax({
                type: "post",
                url: "location_edit.php",
                data: item,
                success: function(data) {
                   // $('#PlanForm')[0].reset();
                    RoleList();
                    $('#alert2').show();
                    $('#alert2').html(data);
                    window.setTimeout(function() {
                        $(".alert2").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, 2000);
                }
            });
        });

        // Reset location setting modal button
        $('.location-btn').click(function(){
            $('#LocationForm')[0].reset();
        });
    </script>