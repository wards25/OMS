<?php
session_start();  // Ensure the session is started to access $_SESSION variables
include_once("dbconnect.php");
$user = $_SESSION['name'];
$hub = $_SESSION['hub'];
$groupno = $_GET['groupno'];
$rack = $_GET['rack'];

$query = "SELECT col, 
                (COUNT(CASE WHEN status = 'MATCH' THEN 1 END) / COUNT(*)) * 100 AS avg_match, 
                groupno,
                (SUM(COALESCE(fin_count, 1)) / COUNT(*)) * 100 AS avg_fin_count,
                (SUM(COALESCE(log_count, 1)) / COUNT(*)) * 100 AS avg_log_count
        FROM tbl_inventory_rack 
        WHERE rack = ? 
        AND groupno = ? 
        AND location = ? 
        AND inv_status = 1
        GROUP BY col
        ORDER BY CAST(col AS UNSIGNED) ASC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("sss", $rack, $groupno, $hub);

// Execute the query
$stmt->execute();

// Get result
$result = $stmt->get_result();

// Check if there are results
if ($result->num_rows > 0) {
    // Loop through each rack result and display in table rows
    while ($row = $result->fetch_assoc()) {
        $column = htmlspecialchars($row['col']);
        $avg_match = number_format($row['avg_match'], 2); // Format to 2 decimal places
        $groupno_safe = htmlspecialchars($row['groupno']);
        $rack_safe = htmlspecialchars($rack);
        
        echo '<tr>';
        echo '<td>' . $column . '</td>';

        if($_SESSION['tag'] == 'FINANCE'){
            echo '<td>' . number_format($row['avg_fin_count'], 2) . '%</td>';
        } else {
            echo '<td>' . number_format($row['avg_log_count'], 2) . '%</td>';
        }

        if ($avg_match == 100) {
            echo '<td class="table-success">' . $avg_match . '%</td>';
        } else {
            echo '<td class="table-warning">' . $avg_match . '%</td>';
        }

        echo '<td><a class="btn btn-sm btn-success" href="inventory_level.php?groupno=' . $groupno_safe . '&rack=' . $rack_safe . '&column=' . $column . '"><i class="fa-solid fa-arrow-right-to-bracket"></i></a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No data available</td></tr>';
}

// Close statement
$stmt->close();
?>
