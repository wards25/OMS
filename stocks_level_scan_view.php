<?php
session_start();
include_once("dbconnect.php");
header('Content-Type: application/json');

$hub    = $_POST['location'] ?? '';
$rack   = $_POST['rack'] ?? '';
$column = $_POST['column'] ?? '';
$level  = $_POST['level'] ?? '';
$pos    = $_POST['pos'] ?? '';
$sku    = $_POST['sku'] ?? '';
$bbd    = $_POST['bbd'] ?? '';
$id     = $_POST['id'] ?? '';

// Default response structure
$data = [
    'id' => '',
    'sku' => '',
    'col' => $column,
    'level' => $level,
    'pos' => $pos,
    'racklocation' => '',
    'rack' => $rack,
    'location' => $hub,
    'description' => '',
    'qty' => 0,
    'bbd' => '',
    'cases' => 0,
    'pieces' => 0,
    'status' => '',
    'received_date' => '',
    'received_by' => ''
];

if (!empty($id)) {

    $stmt = $conn->prepare("SELECT * FROM tbl_inventory_stocks WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $data = [
            'id' => $row['id'],
            'sku' => $row['sku'],
            'col' => $row['col'],
            'level' => $row['level'],
            'pos' => $row['pos'],
            'racklocation' => $row['racklocation'],
            'rack' => $row['rack'],
            'location' => $row['location'],
            'description' => $row['description'],
            'qty' => $row['qty'],
            'bbd' => $row['bbd'],
            'cases' => $row['cases'],
            'pieces' => $row['pieces'],
            'status' => $row['status'],
            'received_date' => $row['received_date'],
            'received_by' => $row['received_by']
        ];
    }

    echo json_encode($data);
    exit;
}

$racklocation = '';

$stmt = $conn->prepare("
    SELECT racklocation 
    FROM tbl_inventory_rack 
    WHERE rack = ? 
      AND col = ? 
      AND level = ? 
      AND pos = ? 
      AND location = ? 
    LIMIT 1
");

$stmt->bind_param("sssss", $rack, $column, $level, $pos, $hub);
$stmt->execute();
$rackRes = $stmt->get_result();

if ($row = $rackRes->fetch_assoc()) {
    $racklocation = $row['racklocation'];
}

if (!empty($racklocation)) {

    $sql = "
        SELECT * 
        FROM tbl_inventory_stocks 
        WHERE racklocation = ? 
          AND location = ?
    ";

    $params = [$racklocation, $hub];
    $types = "ss";

    if (!empty($sku)) {
        $sql .= " AND sku = ?";
        $types .= "s";
        $params[] = $sku;
    }

    if (!empty($bbd)) {
        $sql .= " AND bbd = ?";
        $types .= "s";
        $params[] = $bbd;
    }

    $sql .= " LIMIT 1";

    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param($types, ...$params);
    $stmt2->execute();
    $result = $stmt2->get_result();

    if ($row = $result->fetch_assoc()) {

        $data = [
            'id' => $row['id'],
            'sku' => $row['sku'],
            'col' => $row['col'],
            'level' => $row['level'],
            'pos' => $row['pos'],
            'racklocation' => $row['racklocation'],
            'rack' => $row['rack'],
            'location' => $row['location'],
            'description' => $row['description'],
            'qty' => $row['qty'],
            'bbd' => $row['bbd'],
            'cases' => $row['cases'],
            'pieces' => $row['pieces'],
            'status' => $row['status'],
            'received_date' => $row['received_date'],
            'received_by' => $row['received_by']
        ];
    }
}

echo json_encode($data);
