<?php
session_start();
include_once("dbconnect.php");
$user_id = $_SESSION['id'];

$racklocation = $_POST['rackloc']; // Added missing semicolon

// SQL query to calculate match percentage per rack
$query = "SELECT *
          FROM tbl_inventory_count
          WHERE racklocation = '$racklocation'
          AND user_id = '$user_id'";

$result = mysqli_query($conn, $query);
$rows = mysqli_num_rows($result);

$data = array(); // Initialize $data as an empty array

if ($rows > 0) {
    // Loop through each rack result and display in table
    while ($row = mysqli_fetch_assoc($result)) {

        $product_query = mysqli_query($conn,"SELECT description FROM tbl_product WHERE itemcode = '".$row['sku']."'");
        $fetch_product = mysqli_fetch_assoc($product_query);
        $description = $fetch_product ? $fetch_product['description'] : 'No Description Available';

        // Add each row's data to the $data array
        $data = array(
            'id' => $row['id'],
            'sku' => $row['sku'],
            'description' => $description,
            'col' => $row['col'],
            'level' => $row['level'],
            'pos' => $row['pos'],
            'racklocation' => $racklocation,
            'racklocation1' => $row['racklocation'],
            'qty' => $row['qty'],
            'bbd' => $row['bbd'],
            'uom' => $row['uom'],
            'dt' => $row['dt'],
            'yr' => $row['yr'],
            'cases' => $row['cases'],
            'pieces' => $row['pieces'],
            'status' => $row['status'],
            'rack' => $row['rack'],
            'groupno' => $row['groupno'],
        );
    }
} else {
    // If no results, provide an empty value
    $data = array(
        'id' => '',
        'sku' => '',
        'description' => '',
        'col' => '',
        'level' => '',
        'pos' => '',
        'racklocation' => $racklocation,
        'racklocation1' => '',
        'qty' => '',
        'bbd' => '',
        'uom' =>'',
        'dt' => '',
        'yr' =>'',
        'cases' => '',
        'pieces' => '',
        'status' => '',
        'rack' => '',
        'groupno' => '',
    );
}

// Output the data as JSON
echo json_encode($data);

?>
