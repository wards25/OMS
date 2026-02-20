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

    <!-- Begin Page Content -->
    <div class="container-fluid" style="color:#000000;">
      <!-- DataTales Example -->
      <div class="form-row">
        <div class="col-6 d-flex justify-content-start">
          <img src="img/rgc.png" class="img-fluid" style="width:230px; height:80px;"><small class="d-flex p-3"><i><?php echo date("F d, Y"); ?> --  Warehouse Copy</i></small>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <div>
            <h4 class="d-flex p-2"><b><u><?php if($type == 'PVF'){ echo 'PICKING VARIANCE FORM'; }else{ echo 'REDEL VARIANCE FORM'; } ?></u></b></h4>
            <p class="text-danger">Serial No.: <b><i><?php echo $form_no; ?></i></b></p>
          </div>
        </div>
      </div>
      <div class="table-responsive">
          <table class="table table-bordered table-sm" style="color:#000000;">
            <tbody>
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
            </tbody>
          </table>
      </div>
      <div class="table-responsive">
          <table class="table table-bordered table-sm" style="color:#000000;">
            <thead class="text-center">
              <th>Error Type</th>
              <th>Invoice Sku</th>
              <th>Invoice Qty</th>
              <th>Actual Sku</th>
              <th>Actual Qty</th>
              <th>Uom</th>
              <th>Picked Qty</th>
              <th>Return Qty</th>
            </thead>
            <tbody>
              <?php
              $sku_query = mysqli_query($conn, "SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");
              $rows = [];

              while ($fetch_sku = mysqli_fetch_array($sku_query)) {
                  $rows[] = $fetch_sku;
              }

              // Limit the output to 10 rows, adding blank rows if necessary
              $limit = 10;
              for ($i = 0; $i < $limit; $i++) {
                  echo '<tr>';
                  if (isset($rows[$i])) {
                      echo '<td><center>' . $rows[$i]['error_type'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['invoiced_sku'] . ' - ' . $rows[$i]['invoiced_desc'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['invoiced_qty'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['picked_sku'] . ' - ' . $rows[$i]['picked_desc'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['picked_qty'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['uom'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['qty'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['return_qty'] . '</center></td>';
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
      <div class="form-group">
        <div class="form-row">
          <div class="col-3">
            <div>
              <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
              <p><i>Outbound Supervisor</i></p>
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
          <div class="col-3">
            <div>
              <b>Reviewed By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
              <p><i>Inventory Control</i></p>
            </div>
          </div>
        </div>
      </div>
      <hr>

      <!-- DataTales Example -->
      <div class="form-row">
        <div class="col-6 d-flex justify-content-start">
          <img src="img/rgc.png" class="img-fluid" style="width:230px; height:80px;"><small class="d-flex p-3"><i><?php echo date("F d, Y"); ?> --  Inventory Copy</i></small>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <div>
            <h4 class="d-flex p-2"><b><u><?php if($type == 'PVF'){ echo 'PICKING VARIANCE FORM'; }else{ echo 'REDEL VARIANCE FORM'; } ?></u></b></h4>
            <p class="text-danger">Serial No.: <b><i><?php echo $form_no; ?></i></b></p>
          </div>
        </div>
      </div>
      <div class="table-responsive">
          <table class="table table-bordered table-sm" style="color:#000000;">
            <tbody>
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
            </tbody>
          </table>
      </div>
      <div class="table-responsive">
          <table class="table table-bordered table-sm" style="color:#000000;">
            <thead class="text-center">
              <th>Error Type</th>
              <th>Invoice Sku</th>
              <th>Picked Sku</th>
              <th>Qty</th>
              <th>Uom</th>
              <?php
              if($type=='RVF'){
                echo '<th>Odoo Ref</th>';
              }else{

              }
              ?>
            </thead>
            <tbody>
              <?php
              $sku_query = mysqli_query($conn, "SELECT * FROM tbl_variance_raw WHERE form_no = '$form_no'");
              $rows = [];

              while ($fetch_sku = mysqli_fetch_array($sku_query)) {
                  $rows[] = $fetch_sku;
              }

              // Limit the output to 10 rows, adding blank rows if necessary
              $limit = 10;
              for ($i = 0; $i < $limit; $i++) {
                  echo '<tr>';
                  if (isset($rows[$i])) {
                      echo '<td><center>' . $rows[$i]['error_type'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['invoiced_sku'] . ' - ' . $rows[$i]['invoiced_desc'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['picked_sku'] . ' - ' . $rows[$i]['picked_desc'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['qty'] . '</center></td>';
                      echo '<td><center>' . $rows[$i]['uom'] . '</center></td>';
                      if($type == 'RVF'){
                        echo '<td><center>' . $rows[$i]['odoo_ref'] . '</center></td>';
                      }else{

                      }
                  } else {
                      // Output blank cells if there are no more rows
                      echo '<td style="height:35px;"><center></center></td>';
                      echo '<td><center></center></td>';
                      echo '<td><center></center></td>';
                      echo '<td><center></center></td>';
                      echo '<td><center></center></td>';
                      if($type == 'RVF'){
                        echo '<td><center></center></td>';
                      }else{

                      }
                  }
                  echo '</tr>';
              }
              ?>
            </tbody>
          </table>
      </div>
      <div class="form-group">
        <div class="form-row">
          <div class="col-3">
            <div>
              <b>Prepared By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
              <p><i>Outbound Supervisor</i></p>
            </div>
          </div>
          <div class="col-3">
            <div>
              <b>Reviewed By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
              <p><i>Inventory Team</i></p>
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
      </div>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>

<?php
}else{
    header("Location: denied.php");
}