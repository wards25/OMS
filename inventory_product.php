<?php
include_once("dbconnect.php");

if (isset($_POST['barcode'])) {
    $barcode = $_POST['barcode'];

    $stmt = $conn->prepare("
        SELECT id, itemcode, description, uom, itembarcode, barcode, vendorcode, principal, is_active
        FROM tbl_product 
        WHERE (search_code = ? OR itembarcode = ? OR barcode = ?) 
          AND is_active = '1'
    ");
    
    $stmt->bind_param("sss", $barcode, $barcode, $barcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "id" => $row['id'],
            "itemcode" => $row['itemcode'],
            "description" => $row['description'],
            "uom" => $row['uom'],
            "itembarcode" => $row['itembarcode'],
            "barcode" => $row['barcode'],
            "vendorcode" => $row['vendorcode'],
            "principal" => $row['principal'],
            "is_active" => $row['is_active']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Product not found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "No barcode provided"]);
}
