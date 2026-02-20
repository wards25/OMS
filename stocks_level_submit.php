<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($user) || empty($_POST['sku'])) {
        echo json_encode(["status" => "error", "message" => "User or SKU not set"]);
        exit;
    }

    // COMMON VARIABLES
    $rack = $_POST['rack'] ?? '';
    $column = $_POST['column'] ?? '';
    $level = $_POST['level'] ?? '';
    $pos = $_POST['pos'] ?? '';
    $racklocation = $_POST['racklocation'] ?? '';
    $location = $_POST['location'] ?? '';
    $sku = $_POST['sku'] ?? '';
    $action = $_POST['action'] ?? 'ADD'; // ADD, UPDATE, MOVE, EMPTY
    $status = $_POST['status'] ?? 'ACTIVE'; // Only saved in DB

    $month = str_pad($_POST['month'] ?? date('m'), 2, '0', STR_PAD_LEFT);
    $day = str_pad($_POST['day'] ?? date('d'), 2, '0', STR_PAD_LEFT);
    $year = $_POST['year'] ?? date('Y');
    $bbd = "$year-$month-$day";

    $received_month = str_pad($_POST['received_month'] ?? date('m'), 2, '0', STR_PAD_LEFT);
    $received_day = str_pad($_POST['received_day'] ?? date('d'), 2, '0', STR_PAD_LEFT);
    $received_year = $_POST['received_year'] ?? date('Y');
    $received_by = $_POST['received_by'] ?? '';
    $received_date = "$received_year-$received_month-$received_day";

    // Fetch product info
    $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode='$sku'");
    if (mysqli_num_rows($product_query) > 0) {
        $fetch_product = mysqli_fetch_assoc($product_query);
        $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
        $uom = mysqli_real_escape_string($conn, utf8_encode($fetch_product['uom']));
        $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal'])));
        $company = mysqli_real_escape_string($conn, utf8_encode($fetch_product['vendorcode']));
        $percase = $fetch_product['percase'];
    } else {
        $description = 'NULL';
        $principal = 'NULL';
        $company = 'NULL';
        $percase = 1;
    }

    // Calculate quantities
    $cases = empty($_POST['cases']) ? 0 : $_POST['cases'];
    $pieces = empty($_POST['pieces']) ? 0 : $_POST['pieces'];
    $totalqty = ($cases * $percase) + $pieces;

    // -------------------- HANDLE EMPTY --------------------
    if ($action == 'EMPTY') {
        $sku = $_POST['sku'] ?? '';
        $bbd = $_POST['bbd'] ?? '';

        $check_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location' AND sku='$sku' AND bbd='$bbd'");
        if (mysqli_num_rows($check_query) > 0) {
            mysqli_query($conn, "DELETE FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location' AND sku='$sku' AND bbd='$bbd'");
            
            // Only clear rack if no more SKUs left
            $remaining_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location'");
            if (mysqli_num_rows($remaining_query) == 0) {
                mysqli_query($conn, "UPDATE tbl_inventory_rack SET sku='NO SKU', principal='NULL', company='NULL', inv_status='0' WHERE racklocation='$racklocation' AND location='$location'");
            }

            echo json_encode(["status" => "empty_success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "No matching SKU/BBD to empty"]);
        }
        exit;
    }

    // -------------------- HANDLE ADD --------------------
    if ($action == 'ADD') {
        // Check if SKU exists in this rack/location
        $existing_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location' AND sku='$sku'");
        if (mysqli_num_rows($existing_query) > 0) {
            // SKU exists
            $existing_bbd_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location' AND sku='$sku' AND bbd='$bbd'");
            if (mysqli_num_rows($existing_bbd_query) > 0) {
                // Same SKU + same BBD → sum quantities
                $row = mysqli_fetch_assoc($existing_bbd_query);
                $new_qty = $row['qty'] + $totalqty;
                $new_cases = $row['cases'] + $cases;
                $new_pieces = $row['pieces'] + $pieces;

                mysqli_query($conn, "UPDATE tbl_inventory_stocks SET qty='$new_qty', cases='$new_cases', pieces='$new_pieces', submit_by='$user', submit_date=NOW(), status='$status', received_date='$received_date', received_by='$received_by' WHERE id='{$row['id']}'");
                echo json_encode(["status" => "add_summed"]);
            } else {
                // Same SKU + different BBD → insert new line
                mysqli_query($conn, "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                    VALUES(NULL,'$sku','$description','$principal','$company','$rack','$column','$level','$pos','$racklocation','$totalqty','$uom','$month','$day','$year','$bbd','$cases','$pieces','$status','$location','$user',NOW(),'$received_date','$received_by')");
                echo json_encode(["status" => "add_new_bbd"]);
            }
        } else {
            // Rack/location empty → insert new
            mysqli_query($conn, "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                VALUES(NULL,'$sku','$description','$principal','$company','$rack','$column','$level','$pos','$racklocation','$totalqty','$uom','$month','$day','$year','$bbd','$cases','$pieces','$status','$location','$user',NOW(),'$received_date','$received_by')");
            echo json_encode(["status" => "add_success"]);
        }

        // Update rack table
        mysqli_query($conn, "UPDATE tbl_inventory_rack SET sku='$sku', principal='$principal', company='$company', inv_status='1' WHERE racklocation='$racklocation' AND location='$location'");
        exit;
    }

    // -------------------- HANDLE UPDATE --------------------
    if ($action == 'UPDATE') {
        // Must exist to update
        $existing_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location'");
        if (mysqli_num_rows($existing_query) == 0) {
            echo json_encode(["status" => "error", "message" => "No SKU in this location to update"]);
            exit;
        }

        $existing_rows = mysqli_fetch_all($existing_query, MYSQLI_ASSOC);
        $sku_conflict = false;
        foreach ($existing_rows as $row) {
            if ($row['sku'] != $sku) $sku_conflict = true;
        }

        if ($sku_conflict) {
            echo json_encode(["status" => "error", "message" => "Cannot change SKU: different SKU exists in location"]);
            exit;
        }

        // Check if SKU + BBD exists
        $bbd_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location' AND sku='$sku' AND bbd='$bbd'");
        if (mysqli_num_rows($bbd_query) > 0) {
            // SKU + BBD exists → just update the row, do NOT sum
            $row = mysqli_fetch_assoc($bbd_query);
            mysqli_query($conn, "UPDATE tbl_inventory_stocks 
                SET qty='$totalqty', cases='$cases', pieces='$pieces', description='$description', principal='$principal', company='$company', rack='$rack', col='$column', level='$level', pos='$pos', status='$status', submit_by='$user', submit_date=NOW(), received_date='$received_date', received_by='$received_by' 
                WHERE id='{$row['id']}'");
            echo json_encode(["status" => "update_success"]);
        } else {
            // Insert new line if BBD is different
            mysqli_query($conn, "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                VALUES(NULL,'$sku','$description','$principal','$company','$rack','$column','$level','$pos','$racklocation','$totalqty','$uom','$month','$day','$year','$bbd','$cases','$pieces','$status','$location','$user',NOW(),'$received_date','$received_by')");
            echo json_encode(["status" => "update_new_bbd"]);
        }

        // Update rack table
        mysqli_query($conn, "UPDATE tbl_inventory_rack SET sku='$sku', principal='$principal', company='$company', inv_status='1' WHERE racklocation='$racklocation' AND location='$location'");
        exit;
    }

    // -------------------- HANDLE MOVE --------------------
    if ($action == 'MOVE') {
        $move_location = $_POST['move_location'] ?? '';

        $source_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location'");
        if (mysqli_num_rows($source_query) == 0) {
            echo json_encode(["status" => "error", "message" => "No source data to move"]);
            exit;
        }
        $source = mysqli_fetch_assoc($source_query);
        $source_status = $source['status'];

        $target_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_stocks WHERE racklocation='$move_location' AND location='$location'");
        if (mysqli_num_rows($target_query) > 0) {
            $target = mysqli_fetch_assoc($target_query);

            if ($target['sku'] != $sku) {
                echo json_encode(["status" => "error", "message" => "Cannot move: different SKU exists in target location"]);
                exit;
            }

            if ($target['bbd'] == $bbd) {
                // Sum quantities
                $new_qty = $target['qty'] + $totalqty;
                $new_cases = $target['cases'] + $cases;
                $new_pieces = $target['pieces'] + $pieces;

                mysqli_query($conn, "UPDATE tbl_inventory_stocks SET qty='$new_qty', cases='$new_cases', pieces='$new_pieces', submit_by='$user', submit_date=NOW(), received_date='$received_date', received_by='$received_by' WHERE id='{$target['id']}'");
            } else {
                // Insert new line for different BBD
                mysqli_query($conn, "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                    VALUES(NULL,'$sku','$description','$principal','$company','$rack','$column','$level','$pos','$move_location','$totalqty','$uom','$month','$day','$year','$bbd','$cases','$pieces','$status','$location','$user',NOW(),'$received_date','$received_by')");
            }
        } else {
            // Empty target → insert
            mysqli_query($conn, "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                VALUES(NULL,'$sku','$description','$principal','$company','$rack','$column','$level','$pos','$move_location','$totalqty','$uom','$month','$day','$year','$bbd','$cases','$pieces','$status','$location','$user',NOW(),'$received_date','$received_by')");
        }

        // Subtract from source
        $remaining_qty = $source['qty'] - $totalqty;
        $remaining_cases = $source['cases'] - $cases;
        $remaining_pieces = $source['pieces'] - $pieces;

        if ($remaining_qty <= 0) {
            mysqli_query($conn, "DELETE FROM tbl_inventory_stocks WHERE racklocation='$racklocation' AND location='$location'");
            mysqli_query($conn, "UPDATE tbl_inventory_rack SET sku='NO SKU', principal='NULL', company='NULL', inv_status='0' WHERE racklocation='$racklocation' AND location='$location'");
        } else {
            mysqli_query($conn, "UPDATE tbl_inventory_stocks SET qty='$remaining_qty', cases='$remaining_cases', pieces='$remaining_pieces', status='$source_status', submit_date=NOW() WHERE racklocation='$racklocation' AND location='$location'");
        }

        // Update rack table for target
        mysqli_query($conn, "UPDATE tbl_inventory_rack SET sku='$sku', principal='$principal', company='$company', inv_status='1' WHERE racklocation='$move_location' AND location='$location'");

        echo json_encode(["status" => "move_success"]);
        exit;
    }
}
