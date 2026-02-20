<?php
session_start();
include_once("dbconnect.php");

$user = $_SESSION['name'];
$rack = $_POST['rack'];
$column = $_POST['column'];
$groupno = $_POST['groupno'];
$hub = $_SESSION['hub'];
$level = $_POST['level'];
$pos = $_POST['pos'];

// SQL query to calculate match percentage per rack
$query = "SELECT *
          FROM tbl_inventory_rack 
          WHERE rack = '$rack'
          AND col = '$column'
          AND groupno = '$groupno'
          AND level = '$level' 
          AND pos = '$pos' 
          AND location = '$hub'";

$result = mysqli_query($conn, $query);
$rows = mysqli_num_rows($result);

$data = array(); // Initialize $data as an empty array

if ($rows > 0) {
    // Loop through each rack result and display in table
    while ($row = mysqli_fetch_assoc($result)) {

        $racklocation = $row['racklocation'];
        $rack_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_count WHERE name = '$user' AND racklocation = '$racklocation' AND location = '$hub'");
        $fetch_rack = mysqli_fetch_assoc($rack_query);
        $sku = $fetch_rack ? $fetch_rack['sku'] : '';
        $qty = $fetch_rack ? $fetch_rack['qty'] : '';
        $bbd = $fetch_rack ? $fetch_rack['bbd'] : '';
        $cases = $fetch_rack ? $fetch_rack['cases'] : '';
        $pieces = $fetch_rack ? $fetch_rack['pieces'] : '';
        $status = $fetch_rack ? $fetch_rack['status'] : '';
        
        $product_query = mysqli_query($conn, "SELECT description FROM tbl_product WHERE itemcode = '$sku'");
        $fetch_product = mysqli_fetch_assoc($product_query);
        $description = $fetch_product ? $fetch_product['description'] : '';

        // Add each row's data to the $data array
        $data = array(
            'id' => $row['id'],
            'sku' => $sku,
            'col' => $row['col'],
            'level' => $row['level'],
            'pos' => $row['pos'],
            'racklocation' => $row['racklocation'],
            'groupno' => $row['groupno'],
            'rack' => $row['rack'],
            'location' => $row['location'],
            'description' => $description,
            'qty' => $qty,
            'bbd' => $bbd,
            'cases' => $cases,
            'pieces' => $pieces,
            'status' => $status
        );
    }
} else {
    // If no results, provide an empty value
    $data = array(
        'id' => '',
        'sku' => '',
        'col' => '',
        'level' => '',
        'pos' => '',
        'racklocation' => '',
        'groupno' =>'',
        'rack' => '',
        'location' => '',
        'description' => '',
        'qty' => '',
        'bbd' => '',
        'cases' => '',
        'pieces' => '',
        'status' => ''
    );
}

// Output the data as JSON
echo json_encode($data);

?>
