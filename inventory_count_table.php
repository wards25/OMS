<?php
session_start();
include 'dbconnect.php'; // Ensure correct DB connection

$hub = $_SESSION['hub'];
$groups = [];

// Fetch group numbers based on session tag
if ($_SESSION['tag'] == 'FINANCE') {
    $group_query = mysqli_query($conn, "SELECT groupno FROM tbl_inventory_group WHERE fin_id=" . $_SESSION['id']);
} else {
    $group_query = mysqli_query($conn, "SELECT groupno FROM tbl_inventory_group WHERE log_id=" . $_SESSION['id']);
}

if ($group_query && mysqli_num_rows($group_query) > 0) {
    while ($fetch_group = mysqli_fetch_assoc($group_query)) {
        $groups[] = mysqli_real_escape_string($conn, $fetch_group['groupno']);
    }
}

$groupno = (count($groups) > 0) ? "'" . implode("','", $groups) . "'" : "''";

// Query to calculate match percentages
$query = "SELECT groupno, rack, 
                 COUNT(*) AS total, 
                 SUM(CASE WHEN status = 'MATCH' THEN 1 ELSE 0 END) AS match_count,
                 AVG(COALESCE(fin_count, 1)) AS avg_fin_count,
                 AVG(COALESCE(log_count, 1)) AS avg_log_count
          FROM tbl_inventory_rack 
          WHERE groupno IN ($groupno) 
          AND location = '$hub'
          AND inv_status = 1
          AND groupno IS NOT NULL 
          AND groupno <> ''
          AND groupno <> '0'
          GROUP BY rack
          ORDER BY LENGTH(rack), rack ASC";

$result = mysqli_query($conn, $query);

$data = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $percentage = ($row['total'] > 0) ? round(($row['match_count'] / $row['total']) * 100, 2) : 0;

        if ($_SESSION['tag'] == 'FINANCE') {
            $count_percentage = $row['avg_fin_count'];
        } else {
            $count_percentage = $row['avg_log_count'];
        }

        $data[] = [
            'rack' => $row['rack'],
            'percentage' => $percentage,
            'count_percentage' => round($count_percentage * 100, 2), // Convert fraction to percentage
            'groupno' => $row['groupno']
        ];
    }
}

echo json_encode($data);
?>
