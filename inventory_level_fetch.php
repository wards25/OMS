<?php
session_start();
include_once("dbconnect.php");

$groupno = $_GET['groupno'];
$rack = $_GET['rack'];
$column = $_GET['column'];

// SQL query to calculate match percentage per rack
$query = "SELECT *
          FROM tbl_inventory_rack 
          WHERE rack = '$rack'
          AND col = '$column'
          AND groupno = '$groupno'
          AND location = '$hub'";

$result = mysqli_query($conn, $query);

// Loop through each rack result and display in table
while ($row = mysqli_fetch_assoc($result)) {
    $level = $row['level'];
    $racklocation = $row['racklocation'];

    // Check if this racklocation exists in tbl_inventory_count
    $checking_query = mysqli_query($conn, "SELECT racklocation FROM tbl_inventory_count WHERE racklocation = '$racklocation'");
    $count_checking = mysqli_num_rows($checking_query);
    $fetch_checking = mysqli_fetch_assoc($checking_query);

    // Determine the class based on the count_checking result
    if ($count_checking == 1) {
        $row_class = 'table-warning'; // Only one match
    } elseif ($count_checking == 0) {
        $row_class = 'table-danger';  // No match
    } else {
        $row_class = 'table-success'; // Data matches
    }

    echo '<tr class="' . $row_class . '">';
    echo '<td>Level ' . $level;
    
    // Check if pos is empty or not
    if (empty($row['pos'])) {
        echo '</td>';
    } else {
        echo '-' . $row['pos'] . '</td>';
    }

    // $scan_query = mysqli_query($conn, "SELECT racklocation FROM tbl_inventory_count WHERE racklocation = '$racklocation' AND user_id=".$_SESSION['id']);
    // $count_scan = mysqli_num_rows($scan_query);
    // if($count_scan > 0){
    //     echo '<td><span class="badge badge-success d-inline">ENCODED</span></td>';
    // }else{
    //     echo '<td><span class="badge badge-danger d-inline">NOT ENCODED</span></td>';
    // }

    echo '<td><a href="#" class="btn btn-sm btn-info scan-btn" data-toggle="modal" data-target="#scanModal'.$row['racklocation'].'"><i class="fa-solid fa-barcode"></i></a></td>';
    echo '<td><a href="#" class="btn btn-sm btn-warning text-dark" data-toggle="modal" data-target="#viewModal'.$row['racklocation'].'"><i class="fa-solid fa-info"></i></a></td>';
    echo '</tr>';
?>  
    <!-- View Modal-->
    <div class="modal fade" id="viewModal<?php echo $row['racklocation'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<?php echo $row['racklocation'];?></h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $racklocation = $row['racklocation'];
                    $existing_query = mysqli_query($conn,"SELECT * FROM tbl_inventory_count WHERE racklocation = '$racklocation' AND user_id=".$_SESSION['id']);
                    $fetch_existing = mysqli_fetch_assoc($existing_query);

                    $sku = $fetch_existing['sku'];
                    $product_query = mysqli_query($conn,"SELECT itemcode,description FROM tbl_product WHERE itemcode = '$sku'");
                    $fetch_product = mysqli_fetch_assoc($product_query);
                    ?>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label class="mr-2">SKU:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_product['itemcode'].' - '.$fetch_product['description']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center table-warning">
                        <h6 class="text-dark"><b>Expiration Date</b></h6>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="month" class="mr-2">BBD:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_existing['bbd']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center table-warning">
                        <h6 class="text-dark"><b>Quantity</b></h6>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="month" class="mr-2">CS:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_existing['cases']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_existing['pieces']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label class="mr-2">Status:</label>
                                <input type="text" class="form-control form-control-sm" value="<?php echo $fetch_existing['status']; ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scan Modal-->
    <div class="modal fade" id="scanModal<?php echo $row['racklocation'];?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-barcode"></i> &nbsp;<?php echo $row['racklocation'];?></h6>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><small>×</small></span>
                    </button>
                </div>
                <div class="alert-container"></div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-6">
                                <input type="text" class="form-control form-control-sm barcode-scan" placeholder="Tap here to scan..." autofocus>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-sm btn-danger btn-block clear-btn">Clear</button>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-sm btn-primary btn-block">Manual</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <form class="inventoryForm">
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label class="mr-2">SKU:</label>
                                <input type="text" class="form-control form-control-sm sku-id" readonly>
                                <input type="text" class="form-control form-control-sm sku-itemcode" name="sku" hidden required>
                                <input type="text" class="form-control form-control-sm sku-uom" name="uom" hidden>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center table-warning">
                        <h6 class="text-dark"><b>Expiration Date</b></h6>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-6 d-flex align-items-center">
                                <label for="month" class="mr-2">Month:</label>
                                <select class="form-control form-control-sm month" name="month" required>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-6 d-flex align-items-center">
                                <label for="day" class="mr-2">Day:</label>
                                <select class="form-control form-control-sm day" name="day" required>
                                    <!-- Days will be dynamically added here -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="year" class="mr-2">Year:</label>
                                <select class="form-control form-control-sm year" name="year" required>
                                    <!-- Years will be dynamically added here -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center table-warning">
                        <h6 class="text-dark"><b>Quantity</b></h6>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="month" class="mr-2">CS:</label>
                                <input type="number" class="form-control form-control-sm" name="cases" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label for="day" class="mr-2">IB/PCK/PCS:</label>
                                <input type="number" class="form-control form-control-sm" name="pieces" value="0">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-12 d-flex align-items-center">
                                <label class="mr-2">Status:</label>
                                <select class="form-control form-control-sm" name="status" required>
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="HOLD">HOLD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group text-center">
                        <input type="text" name="groupno" value="<?php echo $row['groupno'];?>" hidden>
                        <input type="text" name="rack" value="<?php echo $row['rack'];?>" hidden>
                        <input type="text" name="column" value="<?php echo $row['col'];?>" hidden>
                        <input type="text" name="level" value="<?php echo $row['level'];?>" hidden>
                        <input type="text" name="pos" value="<?php echo $row['pos'];?>" hidden>
                        <input type="text" name="racklocation" value="<?php echo $row['racklocation'];?>" hidden>
                        <input type="text" name="location" value="<?php echo $row['location'];?>" hidden>
                        <button class="btn btn-sm btn-success" name="submit"><i class="fa-solid fa-check"></i> Submit/Update</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>
</tbody>