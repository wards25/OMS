<?php 
session_start();
include('dbconnect.php');
$user = $_SESSION['name'];

//mime type
header('Content-Type: text/csv');
//tell browser what's the file name
header('Content-Disposition: attachment; filename="Inventory Beginning Report as of ' . date('m-d-Y h:i A') . '.csv"');
//no cache
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

fputcsv($output, array('LOCATION','COMPANY','PRINCIPAL','SKU','DESCRIPTION', 'ACTIVE ODOO','ACTIVE OMS','HOLD ODOO','HOLD OMS','ODOO TOTAL','OMS TOTAL','UOM', 'DATA FROM'));

$result = mysqli_query($conn,"SELECT *,sum(active) as active_odoo, sum(hold) as hold_odoo FROM tbl_inventory_ending GROUP BY sku");

while($row = mysqli_fetch_array($result))
{
	$sku = $row['sku'];
    $active_query = mysqli_query($conn, "SELECT SUM(qty) as active_oms FROM (SELECT DISTINCT qty, status, racklocation FROM tbl_inventory_count WHERE sku = '$sku' AND status = 'ACTIVE' AND count_status = 'MATCH') AS distinct_inventory GROUP BY status");
    $fetch_active = mysqli_fetch_assoc($active_query);
    $active_oms = $fetch_active['active_oms'] ?? 0; // Default to 0 if NULL or no row found

    $hold_query = mysqli_query($conn, "SELECT SUM(qty) as hold_oms FROM (SELECT DISTINCT qty, status, racklocation FROM tbl_inventory_count WHERE sku = '$sku' AND status = 'HOLD' AND count_status = 'MATCH') AS distinct_inventory GROUP BY status");
    $fetch_hold = mysqli_fetch_assoc($hold_query);
    $hold_oms = $fetch_hold['hold_oms'] ?? 0; // Default to 0 if NULL or no row found

    $odoo_total = $row['active_odoo'] + $row['hold_odoo'];
    $oms_total = $active_oms + $hold_oms;

	fputcsv($output, array($row['location'],$row['company'],$row['principal'],$row['sku'],$row['description'],$row['active_odoo'],$active_oms,$row['hold_odoo'],$hold_oms,$odoo_total,$oms_total,$row['uom'],$row['status']));
}

$action = $user." EXPORTED BEGINNING FILE CSV";
$module = "INVENTORY";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

fclose($output);

$conn->close();
?>