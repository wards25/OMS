<?php
    $uri = $_SERVER['REQUEST_URI'];
?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #21c275;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
                <div class="sidebar-brand-icon">
                    <img src="img/oms.png" class="img-fluid me-2 sidebar-logo" style="width: 75px; height: 75px; object-fit: contain;">
                </div>
                <div class="sidebar-brand-text ms-2 sidebar-text">
                    <small class="mb-0" style="gap:1px;"><b>Organizational Mgmt System</b></small>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard_trips.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Trips
            </div>

            <?php
            $picker_query = mysqli_query($conn, "SELECT picker, picker_end FROM tbl_trips_picklist WHERE picker = '$user' AND picker_end = ''");
            $count_picker = mysqli_num_rows($picker_query);

            $checker_query = mysqli_query($conn, "SELECT checker, checker_end FROM tbl_trips_picklist WHERE checker = '$user' AND (status='FOR CHECKING' OR status='CHECKING STARTED')");
            $count_checker = mysqli_num_rows($checker_query);

            $invoicing_query = mysqli_query($conn, "SELECT sono FROM tbl_trips_raw GROUP BY sono HAVING SUM(invoicing_status NOT IN ('FOR INVOICING')) = 0");
            $count_invoicing = mysqli_num_rows($invoicing_query);

            $loading_query = mysqli_query($conn, "SELECT * FROM tbl_trips_tm WHERE ic_end = '' AND plateno != ''");
            $count_loading = mysqli_num_rows($loading_query);

            $dispatch_query = mysqli_query($conn, "SELECT * FROM tbl_trips_tm WHERE sorter_end != '' AND dispatch_date = ''");
            $count_dispatch = mysqli_num_rows($dispatch_query);

            $sorting_query = mysqli_query($conn, "SELECT tmno FROM tbl_trips_picklist GROUP BY tmno HAVING SUM(status NOT IN ('FOR SORTING')) = 0");
            $count_sorting = mysqli_num_rows($sorting_query);

            $count_validation = 0;
            if (in_array(111, $permission)) {
                $validation_query = mysqli_query($conn, "SELECT status FROM tbl_trips_picklist WHERE status = 'FOR VALIDATION'");
                $count_validation = mysqli_num_rows($validation_query);
            }

            // === TRIP LIST ONLY ===
            if (in_array(101, $permission)) {
                echo '
                    <li class="nav-item">
                        <a class="nav-link" href="trips.php">
                            <i class="fas fa-fw fa-route"></i>
                            <span>Trip List</span>
                        </a>
                    </li>';
            }

            // === TRIPS & TASKS DROPDOWN ===
            $task_items = [
                109 => ['url' => 'picking.php', 'label' => 'For Picking', 'count' => $count_picker],
                106 => ['url' => 'checking.php', 'label' => 'For Checking', 'count' => $count_checker],
                112 => ['url' => 'validation.php', 'label' => 'For Validation', 'count' => $count_validation],
                115 => ['url' => 'sorting.php', 'label' => 'For Sorting', 'count' => $count_sorting],
                119 => ['url' => 'invoicing.php', 'label' => 'To Invoice', 'count' => $count_invoicing],
                129 => ['url' => 'loading.php', 'label' => 'For Loading', 'count' => $count_loading],
                124 => ['url' => 'dispatch.php', 'label' => 'Dispatch', 'count' => $count_dispatch]
            ];

            $has_task_permission = false;
            foreach (array_keys($task_items) as $key) {
                if (in_array($key, $permission)) {
                    $has_task_permission = true;
                    break;
                }
            }

            if ($has_task_permission) {
            ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTrips"
                        aria-expanded="true" aria-controls="collapseTrips">
                        <i class="fas fa-fw fa-truck"></i>
                        <span>Operations</span>
                    </a>
                    <div id="collapseTrips" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                            foreach ($task_items as $key => $item) {
                                if (in_array($key, $permission)) {
                                    echo '<a class="collapse-item" href="' . htmlspecialchars($item['url']) . '">';
                                    echo htmlspecialchars($item['label']);
                                    if ($item['count'] > 0) {
                                        echo ' <b class="text-warning">[' . $item['count'] . ']</b>';
                                    }
                                    echo '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }
            ?>

            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Tools
            </div>

            <?php
            if (in_array(101, $permission)) {
                echo '
                    <li class="nav-item">
                        <a class="nav-link" href="trips_map.php" target="_blank">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>Maps</span>
                        </a>
                    </li>';
            }
            ?>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            
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
                            <a class="nav-link dropdown-toggle" href="menu.php">
                                <i class="fa-solid fa-grip text-success"></i>
                            </a>
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
        
        document.querySelector(".sidebar-toggle").addEventListener("click", function() {
            document.querySelector(".sidebar").classList.toggle("collapsed");
        });
    </script>

    <style>
        /* By default, show the text */
        .sidebar-text {
            display: block;
        }

        /* When the sidebar has the collapsed class, hide the text */
        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        /* Adjust the image alignment when collapsed */
        .sidebar.collapsed .sidebar-logo {
            margin-right: 0; /* Remove extra spacing */
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: start; /* Align items to the left */
            text-align: left; /* Ensure text inside is left-aligned */
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column; /* Stack text properly */
            text-align: left; /* Align text within */
            line-height: 18px; /* Reduce line spacing when wrapped */
            margin-left: 4px;
        }
    </style>