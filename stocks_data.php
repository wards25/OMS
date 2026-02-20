<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$sku = $_GET['sku'];
$location = $_GET['location'];

// Fetch the stock data based on the SKU and location
$stock_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE sku = '$sku' AND location = '$location' ORDER BY bbd ASC");

if (mysqli_num_rows($stock_query) > 0) {
    while($fetch_stock = mysqli_fetch_assoc($stock_query)){

        if($fetch_stock['status'] == 'ACTIVE'){
            echo '<tr class="table-success">';
        }else if($fetch_stock['status'] == 'HOLD'){
            echo '<tr class="table-warning">';
        }else if($fetch_stock['status'] == 'FREE GOODS/PREMIUM'){
            echo '<tr class="table-info">';
        }else{

        }

        echo '<td>' . $fetch_stock['racklocation'] . '</td>';
        echo '<td>' . $fetch_stock['bbd'] . '</td>';
        echo '<td>' . $fetch_stock['qty'] . ' ' . $fetch_stock['uom'] . '</td>';
        echo '<td><a class="btn btn-sm btn-warning" href="stocks_level.php?location=' . $location . '&rack=' . $fetch_stock['rack'] . '&column=' . $fetch_stock['col'] . '&update=x" target="_blank"><i class="fa-solid fa-barcode"></i></a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr>';
    echo '<td colspan="5">No stocks found for this location.</td>';
    echo '</tr>';
}
?>