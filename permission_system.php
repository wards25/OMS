<?php
session_start();
include_once("dbconnect.php");

$user = $_SESSION['name'];
$update_id = $_SESSION['update_id'];
$term = isset($_POST['term']) ? mysqli_real_escape_string($conn, $_POST['term']) : ''; // Sanitize and fetch 'term'

$query = "
    SELECT * 
    FROM tbl_permissions 
    WHERE permission_name LIKE '%$term%' 
    OR module LIKE '%$term%' ORDER BY module
";

// Fetch all permissions with the term filter
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

while ($row = mysqli_fetch_array($result)) {
    $permission_id = $row['id'];

    // Check if this permission is assigned to the user
    $permission_query = mysqli_query($conn, "SELECT * FROM tbl_system_permissions WHERE user_id = '$update_id' AND permission_id = '$permission_id'");
    $fetch_permission = mysqli_num_rows($permission_query);

    // Display permission only if it's not assigned to the user
    if ($fetch_permission == 0) {
?>
        <tr>
            <td><center><?php echo htmlspecialchars($row['permission_name']); ?></center></td>
            <td><center><?php echo htmlspecialchars($row['module']); ?></center></td>
            <td style="width:10%;"><center><a type="button" class="text-success btn-xs add-btn2" data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fa fa-plus fa-xs"></i></a></center></td>
        </tr>
<?php
    }
}
?>