<?php
session_start();
include 'dbconnect.php'; // Connect to database

header('Content-Type: application/json; charset=utf-8'); // Ensure JSON response

if (isset($_GET['term'])) {
    $search = "%{$_GET['term']}%"; // Prepare search term for SQL LIKE

    // SQL Query to search SKU, Item Code, or Description (Limited to 5 results)
    $query = "SELECT itemcode, sku, description FROM tbl_product 
              WHERE itemcode LIKE ? OR sku LIKE ? OR description LIKE ? 
              ORDER BY itemcode LIMIT 5";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];

    // Fetch results and format JSON response
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'label' => "{$row['sku']} - {$row['description']}", // Display in dropdown
            'sku' => $row['sku'] // Store SKU for hidden input
        ];
    }

    echo json_encode($products); // Return JSON data
    
    $stmt->close();
    $conn->close();
}
?>
