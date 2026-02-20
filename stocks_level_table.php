<?php
session_start();
include_once("dbconnect.php");

$user = $_SESSION['name'] ?? '';
$user_id = $_SESSION['id'] ?? '';

$rack = $_POST['rack'] ?? '';
$column = $_POST['column'] ?? '';
$hub = $_POST['hub'] ?? '';

if (empty($rack) || empty($column) || empty($hub)) {
    exit;
}

/* =====================================================
   FETCH RACK POSITIONS
===================================================== */

$stmt = $conn->prepare("
    SELECT *
    FROM tbl_inventory_rack
    WHERE rack COLLATE utf8mb4_general_ci = ?
      AND col COLLATE utf8mb4_general_ci = ?
      AND location COLLATE utf8mb4_general_ci = ?
    ORDER BY 
        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 1), 'R', -1) AS UNSIGNED),
        SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 2), 'C', -1),
        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 3), 'L', -1) AS UNSIGNED),
        SUBSTRING_INDEX(racklocation, '-', -1)
");

$stmt->bind_param("sss", $rack, $column, $hub);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    $level = $row['level'];
    $racklocation = $row['racklocation'];
    $pos = $row['pos'];

    /* =====================================================
       FETCH & GROUP STOCKS (SUM SAME SKU + BBD)
    ===================================================== */

    $stock_stmt = $conn->prepare("
        SELECT 
            MIN(s.id) as id,
            s.sku, 
            SUM(s.qty) as qty, 
            s.bbd, 
            s.status,
            p.uom
        FROM tbl_inventory_stocks s
        LEFT JOIN tbl_product p 
            ON s.sku COLLATE utf8mb4_general_ci = p.itemcode COLLATE utf8mb4_general_ci
        WHERE s.racklocation COLLATE utf8mb4_general_ci = ?
          AND s.location COLLATE utf8mb4_general_ci = ?
        GROUP BY s.sku, s.bbd, s.status, p.uom
        ORDER BY s.bbd ASC
    ");

    $stock_stmt->bind_param("ss", $racklocation, $hub);
    $stock_stmt->execute();
    $stock_result = $stock_stmt->get_result();

    $stocks = [];
    $statuses = [];

    while ($stock_row = $stock_result->fetch_assoc()) {
        $stocks[] = $stock_row;
        $statuses[] = $stock_row['status'];
    }

    /* =====================================================
       STATUS PRIORITY LOGIC
       HOLD > FREE GOODS > ACTIVE
    ===================================================== */

    if (in_array('HOLD', $statuses)) {
        $row_class = 'table-warning';
    } elseif (in_array('FREE GOODS/PREMIUM', $statuses)) {
        $row_class = 'table-info';
    } elseif (in_array('ACTIVE', $statuses)) {
        $row_class = 'table-success';
    } else {
        $row_class = '';
    }

    echo '<tr class=" text-center align-middle">';

    /* ================= LEVEL COLUMN ================= */

    echo '<td>';
    echo 'Level ' . htmlspecialchars($level);
    if (!empty($pos)) {
        echo '-' . htmlspecialchars($pos);
    }
    echo '</td>';

    /* ================= STOCK COLUMN ================= */

    echo '<td class="' . $row_class . '">';

    if (!empty($stocks)) {

        foreach ($stocks as $stock) {
            $stock_class = '';

            // if ($stock['status'] === 'HOLD') {
            //     $stock_class = 'bg-warning text-white';
            // } elseif ($stock['status'] === 'FREE GOODS/PREMIUM') {
            //     $stock_class = 'bg-info text-white';
            // } elseif ($stock['status'] === 'ACTIVE') {
            //     $stock_class = 'bg-success text-white';
            // }

            echo '<div class="d-flex justify-content-center align-items-center mb-1 ' . $stock_class . '">';


            echo '<span class="small">';
            echo htmlspecialchars($stock['sku']) . ' - ';
            echo htmlspecialchars($stock['qty']) . ' ';
            echo htmlspecialchars($stock['uom'] ?? '') . ' - ';
            echo htmlspecialchars($stock['bbd']);
            echo '</span>';

            echo '<button 
                    class="btn btn-outline-dark ms-2 edit-bbd-btn"
                    style="padding:2px 6px; font-size:0.7rem;"
                    data-id="' . $stock['id'] . '"
                    data-bs-toggle="modal"
                    data-bs-target="#scanModal">
                    <i class="fa fa-edit"></i>
                  </button>';

            echo '</div>';
        }

    } else {
        echo '<i class="text-muted">EMPTY</i>';
    }

    echo '</td>';

    /* ================= SCAN BUTTON ================= */

    echo '<td>';

    echo '<button 
            class="btn btn-sm btn-info scan-btn"
            data-level="' . htmlspecialchars($level) . '"
            data-pos="' . htmlspecialchars($pos) . '"
            data-racklocation="' . htmlspecialchars($racklocation) . '"
            data-bs-toggle="modal"
            data-bs-target="#scanModal">
            <i class="fa-solid fa-barcode"></i>
          </button>';

    echo '</td>';

    echo '</tr>';
}
?>