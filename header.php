<?php
include_once("dbconnect.php");

if(isset($_SESSION['id']))
{    
    $permission_query = mysqli_query($conn, "SELECT * FROM tbl_system_permissions WHERE user_id=" . $_SESSION['id']);
    $permission = []; // Initialize the header array
    while ($fetch_permission = mysqli_fetch_array($permission_query)) {
        $permission[] = $fetch_permission['permission_id'];
    }

    // Fetch locations
    $location_query = mysqli_query($conn, "SELECT tbl_user_locations.user_id, tbl_user_locations.location_id, tbl_locations.location_name, tbl_locations.is_active 
        FROM tbl_user_locations 
        LEFT JOIN tbl_locations ON tbl_user_locations.location_id = tbl_locations.id 
        WHERE tbl_locations.is_active = 1 
        AND tbl_user_locations.user_id = " . $_SESSION['id']);

    $check_location = mysqli_num_rows($location_query);

    $locations = []; // Initialize an array to store location names

    if ($check_location > 0) {
        while ($fetch_location = mysqli_fetch_array($location_query)) {
            $locations[] = mysqli_real_escape_string($conn, $fetch_location['location_name']); // Escape each value
        }
    }

    // Format the location for SQL query
    if (count($locations) > 1) {
        $location = "'" . implode("','", $locations) . "'"; // Format for SQL IN clause
    } else {
        $location = "'" . $locations[0] . "'"; // Single value, still needs quotes
    }
    
}else{
}

ob_start();
date_default_timezone_set("Asia/Manila");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/oms.png">
    <title>RGC | OMS</title>

    <!-- FONT AWESOME 6-->
    <link href="fa-6/css/all.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!--<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">--> 

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css' rel='stylesheet' />  
    
    <!-- Mobile Assets -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/webcam.min.js"></script>

    <!--Announcement Modal-->
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>

    <!-- Select2 CSS --> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" integrity="sha512-kq3FES+RuuGoBW3a9R2ELYKRywUEQv0wvPTItv3DSGqjpbNtGWVdvT8qwdKkqvPzT93jp8tSF4+oN4IeTEIlQA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.css" integrity="sha512-CbQfNVBSMAYmnzP3IC+mZZmYMP2HUnVkV4+PwuhpiMUmITtSpS7Prr3fNncV1RBOnWxzz4pYQ5EAGG4ck46Oig==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Select2.js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Geolocation -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    
</head>

<!-- <style>
    .nav-item {
    margin-bottom: -5px !important; /* Adjust space between items */
    }
</style> -->

<!-- Page BG -->
<style>
#content-wrapper {
    background-color: #edfaf4 !important;
}
</style>

<!-- Floating search button -->
<style>
    /* Floating Search Button */
    .floating-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #21c275;
        color: #fafafa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: 0.3s;
        z-index: 1050;
    }
    .floating-btn:hover {
        background-color: #17a673;
    }
    /* Floating Search Box */
    .floating-search-box {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 250px;
        background: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
        z-index: 1050;
    }
    /* Position search results above the input */
    #floatingSearchResults {
        position: absolute;
        bottom: 100%; /* Moves it above the input box */
        left: 0;
        width: 100%;
        background: white;
        border-radius: 8px;
        box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        display: none;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ddd;
    }
    /* Chat popup */
    .chat-popup {
        display: none;
        position: fixed;
        bottom: 140px; /* Above floating buttons */
        right: 20px;
        width: 300px;
        max-height: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        flex-direction: column;
        overflow: hidden;
        z-index: 1100;
    }
    .chat-header {
        background: #007bff;
        color: white;
        padding: 8px 10px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-body {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        max-height: 280px;
        background: #f9f9f9;
    }
    .chat-footer {
        display: flex;
        padding: 5px;
        background: #f1f1f1;
    }
    .chat-footer input {
        flex: 1;
        margin-right: 5px;
    }
    .close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }
</style>

<?php if (
    basename($_SERVER['PHP_SELF']) !== 'index.php' &&
    basename($_SERVER['PHP_SELF']) !== 'login.php' &&
    basename($_SERVER['PHP_SELF']) !== 'load.php' &&
    basename($_SERVER['PHP_SELF']) !== 'denied.php' &&
    basename($_SERVER['PHP_SELF']) !== 'menu.php'
) : ?>
    <!-- Floating Search Btn -->
    <div id="floatingSearchBtn" class="floating-btn" style="bottom:20px;">
        <i class="fas fa-search"></i>
    </div>
    <div class="floating-search-box" id="floatingSearchBox">
        <input type="text" class="form-control form-control-sm" id="floatingSearchInput" placeholder="Search..." oninput="showSearchResults()" autocomplete="off">
        <div id="floatingSearchResults" class="list-group position-absolute shadow bg-white" style="z-index: 1000; display: none;"></div>
    </div>

    <!-- Floating Messages Btn + Chat Popup (only if logged in) -->
    <?php if (isset($_SESSION['id'])): ?>
        <div id="floatingMessagesBtn" class="floating-btn" style="bottom:80px; background-color:#007bff;">
            <i class="fas fa-comments"></i>
        </div>

        <div id="chatPopup" class="chat-popup">
            <div class="chat-header">
                <span>Messages</span>
                <button id="closeChat" class="close-btn">&times;</button>
            </div>
            <div class="chat-body" id="chatBody"></div>
            <div class="chat-footer">
                <input type="text" id="chatMessage" placeholder="Type a message..." class="form-control form-control-sm">
                <button id="sendMessage" class="btn btn-primary btn-sm"><i class="fa-solid fa-paper-plane"></i></button>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- <script>
    $(document).ready(function () {
        // Toggle chat popup
        $("#floatingMessagesBtn").click(function () {
            $("#chatPopup").toggle();
            loadMessages();
        });

        $("#closeChat").click(function () {
            $("#chatPopup").hide();
        });

        // Send message function
        function sendMessage() {
            let msg = $("#chatMessage").val().trim();
            if (msg !== "") {
                $.post("chat_message.php", { message: msg }, function () {
                    $("#chatMessage").val("");
                    loadMessages();
                });
            }
        }

        // Click button
        $("#sendMessage").click(sendMessage);

        // Press Enter to send
        $("#chatMessage").keydown(function (e) {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault(); // Prevent newline
                sendMessage();
            }
        });

        // Load messages
        function loadMessages() {
            $.get("chat_fetch.php", function (data) {
                $("#chatBody").html(data);
                $("#chatBody").scrollTop($("#chatBody")[0].scrollHeight);
            });
        }

        // Polling every 3 seconds
        setInterval(loadMessages, 3000);
    });

    $(document).ready(function () {
        // Toggle search input visibility on button click
        $("#floatingSearchBtn").click(function () {
            $("#floatingSearchBox").toggle();
            $("#floatingSearchInput").focus();
        });

        // Show search box when "/" is typed (except inside input fields)
        $(document).on("keydown", function (e) {
            if (e.key === "/" && !$(e.target).is("input, textarea")) {
                e.preventDefault(); // Prevent typing "/" in search input
                $("#floatingSearchBox").show();
                $("#floatingSearchInput").focus();
            } else if (e.key === "Escape") {
                // Hide search box when ESC is pressed
                $("#floatingSearchBox").hide();
            }
        });

        // Perform search
        $("#floatingSearchInput").keyup(function () {
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: "search.php",
                    method: "POST",
                    data: { query: query },
                    success: function (data) {
                        $("#floatingSearchResults").html(data).show();
                    }
                });
            } else {
                $("#floatingSearchResults").hide();
            }
        });

        // Hide search when clicking outside
        $(document).on("click", function (e) {
            if (!$(e.target).closest("#floatingSearchBox, #floatingSearchBtn").length) {
                $("#floatingSearchBox").hide();
            }
        });
    });
</script> -->