<?php
include 'dbconnect.php'; // your DB connection

if (isset($_POST['ship_to'])) {
    $ship_to = trim($_POST['ship_to']);

    $stmt = $conn->prepare("SELECT branch, address FROM tbl_branch WHERE code = ?");
    $stmt->bind_param("s", $ship_to);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "success" => true,
            "station_name" => $row['branch'],
            "business_address" => $row['address']
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
}
