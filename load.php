<?php
ob_start();
session_start();
include_once("header.php");
include_once("dbconnect.php");
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$res = mysqli_query($conn, "SELECT * FROM tbl_users WHERE id=" . $_SESSION['id']);
$userRow = mysqli_fetch_array($res);
?>

<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

<style>
    body {
        background-color: #edfaf4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        transition: opacity 1s ease-out;
    }

    .loading-container {
        width: 300px;
        height: 10px;
        background-color: #d4edda;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .loading-bar {
        width: 0;
        height: 100%;
        background-color: #28a745;
        animation: load 2s linear forwards;
    }

    @keyframes load {
        0% { width: 0; }
        100% { width: 100%; }
    }

    /* Fade-out effect */
    .fade-out {
        opacity: 0;
    }
</style>

<script>
    setTimeout(() => {
        document.body.classList.add('fade-out'); // Apply fade-out effect
        setTimeout(() => {
            window.location.href = "menu.php"; // Redirect after fade-out
        }, 1000); // Match this with CSS transition time
    }, 2300); // Wait for progress bar animation
</script>

<body>
    <div class="loading-container">
        <div class="loading-bar"></div>
    </div>
</body>

    <script>
        var su = new SpeechSynthesisUtterance();
        su.lang = "en";
        su.text = "Welcome to O,M,S, <?php echo $_SESSION['name']; ?>!";
        speechSynthesis.speak(su);
    </script>

    <!-- <footer class="sticky-footer fixed-bottom" style="background-color: #ffffff;">
      <div class="container my-auto">
        <div class="copyright text-center my-auto">
          <?php
            $year = date("Y");
          ?>
        <span>&copy; 2024 - <?php echo $year; ?> RGC OMS | Developed by RGC IT</span>
        </div>
      </div>
    </footer> -->
  </center>                  