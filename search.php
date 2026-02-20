<?php
session_start();
include_once("dbconnect.php");

if (isset($_POST['query'])) {
    $search = "%{$_POST['query']}%";
    $stmt = $conn->prepare("SELECT DISTINCT module, link FROM tbl_search 
                            WHERE module LIKE ? OR sub1 LIKE ? OR sub2 LIKE ? 
                            LIMIT 6");
    $stmt->bind_param("sss", $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="' . htmlspecialchars($row['link']) . '" class="list-group-item list-group-item-action">' . ucwords(htmlspecialchars($row['module'])) . '</a>';
        }
    } else {
        echo '<p class="list-group-item">No results found</p>';
    }
    $stmt->close();
    $conn->close();
}
?>
