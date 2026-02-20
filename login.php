<?php
session_start();
include_once("header.php");

if(isset($_SESSION['id']))
{
  header("Location: menu.php");
}
?>

<body class="flex-column h-100" style="background-color: #edfaf4;">

    <div class="container">
        
        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <h1 class="display-0 fw-bold ls-tight text-primary d-flex justify-content-center align-items-center">
                                        <img class="img-fluid me-2" alt="OMS Logo" src="img/oms.png" width="60px">
                                        <img class="img-fluid" alt="RGC Logo" src="img/rgc.png" style="width:210px">
                                    </h1>
                                        <h6 style="color:#0c4562;"><center><b>Organizational Management System</b></center></h6>
                                    <hr>
                                    <form class="user" method="POST" action="loginprocess.php">
                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control form-control-user"
                                                    id="username" aria-describedby="emailHelp"
                                                    placeholder="Enter Username" name="username"
                                                    value="<?php if(isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>"
                                                    required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                                </div>
                                                <input type="password" class="form-control form-control-user"
                                                    placeholder="Password" name="pass"
                                                    value="<?php if(isset($_COOKIE["pass"])) { echo $_COOKIE["pass"]; } ?>"
                                                    required autocomplete="off" id="password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary fa fa-eye" type="button" id="toggle"></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck" name="remember"
                                                    <?php if(isset($_COOKIE["username"])) { echo "checked"; } ?>>
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-user btn-block" name="btn-login">
                                            Login
                                        </button>
                                        <hr>
                                        <div class="text-center">
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

                                        <script>
                                            const passwordEle = document.getElementById('password');
                                            const toggleEle = document.getElementById('toggle');

                                            toggleEle.addEventListener('click', function () {
                                                const type = passwordEle.getAttribute('type');
                                                $(this).toggleClass('fa-eye-slash fa-eye');
                                                passwordEle.setAttribute('type', type === 'password' ? 'text' : 'password');
                                            });
                                        </script>
                                    </form>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                    case 'activate':
                        $statusType = 'alert-warning';
                        $statusMsg = '<i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Please activate your account.';
                        break;
                    case 'err':
                        $statusType = 'alert-danger';
                        $statusMsg = '<i class="fa fa-exclamation-circle"></i>&nbsp;<b>Error!</b> Username / Password seems wrong.';
                        break;
                    case 'session_mismatch':
                        $statusType = 'alert-warning';
                        $statusMsg = '<i class="fa fa-exclamation-circle"></i>&nbsp;<b>Error!</b> You\'ve been logged into another device.';
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

        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
