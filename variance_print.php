<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

//get id
$type = $_GET['type'];
$form_no = $_GET['form_no'];

$details_query = mysqli_query($conn,"SELECT * FROM tbl_variance_ref WHERE form_no = '$form_no'");
$fetch_details = mysqli_fetch_array($details_query);

if(isset($_SESSION['id']))
{
?>

<style>
  @media print {
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .container-fluid {
      width: 100%; /* Ensure it uses the full width */
      padding: 0;  /* Remove any padding */
    }
    img {
      max-width: 100%; /* Ensure images fit the page */
      height: auto;
    }
    .table {
      width: 100%; /* Tables should take the full width */
      border-collapse: collapse; /* Remove gaps */
    }
    th, td {
      padding: 5px; /* Add some padding */
      border: 1px solid #000; /* Ensure borders are visible */
    }
    /* Hide unnecessary elements for printing */
    .no-print {
      display: none; 
    }
    .top, .bottom {
        height: 50%;
        box-sizing: border-box;
    }
    .nowrap {
      white-space: nowrap;
    }
  }
</style>

    <!-- Begin Page Content -->
    <div class="top">
      <div class="container-fluid" style="color:#000000;">
        <!-- DataTales Example -->
        <div class="form-row">
          <div class="col-6 d-flex justify-content-start">
            <img src="img/rgc.png" class="img-fluid" style="width:230px; height:80px;"><small class="d-flex p-3"><i><?php echo date("F d, Y"); ?> --  Warehouse Copy</i></small>
          </div>
          <div class="col-6 d-flex justify-content-end">
            <div>
              <h4 class="d-flex p-2"><b><u><?php if($type == 'PVF'){ echo 'PICKING VARIANCE FORM'; }else if($type == 'RVF'){ echo 'REDEL VARIANCE FORM'; }else{ echo 'LOADING VARIANCE FORM'; } ?></u></b></h4>
              <p class="text-danger">Serial No.: <b><i><?php echo $form_no; ?></i></b></p>
            </div>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="color:#000000;">
              <tbody>
                <?php
                if($type == 'RVF'){
                ?>
                <tr>
                  <td width="15%"><b>Submit Date:</b></td>
                  <td><?php echo date("F d, Y", strtotime($fetch_details['date']));?></td>
                  <td width="15%"><b>Driver Name:</b></td>
                  <td><?php echo $fetch_details['driver_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>PO No:</b></td>
                  <td><?php echo $fetch_details['po_no'];?></td>
                  <td width="15%"><b>New Driver:</b></td>
                  <td><?php echo $fetch_details['new_driver'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Invoice No:</b></td>
                  <td><?php echo $fetch_details['invoice_no'];?></td>
                  <td width="15%"><b>Helper Name:</b></td>
                  <td><?php echo $fetch_details['helper_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Location:</b></td>
                  <td><?php echo $fetch_details['location'];?></td>
                  <td width="15%"><b>Checker Name:</b></td>
                  <td><?php echo $fetch_details['checker_name'];?></td>
                </tr>
                <?php
                }else{
                ?>
                <tr>
                  <td width="15%"><b>Submit Date:</b></td>
                  <td><?php echo date("F d, Y", strtotime($fetch_details['date']));?></td>
                  <td width="15%"><b>Picker Name:</b></td>
                  <td><?php echo $fetch_details['picker_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>PO No:</b></td>
                  <td><?php echo $fetch_details['po_no'];?></td>
                  <td width="15%"><b>Driver Name:</b></td>
                  <td><?php echo $fetch_details['driver_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Invoice No:</b></td>
                  <td><?php echo $fetch_details['invoice_no'];?></td>
                  <td width="15%"><b>Helper Name:</b></td>
                  <td><?php echo $fetch_details['helper_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Location:</b></td>
                  <td><?php echo $fetch_details['location'];?></td>
                  <td width="15%"><b>Checker Name:</b></td>
                  <td><?php echo $fetch_details['checker_name'];?></td>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
        </div>
        <div>
        <!-- <div class="table-responsive"> -->
            <table class="table table-bordered table-sm" style="color:#000000;">
              <thead class="text-center">
                <th class="align-middle">Error Type</th>
                <th class="align-middle">Invoice Sku</th>
                <th class="align-middle">Invoice Qty</th>
                <th class="align-middle">Actual Sku</th>
                <th class="align-middle">Actual Qty</th>
                <th class="align-middle">Uom</th>
                <th class="align-middle">For Picking Qty</th>
                <th class="align-middle">For Return Qty</th>
              </thead>
              <tbody>
                <?php
                $sku_query = mysqli_query($conn, "SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");
                $rows = [];

                while ($fetch_sku = mysqli_fetch_array($sku_query)) {
                    $rows[] = $fetch_sku;
                }

                // Limit the output to 10 rows, adding blank rows if necessary
                $limit = 8;
                for ($i = 0; $i < $limit; $i++) {
                    echo '<tr>';
                    if (isset($rows[$i])) {
                        echo '<td class="nowrap"><center>' . $rows[$i]['error_type'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['invoiced_sku'] . ' - ' . $rows[$i]['invoiced_desc'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['invoiced_qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['picked_sku'] . ' - ' . $rows[$i]['picked_desc'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['picked_qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['uom'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['return_qty'] . '</center></td>';
                    } else {
                        // Output blank cells if there are no more rows
                        echo '<td style="height:35px;"><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                    }
                    echo '</tr>';
                }
                ?>
              </tbody>
            </table>
        </div>
        <br>
        <div class="form-group">
          <?php
          if($type == 'PVF'){
          ?>
            <div class="form-row">
              <div class="col-4">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-4">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-4">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }else if($type == 'RVF'){
          ?>
            <div class="form-row">
              <div class="col-3">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Clearing Team</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Adjusted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }else{
          ?>
            <div class="form-row">
              <div class="col-3">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Outbound Supervisor</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Adjusted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
      </div>
      <!-- /.container-fluid -->
    </div>

    <!-- Begin Page Content -->
    <div class="bottom">
      <br>
      <div class="container-fluid" style="color:#000000;">
        <!-- DataTales Example -->
        <div class="form-row">
          <div class="col-6 d-flex justify-content-start">
            <img src="img/rgc.png" class="img-fluid" style="width:230px; height:80px;"><small class="d-flex p-3"><i><?php echo date("F d, Y"); ?> --  Warehouse Copy</i></small>
          </div>
          <div class="col-6 d-flex justify-content-end">
            <div>
              <h4 class="d-flex p-2"><b><u><?php if($type == 'PVF'){ echo 'PICKING VARIANCE FORM'; }else if($type == 'RVF'){ echo 'REDEL VARIANCE FORM'; }else{ echo 'LOADING VARIANCE FORM'; } ?></u></b></h4>
              <p class="text-danger">Serial No.: <b><i><?php echo $form_no; ?></i></b></p>
            </div>
          </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" style="color:#000000;">
              <tbody>
                <?php
                if($type == 'RVF'){
                ?>
                <tr>
                  <td width="15%"><b>Submit Date:</b></td>
                  <td><?php echo date("F d, Y", strtotime($fetch_details['date']));?></td>
                  <td width="15%"><b>Driver Name:</b></td>
                  <td><?php echo $fetch_details['driver_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>PO No:</b></td>
                  <td><?php echo $fetch_details['po_no'];?></td>
                  <td width="15%"><b>New Driver:</b></td>
                  <td><?php echo $fetch_details['new_driver'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Invoice No:</b></td>
                  <td><?php echo $fetch_details['invoice_no'];?></td>
                  <td width="15%"><b>Helper Name:</b></td>
                  <td><?php echo $fetch_details['helper_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Location:</b></td>
                  <td><?php echo $fetch_details['location'];?></td>
                  <td width="15%"><b>Checker Name:</b></td>
                  <td><?php echo $fetch_details['checker_name'];?></td>
                </tr>
                <?php
                }else{
                ?>
                <tr>
                  <td width="15%"><b>Submit Date:</b></td>
                  <td><?php echo date("F d, Y", strtotime($fetch_details['date']));?></td>
                  <td width="15%"><b>Picker Name:</b></td>
                  <td><?php echo $fetch_details['picker_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>PO No:</b></td>
                  <td><?php echo $fetch_details['po_no'];?></td>
                  <td width="15%"><b>Driver Name:</b></td>
                  <td><?php echo $fetch_details['driver_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Invoice No:</b></td>
                  <td><?php echo $fetch_details['invoice_no'];?></td>
                  <td width="15%"><b>Helper Name:</b></td>
                  <td><?php echo $fetch_details['helper_name'];?></td>
                </tr>
                <tr>
                  <td width="15%"><b>Location:</b></td>
                  <td><?php echo $fetch_details['location'];?></td>
                  <td width="15%"><b>Checker Name:</b></td>
                  <td><?php echo $fetch_details['checker_name'];?></td>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
        </div>
        <div>
        <!-- <div class="table-responsive"> -->
            <table class="table table-bordered table-sm" style="color:#000000;">
              <thead class="text-center">
                <th class="align-middle">Error Type</th>
                <th class="align-middle">Invoice Sku</th>
                <th class="align-middle">Invoice Qty</th>
                <th class="align-middle">Actual Sku</th>
                <th class="align-middle">Actual Qty</th>
                <th class="align-middle">Uom</th>
                <th class="align-middle">For Picking Qty</th>
                <th class="align-middle">For Return Qty</th>
              </thead>
              <tbody>
                <?php
                $sku_query = mysqli_query($conn, "SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");
                $rows = [];

                while ($fetch_sku = mysqli_fetch_array($sku_query)) {
                    $rows[] = $fetch_sku;
                }

                // Limit the output to 10 rows, adding blank rows if necessary
                $limit = 8;
                for ($i = 0; $i < $limit; $i++) {
                    echo '<tr>';
                    if (isset($rows[$i])) {
                        echo '<td class="nowrap"><center>' . $rows[$i]['error_type'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['invoiced_sku'] . ' - ' . $rows[$i]['invoiced_desc'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['invoiced_qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['picked_sku'] . ' - ' . $rows[$i]['picked_desc'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['picked_qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['uom'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['qty'] . '</center></td>';
                        echo '<td class="nowrap"><center>' . $rows[$i]['return_qty'] . '</center></td>';
                    } else {
                        // Output blank cells if there are no more rows
                        echo '<td style="height:35px;"><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                        echo '<td><center></center></td>';
                    }
                    echo '</tr>';
                }
                ?>
              </tbody>
            </table>
        </div>
        <br>
        <div class="form-group">
          <?php
          if($type == 'PVF'){
          ?>
            <div class="form-row">
              <div class="col-4">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-4">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-4">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }else if($type == 'RVF'){
          ?>
            <div class="form-row">
              <div class="col-3">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Clearing Team</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Adjusted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }else{
          ?>
            <div class="form-row">
              <div class="col-3">
                <div>
                  <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Outbound Supervisor</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Adjusted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Inventory System</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Manager</i></p>
                </div>
              </div>
              <div class="col-3">
                <div>
                  <b>Repicked By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                  <p><i>Warehouse Supervisor</i></p>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
      </div>
      <!-- /.container-fluid -->
    </div>


</body>

</html>

<?php
}else{
    header("Location: denied.php");
}