<?php
//error_reporting(0);
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];
$uri = $_SERVER['REQUEST_URI'];

if(!isset($_SESSION['id']))
{
  header("Location: login.php");
}
?>

<style>
    body {
        background-color: #ffffff;
        opacity: 0; /* Initially invisible */
        transition: opacity 0.8s ease-in; /* Smooth fade-in */
    }
    .visible {
        opacity: 1;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.body.classList.add('visible'); // Apply fade-in effect
    });
</script>

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
                <!-- Search Form -->
                <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" style="padding-top:18px;">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small custom-search" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2" id="searchInput" autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn text-secondary" type="button" style="background-color: #edfaf4;">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div id="searchResults" class="list-group position-absolute shadow bg-white" style="z-index: 1000; display: none; width:23%;"></div>
                </form>

                <!-- Search Icon Only for Mobile -->
                <button class="btn text-secondary d-block d-sm-none" type="button" data-toggle="collapse" data-target="#mobileSearch">
                    <i class="fas fa-search fa-sm"></i>
                </button>

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

        <!-- Mobile Search Bar (Collapsible) -->
        <div class="collapse" id="mobileSearch">
            <div class="p-2 bg-white shadow">
                <input type="text" class="form-control form-control-sm bg-light border-0 small" placeholder="Search..." aria-label="Search" id="mobileSearchInput" autocomplete="off">
                <div id="mobileSearchResults" class="list-group position-absolute shadow bg-white" style="z-index: 1000; display: none; width: 90%;"></div>
            </div>
        </div>
        <!-- End of Topbar -->

        <br>
        <!-- Begin Page Content --> 
        <div class="container-fluid">
            <style>
                /* Apply ONLY for mobile landscape (width between 768px - 1024px) */
                @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
                    .row {
                        margin-left: 20px !important;
                        margin-right: 20px !important;
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                    }

                    .icon-card {
                        width: 22%; /* Fit 4 cards per row for high-res landscape */
                        aspect-ratio: 1 / 1;
                        padding: 12px;
                        font-size: 0.9rem;
                    }

                    .icon-card i {
                        font-size: 1.6rem;
                    }

                    .navbar {
                        padding: 8px; /* Adjusted for more space */
                    }

                    .navbar-nav {
                        flex-direction: row !important;
                        justify-content: space-around;
                        width: 100%;
                    }

                    .navbar .form-inline {
                        width: 100%;
                    }

                    #searchResults, #mobileSearchResults {
                        width: 100%;
                    }
                }

                /* Keep existing mobile landscape (<=767px) */
                @media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
                    .row {
                        margin-left: 20px !important;
                        margin-right: 20px !important;
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center;
                        gap: 10px; /* Spacing between cards */
                    }

                    .icon-card {
                        width: calc(100% / 4 - 10px); /* 4 cards per row */
                        aspect-ratio: 1 / 1;
                        padding: 12px;
                        font-size: 0.9rem;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                    }
                }

                /* Ensure desktop view remains unchanged */
                @media (min-width: 1025px) {
                    .row {
                        margin-left: 300px;
                        margin-right: 300px;
                    }

                    .icon-card {
                        width: 100%; /* Restore desktop view */
                        aspect-ratio: unset;
                        padding: 15px;
                        font-size: 1rem;
                    }
                }

                /* General icon-card styling */
                .icon-card {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    border-radius: 10px;
                    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
                    transition: 0.3s;
                    width: 100%;
                    aspect-ratio: 1 / 1;
                    padding: 15px;
                    margin-bottom: 15px;
                }
                .icon-card i {
                    font-size: clamp(1.5rem, 3vw, 2rem);
                    margin-bottom: 5px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .icon-card p {
                    font-size: clamp(0.75rem, 2vw, 1rem);
                    margin: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .icon-card:hover {
                    transform: scale(1.05);
                    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
                }
            </style>

            <div class="row g-3">

                <div class="col-md-2 col-sm-4 col-4 text-center">
                    <a class="icon-card btn btn-lg btn-primary d-flex flex-column justify-content-center align-items-center" href="dashboard_whse.php">
                        <div class="icon-box">
                            <i class="fa fa-warehouse"></i>
                        </div>
                    </a>
                    <p class="icon-label">Warehouse</p>
                </div>

                <?php
                if(in_array(16, $permission) || in_array(27, $permission) || in_array(31, $permission) || in_array(35, $permission) || in_array(39, $permission) || in_array(43, $permission)){
                    echo '
                    <div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-primary d-flex flex-column" href="dashboard_whse.php">
                            <i class="fa fa-warehouse"></i>
                            <p>Warehouse</p>
                        </a>
                    </div>';
                }

                if(in_array(59, $permission) || in_array(67, $permission) || in_array(75, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-warning d-flex flex-column" href="dashboard_admin.php">
                            <i class="fa-solid fa-building-user"></i>
                            <p>Admin</p>
                        </a>
                    </div>';
                }

                if(in_array(101, $permission) || in_array(104, $permission) || in_array(107, $permission) || in_array(110, $permission) || in_array(113, $permission) || in_array(117, $permission) || in_array(122, $permission) || in_array(127, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-success d-flex flex-column" href="dashboard_trips.php">
                            <i class="fa fa-route"></i>
                            <p>Trips</p>
                        </a>
                    </div>';
                }
                
                if(in_array(133, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-danger d-flex flex-column" href="dashboard_clearing.php">
                            <i class="fa fa-stamp"></i>
                            <p>Clearing</p>
                        </a>
                    </div>';
                }
                
                if($_SESSION['role'] == 'Admin'){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-info d-flex flex-column" href="#">
                            <i class="fa-solid fa-fingerprint"></i>
                            <p>HR</p>
                        </a>
                    </div>' ;
                }
                
                if(in_array(3, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg d-flex flex-column text-white" style="background-color:#8540f5;" href="dashboard_employee.php">
                            <i class="fa fa-users"></i>
                            <p>Employees</p>
                        </a>
                    </div>';
                }

                if(in_array(138, $permission) || in_array(148, $permission) || in_array(153, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-info d-flex flex-column" href="dashboard_inventory.php">
                            <i class="fa fa-barcode"></i>
                            <p>Inventory</p>
                        </a>
                    </div>';
                }else if(in_array(143, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-info d-flex flex-column" href="inventory_count.php">
                            <i class="fa fa-barcode"></i>
                            <p>Inventory</p>
                        </a>
                    </div>';
                }else{
                    
                }

                if(in_array(158, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg d-flex flex-column text-white" style="background-color:#8540f5;" href="stocks_rack.php">
                            <i class="fa fa-boxes"></i>
                            <p>Stocks</p>
                        </a>
                    </div>';
                }

                if(in_array(89, $permission) || in_array(93, $permission) || in_array(97, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg d-flex flex-column text-white" style="background-color:#d63384;" href="dashboard_forms.php">
                            <i class="fa fa-list-alt"></i>
                            <p>Forms</p>
                        </a>
                    </div>';
                }

                if($_SESSION['role'] == 'Admin'){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-primary d-flex flex-column" href="dashboard_po.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <p>Purchase</p>
                        </a>
                    </div>';
                }
                
                if(in_array(79, $permission)){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg btn-warning d-flex flex-column" href="dashboard_product.php">
                            <i class="fa-solid fa-box-open"></i>
                            <p>Products</p>
                        </a>
                    </div>';
                }
                
                if($_SESSION['role'] == 'Admin'){
                    echo '<div class="col-md-2 col-sm-4 col-4">
                        <a class="icon-card btn btn-lg d-flex flex-column text-white" style="background-color:#6c757d;" href="dashboard_settings.php">
                            <i class="fa fa-cogs"></i>
                            <p>Settings</p>
                        </a>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

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
    
    <script>
    $(document).ready(function () {
        function performSearch(inputSelector, resultSelector) {
            $(inputSelector).keyup(function () {
                let query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: "search.php",
                        method: "POST",
                        data: { query: query },
                        success: function (data) {
                            $(resultSelector).html(data).show();
                        }
                    });
                } else {
                    $(resultSelector).hide();
                }
            });

            // Hide results when clicking outside
            $(document).on("click", function (e) {
                if (!$(e.target).closest(inputSelector).length) {
                    $(resultSelector).hide();
                }
            });
        }

        // Initialize search for both desktop and mobile
        performSearch("#searchInput", "#searchResults");
        performSearch("#mobileSearchInput", "#mobileSearchResults");
    });

    document.addEventListener("keydown", function(event) {
        if (event.key === "/" && !event.target.matches("input, textarea")) { 
            event.preventDefault(); // Prevent typing "/" in the search box
            document.getElementById("searchInput").focus(); // Focus on the search input
        } 
        
        if (event.key === "Escape") { 
            const searchInput = document.getElementById("searchInput");
            const searchResults = document.getElementById("searchResults");

            if (document.activeElement === searchInput) {
                searchInput.value = ""; // Clear the search input
                searchInput.blur(); // Remove focus from the input
            }

            if (searchResults) {
                searchResults.style.display = "none"; // Hide the search results
            }
        }
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