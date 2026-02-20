<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

$rack = strtoupper(str_replace([' ', ','], '', $_POST['rack']));
$column = strtoupper(str_replace([' ', ','], '', $_POST['column']));
$level = strtoupper(str_replace([' ', ','], '', $_POST['level']));
$position = strtoupper(str_replace([' ', ','], '', $_POST['position']));
$location = strtoupper(str_replace([' ', ','], '', $_POST['location']));
$groupno = strtoupper(str_replace([' ', ','], '', $_POST['groupno']));

$sku = !empty($sku) ? $sku : '';
$rackFormatted = !empty($rack) ? 'R' . $rack : '';
$columnFormatted = !empty($column) ? 'C' . $column : '';
$levelFormatted = !empty($level) ? 'L' . $level : '';
$positionFormatted = !empty($position) ? $position : '';

$parts = array_filter([$rackFormatted, $columnFormatted, $levelFormatted, $positionFormatted]); // Remove empty values
$racklocation = implode('-', $parts); // Join remaining parts with '-'

$sku = 'NO SKU';
$principal = 'NULL';
$company = 'NULL';

$check_query = mysqli_query($conn,"SELECT racklocation FROM tbl_inventory_rack WHERE racklocation = '$racklocation' AND location = '$location'");
$check = mysqli_num_rows($check_query);

if($check > 0){

    $qstring = '?status=err';

}else{

    mysqli_query($conn, "INSERT INTO tbl_inventory_rack (id,sku,rack,col,level,pos,racklocation,status,location,principal,company,fin,fin_count,log,log_count,groupno,type,dtr,inv_status) VALUES (NULL,'" .$sku. "', '" .$rack. "', '" .$column. "', '" .$level. "', '" .$position. "', '" .$racklocation. "', 'NOT ENCODED', '" .$location. "', '" .$principal. "', '" .$company. "', '0', '0', '0', '0', '" .$groupno. "', 'MANUAL', NOW(), '0')");

    $action = "ADDED SKU RACK: ".$sku;
    $module = "INVENTORY";
    mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

    $qstring = '?status=succ';
}

// Redirect to the listing page
header("Location: inventory_assign.php".$qstring);