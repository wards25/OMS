<?php
session_start();
include_once("dbconnect.php");

$user = $_SESSION['name'];
$user_id = $_SESSION['id'];

$name_query = mysqli_query($conn, "SELECT tag FROM tbl_users WHERE id = '$user_id'");
$fetch_name = mysqli_fetch_assoc($name_query);
$tag = $fetch_name['tag'];

$groupno = $_POST['groupno'];
$rack = $_POST['rack'];
$column = $_POST['column'];
$hub = $_POST['hub'];

/* =====================================================
   FETCH RACK POSITIONS
===================================================== */

$query = "
    SELECT * FROM tbl_inventory_rack 
    WHERE rack = '$rack' 
      AND col = '$column' 
      AND groupno = '$groupno' 
      AND location = '$hub'
      AND inv_status = 1
    ORDER BY 
      CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 1), 'R', -1) AS UNSIGNED),
      SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 2), 'C', -1),
      CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(racklocation, '-', 3), 'L', -1) AS UNSIGNED),
      SUBSTRING_INDEX(racklocation, '-', -1)
";

$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {

    $level = $row['level'];
    $racklocation = $row['racklocation'];
    $pos = $row['pos'];

    /* =====================================================
       FETCH & GROUP COUNTS (SUM SAME SKU + BBD)
    ===================================================== */

    $detail_query = "
        SELECT 
            sku,
            bbd,
            SUM(qty) as qty,
            status
        FROM tbl_inventory_count
        WHERE racklocation = '$racklocation' AND department = '$tag'
        GROUP BY sku, bbd, status
        ORDER BY bbd ASC
    ";

    $detail_result = mysqli_query($conn, $detail_query);

    $details = [];
    $statuses = [];

    while ($detail_row = mysqli_fetch_assoc($detail_result)) {
        $details[] = $detail_row;
        $statuses[] = $detail_row['status'];
    }

    /* =====================================================
       ROW COLOR LOGIC
       NOT MATCH > MATCH > DEFAULT
    ===================================================== */

    if (in_array('NOT MATCH', $statuses)) {
        $row_class = 'table-danger';
    } elseif (in_array('MATCH', $statuses)) {
        $row_class = 'table-success';
    } else {
        $row_class = '';
    }

    echo '<tr class="text-center align-middle ' . $row_class . '">';

    /* ================= LEVEL COLUMN ================= */

    echo '<td>';
    echo 'Level ' . htmlspecialchars($level);
    if (!empty($pos)) {
        echo '-' . htmlspecialchars($pos);
    }
    echo '</td>';

    /* ================= COUNT COLUMN ================= */

    echo '<td>';

    if (!empty($details)) {

        foreach ($details as $detail) {

            $formatted_bbd = '';

            if (!empty($detail['bbd']) && $detail['bbd'] != 'EMPTY') {
                $formatted_bbd = date("d-m-Y", strtotime($detail['bbd']));
            }

            echo '<div class="mb-1 small">';

            echo htmlspecialchars($detail['sku']) . ' - ';
            echo htmlspecialchars($detail['qty']) . ' - ';
            echo $formatted_bbd;

            echo '</div>';
        }

    } else {
        echo '<i class="text-muted">EMPTY</i>';
    }

    echo '</td>';

    /* ================= SCAN BUTTON ================= */

    echo '<td>';

    echo '<a href="#" 
            class="btn btn-sm btn-info scan-btn"
            data-toggle="modal"
            data-level="' . htmlspecialchars($level) . '"
            data-pos="' . htmlspecialchars($pos) . '"
            data-racklocation="' . htmlspecialchars($racklocation) . '">
            <i class="fa-solid fa-barcode"></i>
          </a>';

    echo '</td>';

    echo '</tr>';
}
?>