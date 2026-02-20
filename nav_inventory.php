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
            <?php
            if(in_array(138, $permission)){
                echo '
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard_inventory.php">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span></a>
                    </li>';
            }
            ?>

            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Inventory
            </div>

            <?php
            if (in_array(138, $permission)) {
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseinventory"
                        aria-expanded="true" aria-controls="collapsecount">
                        <i class="fa-solid fa-barcode"></i>
                        <span>Inventory</span>
                    </a>
                    <div id="collapseinventory" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                            $links = [
                                138 => 'inventory.php'
                            ];

                            $labels = [
                                138 => 'Inventory Summary'
                            ];

                            foreach ($links as $key => $url) {
                                if (in_array($key, $permission) && isset($labels[$key])) {
                                    echo '<a href="' . htmlspecialchars($url) . '" class="collapse-item">' . htmlspecialchars($labels[$key]) . '</a>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }

            if (in_array(143, $permission) || in_array(147, $permission) || in_array(148, $permission)) {
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsecount"
                        aria-expanded="true" aria-controls="collapsecount">
                        <i class="fa-solid fa-list-ol"></i>
                        <span>Inventory Count</span>
                    </a>
                    <div id="collapsecount" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                            $links = [
                                143 => 'inventory_count.php',
                                148 => 'inventory_validate.php',
                                147 => 'inventory_overview.php'
                            ];

                            $labels = [
                                143 => 'Inventory Count',
                                148 => 'Inventory Validation',
                                147 => 'Count Overview'
                            ];

                            foreach ($links as $key => $url) {
                                if (in_array($key, $permission) && isset($labels[$key])) {
                                    echo '<a href="' . htmlspecialchars($url) . '" class="collapse-item">' . htmlspecialchars($labels[$key]) . '</a>';
                                }
                            }

                            if(in_array(143, $permission)) {
                                echo '<a href="inventory_recount.php" class="collapse-item">Inventory Recount</a>';
                            }

                            if($_SESSION['role'] == 'Admin' && in_array(152, $permission)){
                                echo '<a href="#" class="collapse-item" data-toggle="modal" data-target="#countModal">Reset Count</a>';
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }

            if (in_array(153, $permission)) {
            ?>
                <!-- Nav Item - Pages Collapse Menu -->
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseWH"
                        aria-expanded="true" aria-controls="collapseWH">
                        <i class="fas fa-people-group"></i>
                        <span>Group Setting</span>
                    </a>
                    <div id="collapseWH" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Modules:</h6>
                            <?php
                            $links = [
                                153 => 'inventory_assign.php'
                            ];

                            $labels = [
                                153 => 'Group Assignment'
                            ];

                            foreach ($links as $key => $url) {
                                if (in_array($key, $permission) && isset($labels[$key])) {
                                    echo '<a href="inventory_assign_table.php" class="collapse-item">Group Table</a>';
                                    echo '<a href="' . htmlspecialchars($url) . '" class="collapse-item">' . htmlspecialchars($labels[$key]) . '</a>';  
                                }
                            }

                            if($_SESSION['role'] == 'Admin' && in_array(152, $permission)){
                                echo '<a href="#" class="collapse-item" data-toggle="modal" data-target="#resetModal">Reset Assignment</a>';
                            }
                            ?>
                        </div>
                    </div>
                </li>
            <?php
            }

            if(in_array(143, $permission)){
                echo '
                <li class="nav-item">
                    <a class="nav-link" href="inventory_pallet.php">
                    <i class="fa-solid fa-pallet"></i>
                    <span>Warehouse Pallets</span></a>
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

    <!-- Rest Group Modal-->
    <div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-power-off fa-sm"></i> Reset Assignment</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <form id="ResetGroup">
                    <div class="modal-body">
                        <div id="alert"></div>
                        Do you want to reset the group assignment?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Count Modal-->
    <div class="modal fade" id="countModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-power-off fa-sm"></i> Reset Count</h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <form id="ResetCount">
                    <div class="modal-body">
                        <div id="alert2"></div>
                        Do you want to reset the inventory count?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger btn-sm">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Reset Count
        $('#ResetCount').submit(function(e) {
            e.preventDefault();
            var item = $(this).serialize(); // Use 'this' for better scoping
            $.ajax({
                type: "POST",
                url: "inventory_count_reset.php",
                data: item
            }).done(function(response) {
                if (response.trim() !== '') {
                    $('#alert2').html(response).show();
                    setTimeout(function() {
                        $("#alert2").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 2000);
                } else {
                    // $('#resetModal').modal('hide'); Close modal on success
                    location.reload(); // Optional: Refresh the page if needed
                }
            }).fail(function() {
                $('#alert2').html('<div class="alert alert-danger">Error resetting count.</div>').show();
            });
        });

        // Reset Group
        $('#ResetGroup').submit(function(e) {
            e.preventDefault();
            var item = $(this).serialize(); // Use 'this' for better scoping
            $.ajax({
                type: "POST",
                url: "inventory_assign_reset.php",
                data: item
            }).done(function(response) {
                if (response.trim() !== '') {
                    $('#alert').html(response).show();
                    setTimeout(function() {
                        $("#alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove();
                        });
                    }, 2000);
                } else {
                    // $('#resetModal').modal('hide'); Close modal on success
                    location.reload(); // Optional: Refresh the page if needed
                }
            }).fail(function() {
                $('#alert').html('<div class="alert alert-danger">Error resetting assignment.</div>').show();
            });
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