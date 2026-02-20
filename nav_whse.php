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
                <a class="nav-link" href="dashboard_whse.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Warehouse
            </div>

            <li class="nav-item">
                <a class="nav-link" href="checklist.php">
                <i class="fa-regular fa-square-check"></i>
                <span>Daily Checklist</span></a>
            </li>

            <?php
            if(in_array(16, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="asset_raw.php">
                    <i class="fa-solid fa-dolly"></i>
                    <span>Asset Audit</span></a>
                </li>';
            }else{
            }

            if(in_array(39, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="ofattendance_raw.php">
                    <i class="fa-solid fa-clipboard-user"></i>
                    <span>Office Attendance</span></a>
                </li>';
            }else{
            }

            if(in_array(43, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="temp_raw.php">
                    <i class="fa-solid fa-temperature-three-quarters"></i>
                    <span>Temp Monitoring</span></a>
                </li>';
            }else{
            }

            if(in_array(31, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="attendance_raw.php">
                    <i class="fa-solid fa-users-between-lines"></i>
                    <span>WH Attendance</span></a>
                </li>';
            }else{
            }

            if(in_array(35, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="zone_raw.php">
                    <i class="fa-solid fa-map-pin"></i>
                    <span>Zone Audit</span></a>
                </li>';
            }else{
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