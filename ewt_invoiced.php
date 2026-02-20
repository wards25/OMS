<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if (isset($_POST['bulk_invoiced']) && !empty($_POST['selected_ids'])) {
    // Escape & quote each serial_no (since it's like 'EWT-0001')
    $ids = array_map(function($id) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $id) . "'";
    }, $_POST['selected_ids']);

    $ids_list = implode(",", $ids);

    $updateQuery = "UPDATE tbl_ewt_raw SET status = 2 WHERE serial_no IN ($ids_list)";
    mysqli_query($conn, $updateQuery);

    $values = [];
    foreach ($_POST['selected_ids'] as $serial_no) {
        $serial_no = mysqli_real_escape_string($conn, $serial_no);
        $action = "INVOICED: EWT " . $serial_no;
        $module = "FORMS";
        $values[] = "(NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())";
    }

    if (!empty($values)) {
        $insertQuery = "INSERT INTO tbl_history VALUES " . implode(",", $values);
        mysqli_query($conn, $insertQuery);
    }

    $qstring = '?status=invoiced';
} else {
    $qstring = '?status=err';
}

header("Location: ewt_invoice.php".$qstring);
exit();
?>
