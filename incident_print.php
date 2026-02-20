<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

//get id
$incident_id = $_GET['incident_id'];

$incident_query = mysqli_query($conn,"SELECT * FROM tbl_report_raw WHERE id = '$incident_id'");
$fetch_incident = mysqli_fetch_array($incident_query);

if(isset($_SESSION['id']))
{
?>

    <!-- Begin Page Content -->
    <div class="container-fluid" style="color:#000000;">
      <!-- DataTales Example -->
      <div class="row">
        <div class="col-6 d-flex justify-content-start">
          <img src="img/rgc.png" class="img-fluid" style="width:300px;"><small class="d-flex p-3"><i>HRD_RamoscoForm No. 003</i></small>
        </div>
        <div class="col-6 d-flex justify-content-end">
          <div>
            <h2 class="d-flex p-2"><b><u>INCIDENT REPORT</u></b></h2>
            <p class="text-danger">Reference No.: <b><i><?php echo $fetch_incident['ref_no'];?></i></b></p>
          </div>
        </div>
      </div>
      <br>
      <div class="table-responsive">
          <table class="table table-bordered" style="color:#000000;">
            <tbody>
              <tr>
                <td width="25%"><b>NAME (Person(s) Involved):</b></td>
                <td colspan="3"><?php echo $fetch_incident['person_involved'];?></td>
              </tr>
              <tr>
                <td width="25%"><b>POSITION(s):</b></td>
                <td><?php echo $fetch_incident['position'];?></td>
                <td width="25%"><b>LOCATION:</b></td>
                <td><?php echo $fetch_incident['location'];?></td>
              </tr>
              <tr>
                <td width="25%"><b>DEPARTMENT(s):</b></td>
                <td><?php echo $fetch_incident['department'];?></td>
                <td width="25%"><b>SUBJECT:</b></td>
                <td><?php echo $fetch_incident['subject'];?></td>
              </tr>
            </tbody>
          </table>
      </div>
      <br>
      <div class="table-responsive">
          <table class="table table-bordered" style="color:#000000;">
            <tbody>
              <tr>
                <td width="25%"><h5><b>WHEN </b></h5><b><i>(Indicate the DATE & TIME / Petsa at Oras ng pangyayari)</i></b></td>
                <td><?php echo $fetch_incident['ir_when'];?></td>
              </tr>
              <tr>
                <td width="25%"><h5><b>WHERE </b></h5><b><i>(Indicate the LOCATION / Lugar ng Pangyayari)</i></b></td>
                <td><?php echo $fetch_incident['ir_where'];?></td>
              </tr>
              <tr>
                <td width="25%"><h5><b>WHAT </b></h5><b><i>(Indicate the Incident / Ano ang naganap) Attach photo if necessary</i></b></td>
                <td><?php echo $fetch_incident['ir_what'];?></td>
              </tr>
              <tr>
                <td width="25%"><h5><b>HOW </b></h5><b><i>(Indicate the manner how it happened / Papaano naganap o nangyari ang insidente)</i></b></td>
                <td><?php echo $fetch_incident['ir_how'];?></td>
              </tr>
              <tr>
                <td width="25%"><h5><b></b></h5><b><i>Remarks</i></b></td>
                <td><?php echo $fetch_incident['remarks'];?></td>
              </tr>
            </tbody>
          </table>
      </div>
      <br>
      <div class="row">
        <div class="col-6">
            <b>Reported By: </b> <u><?php echo $fetch_incident['reported_by'];?></u>
        </div>
        <div class="col-6">
          <b>Reporting Date: </b> <?php echo date("F d, Y", strtotime($fetch_incident['report_date']));?>
        </div>
      </div>
      <br>
      <div class="table-responsive">
          <table class="table table-bordered" style="color:#000000;">
            <tbody>
              <tr>
                <td width="25%"><h5><b>RESOLUTION </b></h5><b><i>(Indicate the SOLUTION/ACTION MADE OR DONE / Ano ang ginawang solusyon sa pangyayari)</i></b></td>
                <td><?php echo $fetch_incident['resolution'];?></td>
              </tr>
            </tbody>
          </table>
      </div>
      <br>
      <div class="row">
        <div class="col-6">
            <b>Resolved By: </b> <u><?php echo $fetch_incident['resolved_by'];?></u>
        </div>
        <div class="col-6">
          <b>Resolve Date: </b> 
          <?php
          if($fetch_incident['resolve_date'] == '0000-00-00'){

          }else{
            echo date("F d, Y", strtotime($fetch_incident['resolve_date']));
          }
          ?>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="col-6">
          <div>
            <b>Noted By: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
            <p><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department Head</i></p>
          </div>
        </div>
        <div class="col-6">
          <div>
            <b>Submitted To: </b> <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
            <p><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HR Department</i></p>
          </div>
        </div>
      </div>
      <br><hr>
      <center>
        <i>Note:<b><u> Should be signed by the Dept. Head, before endorsement to HR Department.</u></b></i>
        <p><i>(Papirmahan muna sa Department Head, bago ibigay sa HR Deparment)</i></p>
        <br>
        <a class="btn btn-success btn-sm" onclick= "$(this).hide(); window.print();"><i class="fa fa-sm fa-print"></i> Print Incident Report</a>
      </center>
      <br>
      <!-- End Table -->

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