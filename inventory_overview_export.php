<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="Inventory Count Report as of ' . date('m-d-Y h:i A') . '.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

fputcsv($output, array('LOCATION','RACKLOCATION','COMPANY','PRINCIPAL','SKU','DESCRIPTION','QTY','UOM','BBD','STATUS'));

$query = "
    SELECT 
        unique_counts.sku,
        unique_counts.racklocation,
        SUM(unique_counts.qty) AS total_qty,
        unique_counts.uom,        -- Make sure uom is selected
        unique_counts.bbd,
        unique_counts.status,
        unique_counts.location
    FROM (
        SELECT DISTINCT sku, racklocation, qty, uom, bbd, status, location   -- Include uom in the DISTINCT selection
        FROM tbl_inventory_count
        WHERE count_status = 'MATCH'
    ) AS unique_counts
    GROUP BY unique_counts.sku, unique_counts.racklocation, unique_counts.bbd, unique_counts.status, unique_counts.uom, unique_counts.location
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_array($result)) {
    $sku = $row['sku'];

    $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode = '$sku'");
    $check_product = mysqli_num_rows($product_query);

    if ($check_product > 0) {
        $fetch_product = mysqli_fetch_assoc($product_query);
        $company = $fetch_product['vendorcode'];
        $principal = $fetch_product['principal'];
        $description = $fetch_product['description'];
    } else {
        $company = '';
        $principal = '';
        $description = '';
    }

    fputcsv($output, array(
        $row['location'],
        $row['racklocation'],
        $company,
        $principal,
        $sku,
        $description,
        $row['total_qty'],
        $row['uom'],               // Ensure the uom field is added here
        $row['bbd'],
        $row['status']
    ));
}

$action = $user." EXPORTED INVENTORY COUNT FILE CSV";
$module = "INVENTORY";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

fclose($output);

$conn->close();
?>