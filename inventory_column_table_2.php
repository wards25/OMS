<?php
session_start();
include 'dbconnect.php'; // Ensure correct DB connection

header('Content-Type: application/json'); // Ensure JSON output
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hub = $_SESSION['hub'] ?? '';
$groupno = $_GET['groupno'] ?? '';
$rack = $_GET['rack'] ?? '';

// Validate input to prevent SQL injection
$groupno = mysqli_real_escape_string($conn, $groupno);
$rack = mysqli_real_escape_string($conn, $rack);
$hub = mysqli_real_escape_string($conn, $hub);

// SQL query to fetch match percentage per column
$query = "SELECT groupno, col,
                 COUNT(*) AS total, 
                 SUM(CASE WHEN status = 'MATCH' THEN 1 ELSE 0 END) AS match_count 
          FROM tbl_inventory_rack 
          WHERE rack = '$rack' 
          AND groupno = '$groupno' 
          AND location = '$hub'
          GROUP BY col";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "Query error: " . mysqli_error($conn)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $percentage = ($row['total'] > 0) ? round(($row['match_count'] / $row['total']) * 100, 2) : 0;
    $data[] = [
        'column' => $row['col'],
        'percentage' => $percentage,
        'groupno' => $row['groupno']
    ];
}

echo json_encode($data);
?>
