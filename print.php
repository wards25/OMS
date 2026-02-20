<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="img/oms.png">
    <title>RGC | OMS</title>

    <!-- FONT AWESOME 6-->
    <link href="fa-6/css/all.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!--<link href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" rel="stylesheet">--> 

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.13.0/mapbox-gl.css' rel='stylesheet' />  
    
    <!-- Mobile Assets -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/webcam.min.js"></script>

    <!--Announcement Modal-->
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>

    <!-- Select2 CSS --> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" integrity="sha512-kq3FES+RuuGoBW3a9R2ELYKRywUEQv0wvPTItv3DSGqjpbNtGWVdvT8qwdKkqvPzT93jp8tSF4+oN4IeTEIlQA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.css" integrity="sha512-CbQfNVBSMAYmnzP3IC+mZZmYMP2HUnVkV4+PwuhpiMUmITtSpS7Prr3fNncV1RBOnWxzz4pYQ5EAGG4ck46Oig==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Select2.js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Geolocation -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    
</head>

<?php
session_start();
include_once("dbconnect.php");

$tag = $_GET['tag'];
?>

<style media="print">
    /* Ensure the grid layout applies in print */
    .row {
        display: flex !important;
        flex-wrap: wrap !important;
    }

    [class*="col-"] {
        flex: 0 0 auto;
        width: 100% !important; /* You can customize width if needed */
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact !important; /* For background colors */
        }

        /* Make backgrounds visible in print */
        .bg-light { background-color: #f8f9fa !important; }
        .text-white { color: #fff !important; }
        .bg-dark { background-color: #343a40 !important; }
        
        /* Optional: hide buttons, navs etc. */
        .no-print, button, .btn {
            display: none !important;
        }
    }
</style>

    <!-- Begin Page Content -->
    <div class="top">
      <div class="container-fluid" style="color:#000000;">
          <!-- Header with logos -->
          <div class="d-flex justify-content-center align-items-center mb-2">
              <img class="img-fluid me-2" alt="OMS Logo" src="img/oms.png" width="48">
              <img class="img-fluid" alt="RGC Logo" src="img/carbon.png" width="170">
          </div>

          <!-- Customer Information Form -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #157aae;">
                          <h5 class="text-center m-2 text-white">CUSTOMER INFORMATION FORM - <?php echo $tag; ?></h5>
                      </div>
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">TO BE FILLED-UP BY CUSTOMER</h6>
                      </div>
                      <div class="col-12 bg-light">
                          <small class="m-1 text-dark"><center><b>TYPE OF APPLICATION</b></center></small>
                      </div>
                  </div>
                  <div class="row mt-1 align-items-center">
                      <div class="col-12 col-md-2">
                          <label class="form-label mb-0"><small class="text-danger">*</small> Type of Application:</label>
                      </div>
                      <div class="col-12 col-md-10">
                          <div class="col-12 d-flex flex-column flex-md-row gap-3">
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="type" value="N" id="newChannel" required>
                                  <label class="form-check-label" for="newChannel">New Channel</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="type" value="UR" id="updateRenewal" required disabled>
                                  <label class="form-check-label" for="updateRenewal">Update (U) /Renewal (R)</label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- General Information -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">GENERAL INFORMATION</h6>
                      </div>
                  </div>
                  <!-- Business Name -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> Business Name:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" id="business_name" name="business_name" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- Address -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> Business Address:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" name="address" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- TIN and Contact No -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> TIN No:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="tin_no" autocomplete="off" required>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Contact No:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="contact_no" autocomplete="off" required>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php
                  if($tag == 'HCP INDIVIDUAL'){
                  ?>
                  <!-- PRC License No and Validity Date -->
                  <div class="row mt-1 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> PRC License No:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="license_no" autocomplete="off" required style="text-transform: uppercase;">
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Validity Date:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="validity_date" required>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- PRC License Photo -->
                  <div class="row mt-0 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> PRC License Photo:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="prc_license" required/> -->
                          <small><i>*attach photo to this form*</i></small>
                      </div>
                  </div>
                  <?php
                  }else if($tag == 'INSTITUTION'){
                  ?>
                  <!-- BIR and SEC -->
                  <div class="row mt-1 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> BIR (2303):</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="bir" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Articles of Incorporation Registered with the SEC:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="sec" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- By Laws and GIS -->
                  <div class="row mt-0 align-items-center">
                      <div class="col-12 col-md-6 mb-1">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> By Laws:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="by_laws" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-1">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> GIS:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="gis" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- Mayors Permit and ID Speciment -->
                  <div class="row mt-0 align-items-center">
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Mayors Permit:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="mayors_permit" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> ID with Speciment Signature of Signatory:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <!-- <input type="file" accept="image/*" capture="capture" class="form-control form-control-sm" name="id_speciment" required/> -->
                                  <small><i>*attach photo to this form*</i></small>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php
                  }else{

                  }
                  ?>
              </div>
          </div>

          <!-- Delivery Address -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">DELIVERY ADDRESS</h6>
                      </div>
                      <div class="col-12 bg-light text-dark text-center py-1">
                          <small><b>DEFAULT DELIVERY / BUSINESS ADDRESS</b></small>
                      </div>
                  </div>
                  <!-- Building Name & Room No -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> Building Name & Room No:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" name="bldg_name" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- Street -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> Number & Street:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" name="street" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- Province and Town/City -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Province:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input type="text" class="form-control form-control-sm" required>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Town / City:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input type="text" class="form-control form-control-sm" required>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- Barangay / District -->
                  <div class="row mt-0 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-0"><small class="text-danger">*</small> Barangay / District:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <input type="text" class="form-control form-control-sm" required>
                      </div>
                  </div>
                  <!-- Zip and Area Code -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Zip Code:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="number" name="zip_code" required>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Area Code:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="area_code" value="+63" readonly>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- Email and Telephone -->
                  <div class="row mt-0 align-items-center">
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Email Address:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="email" name="email" autocomplete="off" required style="text-transform: lowercase;">
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-2">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Telephone No:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="number" name="telephone" autocomplete="off" required>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- Delivery Schedule -->
                  <div class="row mt-1 align-items-center">
                      <div class="col-12 col-md-2">
                          <h6 class="mb-2"><small class="text-danger">*</small> Clinic Schedule:</h6>
                      </div>
                      <div class="col-12 col-md-10">
                          <div class="d-flex flex-wrap gap-2">
                          <!-- Repeat for each day -->
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="M" id="Monday">
                                  <label class="form-check-label" for="Monday">Monday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="T" id="Tuesday">
                                  <label class="form-check-label" for="Tuesday">Tuesday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="W" id="Wednesday">
                                  <label class="form-check-label" for="Wednesday">Wednesday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="TH" id="Thursday">
                                  <label class="form-check-label" for="Thursday">Thursday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="F" id="Friday">
                                  <label class="form-check-label" for="Friday">Friday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="SA" id="Saturday">
                                  <label class="form-check-label" for="Saturday">Saturday</label>
                              </div>
                              <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="checkbox" name="delivery_sched[]" value="SU" id="Sunday">
                                  <label class="form-check-label" for="Sunday">Sunday</label>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- Clinic Hours -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Clinic Hours From:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input type="text" class="form-control form-control-sm" required>
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <h6 class="mb-0"><small class="text-danger">*</small> Clinic Hours To:</h6>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input type="text" class="form-control form-control-sm" required>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Authorized Person / Representative -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">AUTHORIZED PERSON / REPRESENTATIVE</h6>
                      </div>
                  </div>
                  <!-- Name -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <label class="form-label mb-0"><small class="text-danger">*</small> Name of Authorized Rep:</label>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" id="authorized_name" name="authorized_name" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- Designation and Contact -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0"><small class="text-danger">*</small> Designation / Position:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" id="authorized_pos" name="authorized_pos" autocomplete="off" required style="text-transform: uppercase;">
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0"><small class="text-danger">*</small> Contact No:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="authorized_contact" autocomplete="off" required>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Authorized Signatories -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">ADDITIONAL AUTHORIZED SIGNATORY</h6>
                      </div>
                  </div>
                  <!-- Signatory -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-2">
                          <label class="form-label mb-0"><small class="text-danger">*</small> Auth Signatory Name:</label>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" name="signatory1_name" autocomplete="off" required style="text-transform: uppercase;">
                      </div>
                  </div>
                  <!-- Signatory Details -->
                  <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0"><small class="text-danger">*</small> Signatory Designation:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="signatory1_pos" autocomplete="off" required style="text-transform: uppercase;">
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0"><small class="text-danger">*</small> Signatory Contact No:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" name="signatory1_contact" autocomplete="off" required>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Waiver -->
          <div class="mb-2">
              <div class="border p-2" style="border: 1px solid #ced4da;">
                  <div class="row">
                      <div class="col-12" style="background-color: #0c4562;">
                          <h6 class="text-center m-1 text-white">WAIVER</h6>
                      </div>
                  </div>
                  <div class="row mt-3">
                      <div class="col-12 text-dark text-center">
                          <i>
                              I / We wish to apply as customer of <b><u>Carbon Distribution Company Inc.</u></b>. For this purpose/ I / We hereby certify that all of the above information are true and correct.
                              <br>
                              I / We understood that all our purchases from <b><u>Carbon Distribution Company Inc.</u></b> shall be on online banking.
                              <br>
                              By signing this application form, I / We accept the terms and conditions as stated. In addition, I / We authorized representative(s) to make any inquiries necessary to process this application.
                          </i>
                      </div>
                  </div>
                  <hr>
                  <!-- Signature -->
                  <!-- <div class="row mt-1 align-items-center">
                      <div class="col-12 col-md-2">
                          <label class="form-label mb-0">Business Name:</label>
                      </div>
                      <div class="col-12 col-md-10">
                          <input class="form-control form-control-sm" type="text" id="readonly_business_name" readonly style="text-transform: uppercase;">
                      </div>
                  </div> -->
                  <!-- <div class="row mt-2 align-items-center">
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0">Authorized Representative:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" id="readonly_authorized_name" readonly style="text-transform: uppercase;">
                              </div>
                          </div>
                      </div>
                      <div class="col-12 col-md-6 mb-0">
                          <div class="row align-items-center">
                              <div class="col-12 col-sm-4 col-md-4">
                                  <label class="form-label mb-0">Designation / Position:</label>
                              </div>
                              <div class="col-12 col-sm-8 col-md-8">
                                  <input class="form-control form-control-sm" type="text" id="readonly_authorized_pos" readonly style="text-transform: uppercase;">
                              </div>
                          </div>
                      </div>
                  </div> -->
                  <div class="row mt-2">
                      <div class="col-12 mb-2">
                          <label class="form-label mb-0"><small class="text-danger">*</small> Signature Over Printed Name:</label>
                      </div>
                      <div class="col-12">
                          <div class="border rounded bg-white" style="width:100%; height:150px; position:relative;">
                              <canvas id="signatureCanvas" style="width:100%; height:100%;"></canvas>
                              <input type="hidden" name="signature_data" id="signature_data">
                          </div>
                          <!-- <div class="text-center mt-2 no-print">
                              <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">Clear Signature</button>
                              <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#import">
                                  <i class="fa fa-check"></i> Submit
                              </span>
                          </div> -->
                      </div>
                  </div>
              </div>
          </div>
      </div>
      </div>
      <!-- /.container-fluid -->
    </div>


</body>

</html>