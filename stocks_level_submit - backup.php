<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if(!isset($user) || empty($_POST['sku'])){
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }else{

        $rack = $_POST['rack'];
        $column = $_POST['column'];
        $level = $_POST['level'];
        $pos = $_POST['pos'];
        $racklocation = $_POST['racklocation'];
        $location = $_POST['location'];
        $status = $_POST['status'];

        if($status == 'MOVE'){

            $sku = $_POST['sku'];
            $month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
            $day = str_pad($_POST['day'], 2, '0', STR_PAD_LEFT);
            $year = $_POST['year'];
            $bbd = $year.'-'.$month.'-'.$day;
            $received_month = $_POST['received_month'];
            $received_day = $_POST['received_day'];
            $received_year = $_POST['received_year'];
            $received_by = $_POST['received_by'];
            $received_date = $received_year . '-' . $received_month . '-' . $received_day;

            $product_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$sku'");

            if (mysqli_num_rows($product_query) > 0) {
                $fetch_product = mysqli_fetch_assoc($product_query);
                $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
                $uom = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['uom'])));
                $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal']))); // Ensure you're using the correct column name
                $company = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['vendorcode'])));
            } else {
                $description = 'NULL';
                $principal = 'NULL';
                $company = 'NULL';
            }

            if (empty($_POST['cases'])){
                $cases = '0';
                $totalqtycases = '0';
            }else{
                $cases = $_POST['cases'];
                $totalqtycases = $cases * $fetch_product['percase'];
            }

            if (empty($_POST['pieces'])){
                $pieces = '0';
                $totalqtypieces = '0';
            }else{
                $pieces = $_POST['pieces'];
                $totalqtypieces = $pieces;
            }

            $totalqty = $totalqtycases + $totalqtypieces;
            $move_location = $_POST['move_location'];

            $existing_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_rack WHERE racklocation = '$move_location'");
            $fetch_existing = mysqli_fetch_assoc($existing_query);
            $rack2 = $fetch_existing['rack'];
            $column2 = $fetch_existing['col'];
            $level2 = $fetch_existing['level'];
            $pos2 = $fetch_existing['pos'];

            $update_query = "UPDATE tbl_inventory_stocks SET rack='$rack2',col='$column2',level='$level2',pos='$pos2',qty='$totalqty',cases='$cases',pieces='$pieces',mt='$month',dt='$day',yr='$year',bbd='$bbd',racklocation='$move_location',submit_by='$user',submit_date=NOW(),received_date='$received_date',received_by='$received_by' WHERE racklocation = '$racklocation' AND location = '$location'";

            mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = 'NO SKU', principal = 'NULL', company = 'NULL', inv_status = '0' WHERE racklocation='$racklocation' AND location = '$location'");
            mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = '$sku', principal = '$principal', company = '$company', inv_status = '1' WHERE racklocation='$move_location' AND location = '$location'");

            if (mysqli_query($conn, $update_query)) {
                echo json_encode(["status" => "update"]);
            } else {
                echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
            }

        }else if($status == 'EMPTY'){

            $sku = 'EMPTY';
            $check_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_stocks WHERE racklocation = '$racklocation' AND location = '$location'");
            $count_check = mysqli_num_rows($check_query);

            if ($count_check > 0) {

                $update_query = "DELETE FROM tbl_inventory_stocks WHERE racklocation = '$racklocation' AND location = '$location'";

                mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = 'NO SKU', principal = 'NULL', company = 'NULL', inv_status = '0' WHERE racklocation='$racklocation' AND location = '$location'");

                if (mysqli_query($conn, $update_query)) {
                    echo json_encode(["status" => "update"]);
                } else {
                    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
                }

            } else {
                echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
            }

        }else{

            $sku = $_POST['sku'];
            $month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
            $day = str_pad($_POST['day'], 2, '0', STR_PAD_LEFT);
            $year = $_POST['year'];
            $bbd = $year.'-'.$month.'-'.$day;
            $received_month = $_POST['received_month'];
            $received_day = $_POST['received_day'];
            $received_year = $_POST['received_year'];
            $received_by = $_POST['received_by'];
            $received_date = $received_year . '-' . $received_month . '-' . $received_day;

            $product_query = mysqli_query($conn,"SELECT * FROM tbl_product WHERE itemcode = '$sku'");

            if (mysqli_num_rows($product_query) > 0) {
                $fetch_product = mysqli_fetch_assoc($product_query);
                $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
                $uom = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['uom'])));
                $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal']))); // Ensure you're using the correct column name
                $company = mysqli_real_escape_string($conn, utf8_encode(str_replace("", '', $fetch_product['vendorcode'])));
            } else {
                $description = 'NULL';
                $principal = 'NULL';
                $company = 'NULL';
            }

            if (empty($_POST['cases'])){
                $cases = '0';
                $totalqtycases = '0';
            }else{
                $cases = $_POST['cases'];
                $totalqtycases = $cases * $fetch_product['percase'];
            }

            if (empty($_POST['pieces'])){
                $pieces = '0';
                $totalqtypieces = '0';
            }else{
                $pieces = $_POST['pieces'];
                $totalqtypieces = $pieces;
            }

            $totalqty = $totalqtycases + $totalqtypieces;

            $check_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_stocks WHERE racklocation = '$racklocation' AND location = '$location'");
            $count_check = mysqli_num_rows($check_query);

            if ($count_check > 0) {

                $update_query = "UPDATE tbl_inventory_stocks 
                         SET submit_by='$user', sku='$sku', description='$description', principal='$principal', company='$company', rack='$rack', col='$column', 
                             level='$level', pos='$pos', racklocation='$racklocation', qty='$totalqty', uom='$uom', 
                             mt='$month', dt='$day', yr='$year', bbd='$bbd', cases='$cases', pieces='$pieces', status='$status', 
                             submit_date=NOW(), location='$location',received_date='$received_date',received_by='$received_by'
                         WHERE location = '$location' AND racklocation = '$racklocation'";

                mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = '$sku', principal = '$principal', company = '$company', inv_status = '1' WHERE racklocation='$racklocation' AND location = '$location'");

                if (mysqli_query($conn, $update_query)) {
                    echo json_encode(["status" => "update"]);
                } else {
                    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
                }

            } else {

                $insert_query = "INSERT INTO tbl_inventory_stocks(id, sku, description, principal, company, rack, col, level, pos, 
                            racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, location, submit_by, submit_date, received_date, received_by) 
                            VALUES(NULL, '$sku', '$description', '$principal', '$company', '$rack', '$column', '$level', 
                            '$pos', '$racklocation', '$totalqty', '$uom', '$month', '$day', '$year', '$bbd', '$cases', 
                            '$pieces', '$status', '$location', '$user', NOW(), '$received_date', '$received_by')";

                mysqli_query($conn,"UPDATE tbl_inventory_rack SET sku = '$sku', principal = '$principal', company = '$company', inv_status = '1' WHERE racklocation='$racklocation' AND location = '$location'");

                if (mysqli_query($conn, $insert_query)) {
                    echo json_encode(["status" => "insert"]);
                } else {
                    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
                }
            }
        }
    }
}
