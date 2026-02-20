<?php
session_start();
include_once("dbconnect.php");

$user_id = $_SESSION['id'] ?? '';

$location = $_POST['location'] ?? '';
$racklocation = $_POST['rackloc'] ?? '';
$sku = $_POST['sku'] ?? ''; // optional, to filter a specific SKU
$bbd = $_POST['bbd'] ?? ''; // optional, to filter a specific batch

$data = [
    'id' => '',
    'sku' => '',
    'description' => '',
    'col' => '',
    'level' => '',
    'pos' => '',
    'racklocation' => $racklocation,
    'racklocation1' => '',
    'qty' => 0,
    'bbd' => '',
    'uom' => '',
    'dt' => '',
    'yr' => '',
    'cases' => 0,
    'pieces' => 0,
    'status' => '',
    'rack' => ''
];

// Build SQL
$sql = "SELECT *
        FROM tbl_inventory_stocks
        WHERE racklocation = ? AND location = ?";
$params = [$racklocation, $location];
$types = "ss";

if ($sku !== '') {
    $sql .= " AND sku = ?";
    $types .= "s";
    $params[] = $sku;
}

if ($bbd !== '') {
    $sql .= " AND bbd = ?";
    $types .= "s";
    $params[] = $bbd;
}

$sql .= " ORDER BY bbd ASC, id DESC LIMIT 1"; // latest stock if multiple rows

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $data = [
        'id' => $row['id'],
        'sku' => $row['sku'],
        'description' => $row['description'],
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
        'rack' => $row['rack']
    ];
}

echo json_encode($data);
?>
