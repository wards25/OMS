<?php
session_start();
include_once("dbconnect.php");

// Check if the user is logged in and update_id is set
if (!isset($_SESSION['name']) || !isset($_SESSION['update_id'])) {
    die('User not authenticated or update_id not set.');
}

$user = $_SESSION['name'];
$update_id = intval($_SESSION['update_id']); // Sanitize the user input

// Prepare the base query
$query = "
    SELECT sp.*, p.permission_name, p.module
    FROM tbl_system_permissions sp  
    INNER JOIN tbl_permissions p ON sp.permission_id = p.id
    WHERE sp.user_id = ?
";

// Check if there is a search term and modify the query accordingly
if (isset($_POST['term']) && !empty($_POST['term'])) {
    $term = '%' . $_POST['term'] . '%'; // Use '%' for LIKE clause
    $query .= " AND (p.module LIKE ? OR p.permission_name LIKE ?)";
}

// Add LIMIT clause to the query
$query .= " ORDER BY p.module";

// Prepare and execute the statement
$stmt = $conn->prepare($query);

// Bind parameters based on the query
if (isset($term)) {
    $stmt->bind_param("sss", $update_id, $term, $term);
} else {
    $stmt->bind_param("i", $update_id);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if there are results and display them
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <tr>
            <td><center><?php echo htmlspecialchars($row['permission_name']); ?></center></td>
            <td><center><?php echo htmlspecialchars($row['module']); ?></center></td>
            <td style="width:10%;">
                <center><a type="button" class="text-danger btn-xs delete-btn" data-id="<?php echo htmlspecialchars($row['id']); ?>"><i class="fa fa-times fa-xs"></i></a></center>
            </td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='3'><center>No permission added.</center></td></tr>";
}

$stmt->close();
$conn->close();
?>