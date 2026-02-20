<?php
session_start();
include_once("dbconnect.php");
$user = $_SESSION['name'];
$user_id = $_SESSION['id'];
$hub = $_SESSION['hub'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($user_id)) {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    } else {

        $name_query = mysqli_query($conn, "SELECT fname,lname,tag FROM tbl_users WHERE id = '$user_id'");
        $fetch_name = mysqli_fetch_assoc($name_query);
        $name = $fetch_name['fname'] . ' ' . $fetch_name['lname'];
        $tag = $fetch_name['tag'];
        $groupno = $_POST['groupno'];
        $rack = $_POST['rack'];
        $column = $_POST['column'];
        $level = $_POST['level'];
        $pos = $_POST['pos'];
        $racklocation = $_POST['racklocation'];
        $location = $_POST['location'];
        $status = $_POST['status'] ?? 'ACTIVE';
        $action = $_POST['action'] ?? 'ADD';

        if ($status == 'EMPTY') {
            $sku = 'EMPTY';
            $check_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_count WHERE racklocation = '$racklocation' AND user_id = '$user_id'");
            $count_check = mysqli_num_rows($check_query);

            if ($count_check > 0) {

                $validation_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_count WHERE sku='$sku' AND racklocation='$racklocation' AND qty='0' AND bbd='EMPTY' AND status='$status' AND department!='$tag'");
                $count_validation = mysqli_num_rows($validation_query);

                if ($count_validation > 0) {

                    $update_query = "UPDATE tbl_inventory_count 
                             SET name='$name', department='$tag', groupno='$groupno', sku='$sku', rack='$rack', col='$column', 
                                 level='$level', pos='$pos', racklocation='$racklocation', qty='0', uom='EMPTY', 
                                 mt='EMPTY', dt='EMPTY', yr='EMPTY', bbd='EMPTY', cases='0', pieces='0', status='$status', 
                                 submit_date=NOW(), location='$hub', count_status = 'MATCH'
                             WHERE user_id = '$user_id' AND racklocation = '$racklocation'";

                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET status = 'MATCH' WHERE racklocation='$racklocation'");
                    mysqli_query($conn, "UPDATE tbl_inventory_count SET count_status = 'MATCH' WHERE racklocation='$racklocation'");

                } else {

                    $update_query = "UPDATE tbl_inventory_count 
                             SET name='$name', department='$tag', groupno='$groupno', sku='$sku', rack='$rack', col='$column', 
                                 level='$level', pos='$pos', racklocation='$racklocation', qty='0', uom='EMPTY', 
                                 mt='EMPTY', dt='EMPTY', yr='EMPTY', bbd='EMPTY', cases='0', pieces='0', status='$status', 
                                 submit_date=NOW(), location='$hub', count_status = 'NOT MATCH'
                             WHERE user_id = '$user_id' AND racklocation = '$racklocation'";

                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET status = 'NOT ENCODED' WHERE racklocation='$racklocation'");
                    mysqli_query($conn, "UPDATE tbl_inventory_count SET count_status = 'NOT MATCH' WHERE racklocation='$racklocation'");
                }

                if ($tag == 'FINANCE') {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET fin_count = '1' WHERE racklocation = '$racklocation'");
                } else {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET log_count = '1' WHERE racklocation = '$racklocation'");
                }

                if (mysqli_query($conn, $update_query)) {
                    echo json_encode(["status" => "update"]);
                } else {
                    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
                }
            } else {

                $validation_query = mysqli_query($conn, "SELECT * FROM tbl_inventory_count WHERE sku='$sku' AND racklocation='$racklocation' AND qty='0' AND bbd='EMPTY' AND status='$status' AND department!='$tag'");
                $count_validation = mysqli_num_rows($validation_query);

                if ($count_validation > 0) {

                    $insert_query = "INSERT INTO tbl_inventory_count(id, user_id, name, department, groupno, sku, rack, col, level, pos, 
                                racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, submit_date,location,count_status,qty2,qty3,qty4,qty5) 
                                VALUES(NULL, '$user_id', '$name', '$tag', '$groupno', '$sku', '$rack', '$column', '$level', 
                                '$pos', '$racklocation', '0', 'EMPTY', 'EMPTY', 'EMPTY', 'EMPTY', 'EMPTY', '0', 
                                '0', '$status', NOW(),'$hub','MATCH','0','0','0','0')";

                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET status = 'MATCH' WHERE racklocation='$racklocation'");
                    mysqli_query($conn, "UPDATE tbl_inventory_count SET count_status = 'MATCH' WHERE racklocation='$racklocation'");

                } else {

                    $insert_query = "INSERT INTO tbl_inventory_count(id, user_id, name, department, groupno, sku, rack, col, level, pos, 
                                racklocation, qty, uom, mt, dt, yr, bbd, cases, pieces, status, submit_date,location,count_status,qty2,qty3,qty4,qty5) 
                                VALUES(NULL, '$user_id', '$name', '$tag', '$groupno', '$sku', '$rack', '$column', '$level', 
                                '$pos', '$racklocation', '0', 'EMPTY', 'EMPTY', 'EMPTY', 'EMPTY', 'EMPTY', '0', 
                                '0', '$status', NOW(),'$hub','NOT MATCH','0','0','0','0')";

                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET status = 'NOT ENCODED' WHERE racklocation='$racklocation'");
                    mysqli_query($conn, "UPDATE tbl_inventory_count SET count_status = 'NOT MATCH' WHERE racklocation='$racklocation'");
                }


                if ($tag == 'FINANCE') {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET fin_count = '1' WHERE racklocation = '$racklocation'");
                } else {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET log_count = '1' WHERE racklocation = '$racklocation'");
                }

                if (mysqli_query($conn, $insert_query)) {
                    echo json_encode(["status" => "insert"]);
                } else {
                    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
                }
            }

        } else {

            $sku = $_POST['sku'];
            $month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
            $day = str_pad($_POST['day'], 2, '0', STR_PAD_LEFT);
            $year = $_POST['year'];
            $bbd = $year . '-' . $month . '-' . $day;

            $product_query = mysqli_query($conn, "SELECT * FROM tbl_product WHERE itemcode = '$sku'");

            if (mysqli_num_rows($product_query) > 0) {
                $fetch_product = mysqli_fetch_assoc($product_query);
                $description = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['description'])));
                $uom = mysqli_real_escape_string($conn, utf8_encode($fetch_product['uom']));
                $principal = mysqli_real_escape_string($conn, utf8_encode(str_replace(",", '', $fetch_product['principal'])));
                $company = mysqli_real_escape_string($conn, utf8_encode($fetch_product['vendorcode']));
                $percase = $fetch_product['percase'];
            } else {
                echo json_encode(["status" => "error", "message" => "Product not found"]);
                exit;
            }

            // Compute quantities
            $cases = !empty($_POST['cases']) ? $_POST['cases'] : 0;
            $pieces = !empty($_POST['pieces']) ? $_POST['pieces'] : 0;

            $totalqtycases = $cases * $percase;
            $totalqtypieces = $pieces;
            $totalqty = $totalqtycases + $totalqtypieces;

            if ($action == 'ADD') {

                // Check if same SKU exists for this rack + user
                $existing_query = mysqli_query($conn, "
            SELECT * FROM tbl_inventory_count
            WHERE racklocation='$racklocation'
            AND user_id='$user_id'
            AND sku='$sku'
        ");

                if (mysqli_num_rows($existing_query) > 0) {

                    // Check same BBD
                    $existing_bbd_query = mysqli_query($conn, "
                SELECT * FROM tbl_inventory_count
                WHERE racklocation='$racklocation'
                AND user_id='$user_id'
                AND sku='$sku'
                AND bbd='$bbd'
            ");

                    if (mysqli_num_rows($existing_bbd_query) > 0) {

                        // 🔥 SAME SKU + SAME BBD → SUM
                        $row = mysqli_fetch_assoc($existing_bbd_query);

                        $new_qty = $row['qty'] + $totalqty;
                        $new_cases = $row['cases'] + $cases;
                        $new_pieces = $row['pieces'] + $pieces;

                        mysqli_query($conn, "
                    UPDATE tbl_inventory_count SET
                        qty='$new_qty',
                        cases='$new_cases',
                        pieces='$new_pieces',
                        submit_date=NOW()
                    WHERE id='{$row['id']}'
                ");

                        $response = "summed";

                    } else {

                        mysqli_query($conn, "
                    INSERT INTO tbl_inventory_count
                    (id,user_id,name,department,groupno,sku,rack,col,level,pos,
                     racklocation,qty,uom,mt,dt,yr,bbd,cases,pieces,
                     status,submit_date,location,count_status,
                     qty2,qty3,qty4,qty5)
                    VALUES
                    (NULL,'$user_id','$name','$tag','$groupno','$sku',
                     '$rack','$column','$level','$pos',
                     '$racklocation','$totalqty','$uom',
                     '$month','$day','$year','$bbd',
                     '$cases','$pieces',
                     '$status',NOW(),'$hub','NOT MATCH',
                     '0','0','0','0')
                ");

                        $response = "insert";
                    }

                } else {
                    mysqli_query($conn, "
                INSERT INTO tbl_inventory_count
                (id,user_id,name,department,groupno,sku,rack,col,level,pos,
                 racklocation,qty,uom,mt,dt,yr,bbd,cases,pieces,
                 status,submit_date,location,count_status,
                 qty2,qty3,qty4,qty5)
                VALUES
                (NULL,'$user_id','$name','$tag','$groupno','$sku',
                 '$rack','$column','$level','$pos',
                 '$racklocation','$totalqty','$uom',
                 '$month','$day','$year','$bbd',
                 '$cases','$pieces',
                 '$status',NOW(),'$hub','NOT MATCH',
                 '0','0','0','0')
            ");

                    $response = "insert";
                }

                // Insert into ending table if not exists
                $ending_query = mysqli_query($conn, "SELECT sku FROM tbl_inventory_ending WHERE sku='$sku'");
                if (mysqli_num_rows($ending_query) == 0) {
                    mysqli_query($conn, "
                INSERT INTO tbl_inventory_ending
                (id,sku,description,principal,company,active,hold,uom,location,status,dtr)
                VALUES
                (NULL,'$sku','$description','$principal','$company',
                 '0','0','$uom','$hub','MANUAL',NOW())
            ");
                }

                // Update rack flags
                if ($tag == 'FINANCE') {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET fin_count='1' WHERE racklocation='$racklocation'");
                } else {
                    mysqli_query($conn, "UPDATE tbl_inventory_rack SET log_count='1' WHERE racklocation='$racklocation'");
                }

                echo json_encode(["status" => $response]);
                exit;
            }
        }
    }
}
