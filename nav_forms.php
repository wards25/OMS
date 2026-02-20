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
                <a class="nav-link" href="dashboard_forms.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Forms
            </div>

            <?php
            if(in_array(89, $permission) || in_array(93, $permission) || in_array(97, $permission) || in_array(163, $permission)){
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseForm"
                        aria-expanded="true" aria-controls="collapseForm">
                        <i class="fas fa-fw fa-warehouse"></i>
                        <span>Warehouse</span>
                    </a>
                    <div id="collapseForm" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Forms:</h6>
                                <?php
                                $links = [
                                    89 => 'pvf.php',
                                    163 => 'svf.php',
                                    97 => 'lvf.php',
                                    93 => 'rvf.php'
                                ];

                                $labels = [
                                    89 => 'Picking Variance',
                                    163 => 'Sorting Variance',
                                    97 => 'Loading Variance',
                                    93 => 'Redel Variance'
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

            if(in_array(167, $permission)){
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCIF"
                        aria-expanded="true" aria-controls="collapseCIF">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Customer Info</span>
                    </a>
                    <div id="collapseCIF" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Forms:</h6>
                            <a href="hcp_raw.php" class="collapse-item">HCP Individual</a>
                            <a href="ins_raw.php" class="collapse-item">HCP Institution</a>
                            <a href="gas_raw.php" class="collapse-item">Gascon</a>
                            <?php
                            if(in_array(166, $permission)){
                                echo '<a href="#" class="collapse-item" data-toggle="modal" data-target="#ReportModal">Export All Data</a>';
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }else{
            }

            if(in_array(175, $permission) || in_array(174, $permission) || in_array(173, $permission)){

                $validate_query = mysqli_query($conn, "SELECT status FROM tbl_ewt_raw WHERE status = '0'");
                $count_validate = mysqli_num_rows($validate_query);

                $invoice_query = mysqli_query($conn, "SELECT status FROM tbl_ewt_raw WHERE status = '1'");
                $count_invoice = mysqli_num_rows($invoice_query);
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEWT"
                        aria-expanded="true" aria-controls="collapseEWT">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>EWT Raw</span>
                    </a>
                    <div id="collapseEWT" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">

                            <h6 class="collapse-header">Forms:</h6>
                                <?php
                                $links = [
                                    175 => 'ewt_search.php',
                                    174 => 'ewt_raw.php',
                                    173 => 'ewt_invoice.php'
                                ];

                                $labels = [
                                    175 => 'Search EWT',
                                    174 => 'Validate EWT',
                                    173 => 'Invoice EWT'
                                ];

                                // Append counts
                                if (isset($count_validate)) {
                                    $labels[174] .= ' <b><span class="text-warning">[' . $count_validate . ']</span></b>';
                                }
                                if (isset($count_invoice)) {
                                    $labels[173] .= ' <b><span class="text-warning">[' . $count_invoice . ']</span></b>';
                                }

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

            if(in_array(178, $permission) || in_array(179, $permission) || in_array(180, $permission)){

                $validate2_query = mysqli_query($conn, "SELECT status FROM tbl_check_raw WHERE status = '0'");
                $count_validate2 = mysqli_num_rows($validate2_query);
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCheck"
                        aria-expanded="true" aria-controls="collapseCheck">
                        <i class="fa-solid fa-credit-card"></i>
                        <span>Check Raw</span>
                    </a>
                    <div id="collapseCheck" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">

                            <h6 class="collapse-header">Forms:</h6>
                                <?php
                                $links = [
                                    180 => 'check_search.php',
                                    179 => 'check_raw.php'
                                ];

                                $labels = [
                                    180 => 'Search Check',
                                    179 => 'Validate Check'
                                ];

                                // Append counts
                                if (isset($count_validate)) {
                                    $labels[179] .= ' <b><span class="text-warning">[' . $count_validate . ']</span></b>';
                                }

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

            if(in_array(19, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="incident_raw.php">
                    <i class="fas fa-fw fa-exclamation-triangle"></i>
                    <span>Incident Report</span></a>
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

    <!-- Report Modal-->
    <div class="modal fade" id="ReportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-download"></i> Export All Data</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">Do you want to export all data?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-success btn-sm" href="cif_export_google.php">Export</a>
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