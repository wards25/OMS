<?php
include_once("dbconnect.php");

if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("
        SELECT itemcode, description, uom 
        FROM tbl_product 
        WHERE (search_code = ? OR itembarcode = ? OR barcode = ?) 
          AND is_active = '1'
    ");
    
    // Bind parameters (3 placeholders require 3 variables)
    $stmt->bind_param("sss", $barcode, $barcode, $barcode);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the result as an associative array
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["status" => 'success', "itemcode" => $row['itemcode'], "description" => $row['description'], "uom" => $row['uom']]);
    } else {
        echo json_encode(["status" => 'error', 'message' => 'Product not found']);
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
    
} else {
    echo json_encode(["status" => 'error', "message" => "No barcode provided"]);
}
