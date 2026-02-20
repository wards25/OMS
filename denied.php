<?php
include_once("header.php");
?>

<style>
    /* Center the card in the page */
    body, html {
        height: 100%;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #edfaf4;
    }

    .container-fluid {
        width: 100%;
        max-width: 700px; /* Optional: Set max width for the card */
    }

    .card {
        width: 100%;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .lock-circle {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #1cc88a;
        color: white;
        font-size: 36px;
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<body>
    <div class="container-fluid">
        <div class="card shadow mb-4 text-center">
            <div class="card-body">
                <div class="form-group lock-circle">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2>Access denied</h2>
                <p>You don't have permission to access this page.</p>
                <p>Log in or Contact an administrator to get permissions or go to the home page and browse other pages.</p>
                <br>
                <!-- <button type="button" class="btn btn-sm btn-secondary" onclick="history.back();"><i class="fa fa-arrow-left"></i> Go Back</button> -->
                <a type="button" class="btn btn-sm btn-secondary" href="index.php"><i class="fa fa-arrow-left"></i> Go Back</a>
            </div>
        </div>
    </div>
</body>