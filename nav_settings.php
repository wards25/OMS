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
                Settings
            </div>

            <?php
            if($_SESSION['role'] == 'Admin'){
            ?>

            <li class="nav-item">
                <a class="nav-link" data-toggle="modal" data-target="#addroleModal" href="#">
                <i class="fa-solid fa-user-tie"></i>
                <span>Add Position</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="permission.php">
                <i class="fa-solid fa-folder-tree"></i>
                <span>Permission Map</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="account.php">
                <i class="fa-solid fa-users-gear"></i>
                <span>User Accounts</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>Configuration</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Settings:</h6>
                            <a href="company.php" class="collapse-item">Company Setting</a>
                            <a href="deadline.php" class="collapse-item">Deadline Setting</a>
                            <a href="kpi.php" class="collapse-item">KPI Setting</a>
                            <a href="#" class="collapse-item location-btn" data-toggle="modal" data-target="#locationModal">Location Setting</a>
                            <a href="search_raw.php" class="collapse-item">Search Setting</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-toggle="modal" data-target="#backupModal" href="#">
                <i class="fa-solid fa-database"></i>
                <span>Backup Database</span></a>
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