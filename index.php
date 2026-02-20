<?php
session_start();
include_once("header.php");

if(isset($_SESSION['id']))
{
  header("Location: menu.php");
}
?>
    <body class="d-flex flex-column vh-100">
        <main class="d-flex flex-column justify-content-center align-items-center flex-grow-1 text-center">
            <div class="container" style="max-width: 1200px;"> 
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-5 col-md-8 col-sm-10 mb-4">
                        <h1 class="display-0 fw-bold ls-tight text-primary d-flex justify-content-center align-items-center">
                            <img class="img-fluid me-2" alt="OMS Logo" src="img/oms.png" width="65px">
                            <img class="img-fluid" alt="RGC Logo" src="img/rgc.png" width="220px">
                        </h1>
                        <h1 class="d-none d-md-block" style="color:#0c4562;"><b>Organizational Management System</b></h1>
                        <h3 class="d-block d-md-none" style="color:#0c4562;"><b>Organizational Management System</b></h3>
                        <br>
                        <p style="color: hsl(217, 10%, 50.8%);">
                            Organizational management is the systematic practice of planning, coordinating, and managing 
                            people, resources, and processes, enabling organizations to attain their objectives successfully. 
                            It plays a crucial role in building rapport among employees for collaboration, ensuring that 
                            everyone works towards a common goal. By fostering teamwork, businesses can enhance productivity, 
                            subsequently driving growth and sustainability.
                        </p>
                        <a type="button" class="btn btn-success btn-user btn-block rounded-pill" href="login.php">Get Started</a>
                    </div>
                    <div class="col-lg-7 d-none d-lg-block">
                        <img src="img/asset.png" class="img-fluid">
                    </div>
                </div>
            </div>
        </main>

        <footer class="footer py-3 text-center bg-white">
            <div class="container">
                <?php
                $year = date("Y");
                $version_query = mysqli_query($conn, "
                    SELECT version 
                    FROM tbl_changelog 
                    ORDER BY
                        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 1), 'v', -1) AS UNSIGNED) DESC,
                        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                        CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC
                    LIMIT 1
                ");
                $fetch_version = mysqli_fetch_assoc($version_query);

                echo '<small>&copy; 2024 - ' . $year . ' RGC OMS ' . $fetch_version['version'] . ' | Developed by CAB</small>';
                ?>
            </div>
        </footer>
    </body>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
