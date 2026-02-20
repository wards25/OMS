<?php
session_start();
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

<?php
$user = $_SESSION['name'];

if(isset($_SESSION['id']))
{
?>

<style>
    /*@media print {
        @page {
            size: landscape;
        }
    }*/
    .container-fluid {
      width: 100%; /* Ensure it uses the full width */
      padding: 0;  /* Remove any padding */
    }
    img {
      max-width: 100%; /* Ensure images fit the page */
      height: auto;
    }
    .table {
      width: 100%; /* Tables should take the full width */
      border-collapse: collapse; /* Remove gaps */
    }
    th, td {
      padding: 5px; /* Add some padding */
      border: 1px solid #000; /* Ensure borders are visible */
    }
    /* Hide unnecessary elements for printing */
    .no-print {
      display: none; 
    }
    .top, .bottom {
        height: 50%;
        box-sizing: border-box;
    }
    .nowrap {
      white-space: nowrap;
    }
  }
</style>

    <!-- Begin Page Content -->
    <div class="container-fluid">     
        <div class="table-responsive">
            <table class="table table-bordered table-sm text-center" style="color:#000000;">
                <thead>
                    <tr class="text-center">
                        <th hidden></th>
                        <th>Group</th>
                        <th>Rack #</th>
                        <th>Rack Count</th>
                        <th>Finance Counter</th>
                        <th>Logistics Counter</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php
                    $group_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_group ORDER BY groupno");
                    while ($fetch_group = mysqli_fetch_assoc($group_query)) {
                        $groupno = $fetch_group['groupno'];

                        $rack_query = mysqli_query($conn, "SELECT rack FROM tbl_inventory_rack WHERE groupno = '$groupno'");
                        $rack_count = mysqli_num_rows($rack_query);
                        $rack_numbers = [];
                        while ($rack = mysqli_fetch_assoc($rack_query)) {
                            $rack_numbers[] = $rack['rack'];
                        }

                        // Remove duplicates
                        $rack_numbers = array_unique($rack_numbers);

                        // Join rack numbers with a comma
                        $rack_list = implode(", ", $rack_numbers);

                        echo '<tr>';
                        echo '<td hidden>' . $fetch_group['groupno'] . '</td>';
                        echo '<td>Group ' . $fetch_group['groupno'] . '</td>';
                        echo '<td>' . $rack_list . '</td>'; // Show rack numbers without duplicates
                        echo '<td>' . $rack_count . '</td>'; // Show rack count
                        echo '<td>' . $fetch_group['fin_name'] . '</td>';
                        echo '<td>' . $fetch_group['log_name'] . '</td>';
                        echo '<td>' . $fetch_group['location'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

</body>

</html>

<?php
}else{
    header("Location: denied.php");
}