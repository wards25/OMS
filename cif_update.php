<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(167, $permission))
{
include_once("nav_forms.php");

$tag = $_GET['tag'];
$serial_no = $_GET['ref_no'];
$link = $_GET['link'];

$update_query = mysqli_query($conn,"SELECT * FROM tbl_customer WHERE serial_no = '$serial_no'");
$fetch_update = mysqli_fetch_assoc($update_query);
?>

    <!-- Begin Page Content -->
    <div id="content-wrapper" class="d-flex flex-column min-vh-100" style="background-color: #edfaf4;">
        <div class="container-fluid my-1">
        <form onsubmit="return saveSignature()" action="form_submit.php" method="POST" enctype="multipart/form-data">
            <!-- Page Heading -->

            <div class="card shadow mb-3">
                <!-- <div class="d-sm-flex card-header justify-content-between bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Select CSV File</h6>
                </div> -->
                <div class="card-body">

                    <!-- General Information -->
                    <div class="mb-2">
                        <div class="border p-2" style="border: 1px solid #ced4da;">
                            <div class="row">
                                <div class="col-12" style="background-color: #157aae;">
                                    <h5 class="text-center m-2 text-white"><?php echo $fetch_update['business_name'].' - '.$serial_no; ?></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12" style="background-color: #0c4562;">
                                    <h6 class="text-center m-1 text-white">GENERAL INFORMATION</h6>
                                </div>
                            </div>
                            <!-- Delegate Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Delegate Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['delegate_name']; ?>" name="delegate_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Business Address:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['address']; ?>" name="address" autocomplete="off" required style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['tin_no']; ?>" name="tin_no" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Contact No:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['contact']; ?>" name="contact_no" autocomplete="off" required>
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['license_no']; ?>" name="license_no" autocomplete="off" required style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Validity Date:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="date" value="<?php echo $fetch_update['validity_date']; ?>" name="validity_date" required>
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
                                    <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#prc"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#bir"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#sec"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#bylaws"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#gis"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#permit"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#signatory"><i class="fa fa-paperclip"></i> View Attachment</a>
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
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['bldg_name']; ?>" name="bldg_name" autocomplete="off" style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Street -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Number & Street:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['street']; ?>" name="street" autocomplete="off" style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['province']; ?>" name="province" autocomplete="off" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Town / City:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['city']; ?>" name="city" autocomplete="off" style="text-transform: uppercase;">
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
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['brgy']; ?>" name="brgy" autocomplete="off" style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="number" value="<?php echo $fetch_update['zip_code']; ?>" name="zip_code">
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
                            <?php
                            if($tag == 'HCP INDIVIDUAL'){
                            ?>
                            <!-- Email and Telephone -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Email Address:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="email" value="<?php echo $fetch_update['email']; ?>" name="email" autocomplete="off" style="text-transform: lowercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Telephone No:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="number" value="<?php echo $fetch_update['telephone']; ?>" name="telephone" autocomplete="off">
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
                                        <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['delivery_sched']; ?>" name="delivery_sched" autocomplete="off" style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['hours_from']; ?>" name="hours_from" autocomplete="off" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Clinic Hours To:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['hours_to']; ?>" name="hours_to" autocomplete="off" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }else{
                            ?>
                            <!-- Email and Telephone -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Email Address:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="email" value="<?php echo $fetch_update['email']; ?>" name="email" autocomplete="off" style="text-transform: lowercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Telephone No:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="number" value="<?php echo $fetch_update['telephone']; ?>" name="telephone" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0"> Delivery Address 2:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['del_address2']; ?>" name="del_address2" autocomplete="off" style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0"> Delivery Address 3:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['del_address3']; ?>" name="del_address3" autocomplete="off" style="text-transform: uppercase;">
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
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['authorized_name']; ?>" name="authorized_name" autocomplete="off" style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['authorized_pos']; ?>" name="authorized_pos" autocomplete="off" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <label class="form-label mb-0"><small class="text-danger">*</small> Contact No:</label>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['authorized_contact']; ?>" name="authorized_contact" autocomplete="off">
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
                                    <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['signatory1_name']; ?>" name="signatory1_name" autocomplete="off" style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['signatory1_pos']; ?>" name="signatory1_pos" autocomplete="off" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <label class="form-label mb-0"><small class="text-danger">*</small> Signatory Contact No:</label>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" value="<?php echo $fetch_update['signatory1_contact']; ?>" name="signatory1_contact" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- E-Sig -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-12 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-md-2">
                                            <label class="form-label mb-0"><small class="text-danger">*</small> E-Signature:</label>
                                        </div>
                                        <div class="col-12 col-md-10">
                                            <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#esig"><i class="fa fa-paperclip"></i> View Attachment</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="text-center mt-2 no-print">
                        <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#import">
                            <i class="fa fa-check"></i> Update
                        </span>
                    </div> -->
                    <div class="text-center mt-2 no-print">
                        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <?php
                        if($fetch_update['validated'] == 0 && in_array(166, $permission)){
                            echo '<a type="button" href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#validate"><i class="fa fa-check"></i> Validate</a>';
                        }
                        ?>
                    </div>
                </div>

                <!-- BIR modal -->
                <div class="modal fade" id="bir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> BIR 2303</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/BIR_2303.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEC modal -->
                <div class="modal fade" id="sec" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> SEC</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/SEC.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- By Laws modal -->
                <div class="modal fade" id="bylaws" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> By Laws</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/BYLAWS.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GIS modal -->
                <div class="modal fade" id="gis" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> GIS</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/GIS.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permit modal -->
                <div class="modal fade" id="permit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> Mayors Permit</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/PERMIT.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signatory modal -->
                <div class="modal fade" id="signatory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> ID with Signature</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/ID.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PRC modal -->
                <div class="modal fade" id="prc" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> PRC License</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/PRC_'.$fetch_update['license_no'].'_'.$fetch_update['validity_date'].'.jpg'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- E-sig modal -->
                <div class="modal fade" id="esig" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-image"></i> E-Signature</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body"><img class="img-fluid" src="<?php echo 'upload/customers/'.$tag.'/'.$fetch_update['business_name'].'/'.$fetch_update['application_date'].'/ESIG_'.$fetch_update['authorized_name'].'.png'; ?>"></div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Modal-->
                <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-check fa-sm"></i> Update Form</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">Are you sure you want to submit this form?</div>
                            <div class="modal-footer">
                                <input type="text" name="tag" value="<?php echo $tag; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-success btn-sm" name="submit" value="Submit">
                            </div>
                        </div>
                    </div>
                </div>
            </form> 

            <!-- Validate modal -->
            <div class="modal fade" id="validate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-success">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check"></i> Validate Form</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Do you want to validate this form?</h6>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            <form method="POST" action="cif_validate.php">
                            <input type="text" name="link" value="<?php echo $link; ?>" hidden>
                            <input type="text" name="serial_no" value="<?php echo $serial_no; ?>" hidden>
                            <button type="submit" class="btn btn-success btn-sm">Validate</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            </div>
            <!-- End Table -->
            <!-- <center><small><a href="print.php?tag=<?php echo $tag; ?>"><i>*print this customer information form*</i></a></small></center> -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->
    
    <?php
    include_once("footer.php");
    ?>

</div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<script>
    $('#provinceDropdown').select2({
        theme: "bootstrap"
    });

    // const provinceDropdown = document.getElementById("provinceDropdown");
    // const cityDropdown = document.getElementById("cityDropdown");
    // const brgyDropdown = document.getElementById("brgyDropdown");

    // let provinces = [];
    // let cities = [];
    // let barangays = [];

    // // Load data
    // Promise.all([
    //     fetch('provinces.json').then(res => res.json()),
    //     fetch('cities.json').then(res => res.json()),
    //     fetch('barangays.json').then(res => res.json())
    // ])
    // .then(([provinceData, cityData, brgyData]) => {
    //     provinces = provinceData;
    //     cities = cityData;
    //     barangays = brgyData;

    //     // Populate provinces by name (value = name)
    //     provinces.sort((a, b) => a.name.localeCompare(b.name));
    //     provinceDropdown.innerHTML = '<option value="">-- Select Province --</option>';
    //     provinces.forEach(prov => {
    //         const name = prov.name.trim().toUpperCase();
    //         $('#provinceDropdown').append(new Option(name, name));
    //     });

    //     // Initialize Select2
    //     $('#provinceDropdown').select2({
    //         placeholder: "-- Select Province --",
    //         width: '100%'
    //     });
    // })
    // .catch(err => {
    //     console.error("Error loading location data:", err);
    //     alert("Failed to load location data.");
    // });

    // // On province change
    // $('#provinceDropdown').on('change', function () {
    //     const selectedProvName = $(this).val(); // e.g., "ILOCOS NORTE"

    //     cityDropdown.innerHTML = '<option value="">-- Select City/Municipality --</option>';
    //     brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
    //     cityDropdown.disabled = !selectedProvName;
    //     brgyDropdown.disabled = true;

    //     if (selectedProvName) {
    //         const filteredCities = cities
    //             .filter(city => {
    //                 const prov = provinces.find(p => p.prov_code === city.prov_code);
    //                 return prov && prov.name.trim().toUpperCase() === selectedProvName;
    //             })
    //             .sort((a, b) => a.name.localeCompare(b.name));

    //         filteredCities.forEach(city => {
    //             const cityName = city.name.trim().toUpperCase();
    //             const option = document.createElement("option");
    //             option.value = cityName;
    //             option.textContent = cityName;
    //             cityDropdown.appendChild(option);
    //         });
    //     }
    // });

    // // On city change
    // cityDropdown.addEventListener("change", function () {
    //     const selectedCityName = this.value;

    //     brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
    //     brgyDropdown.disabled = !selectedCityName;

    //     if (selectedCityName) {
    //         const city = cities.find(c => c.name.trim().toUpperCase() === selectedCityName);
    //         if (city) {
    //             const filteredBrgys = barangays
    //                 .filter(brgy => brgy.mun_code === city.mun_code)
    //                 .sort((a, b) => a.name.localeCompare(b.name));

    //             filteredBrgys.forEach(brgy => {
    //                 const brgyName = brgy.name.trim().toUpperCase();
    //                 const option = document.createElement("option");
    //                 option.value = brgyName;
    //                 option.textContent = brgyName;
    //                 brgyDropdown.appendChild(option);
    //             });
    //         }
    //     }
    // });

    // E-sig canvas
    const canvas = document.getElementById("signatureCanvas");
    const ctx = canvas.getContext("2d");

    // Set canvas width/height
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;
    }
    resizeCanvas();

    let drawing = false;

    canvas.addEventListener("mousedown", startDrawing);
    canvas.addEventListener("mouseup", stopDrawing);
    canvas.addEventListener("mousemove", draw);

    canvas.addEventListener("touchstart", (e) => startDrawing(e.touches[0]));
    canvas.addEventListener("touchend", stopDrawing);
    canvas.addEventListener("touchmove", (e) => draw(e.touches[0]));

    function startDrawing(e) {
        drawing = true;
        ctx.beginPath();
        ctx.moveTo(getX(e), getY(e));
    }

    function stopDrawing() {
        drawing = false;
    }

    function draw(e) {
        if (!drawing) return;
        ctx.lineTo(getX(e), getY(e));
        ctx.strokeStyle = "#000";
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.stroke();
    }

    function getX(e) {
        return e.clientX - canvas.getBoundingClientRect().left;
    }

    function getY(e) {
        return e.clientY - canvas.getBoundingClientRect().top;
    }

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    window.addEventListener("resize", resizeCanvas);

    //save signature before submission
    function saveSignature() {
        const canvas = document.getElementById("signatureCanvas");
        const ctx = canvas.getContext("2d");

        const blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        const blankCtx = blank.getContext('2d');

        // Check if canvas is blank
        if (ctx.getImageData(0, 0, canvas.width, canvas.height).data.toString() === blankCtx.getImageData(0, 0, blank.width, blank.height).data.toString()) {
            alert("E-Signature is required.");
            $('#import').modal('hide'); // Close modal if open
            return false;
        }

        const dataURL = canvas.toDataURL("image/png");
        document.getElementById("signature_data").value = dataURL;
        return true;
    }

    // read-only data
    document.getElementById('business_name').addEventListener('input', function () {
        document.getElementById('readonly_business_name').value = this.value;
    });
    document.getElementById('authorized_name').addEventListener('input', function () {
        document.getElementById('readonly_authorized_name').value = this.value;
    });
    document.getElementById('authorized_pos').addEventListener('input', function () {
        document.getElementById('readonly_authorized_pos').value = this.value;
    });
</script>

    <!-- Bootstrap core JavaScript-->
    <!-- <script src="vendor/jquery/jquery.min.js"></script> -->
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