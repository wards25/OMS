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
<style>
    @media (min-width: 768px) {
      .width-desktop {
        width: 100%;
      }
    }
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<?php
include_once("dbconnect.php");
$tag = 'HCP INDIVIDUAL';
$code = 'HCP';
?>

    <!-- Begin Page Content -->
    <div id="content-wrapper" class="d-flex flex-column min-vh-100" style="background-color: #edfaf4;">
        <div class="container-fluid my-4">
        <form onsubmit="return saveSignature()" action="../cif_submit.php" method="POST" enctype="multipart/form-data">

            <!-- Page Heading -->
            <div class="card shadow mb-3">
                <div class="card-body">

                    <script>
                    window.setTimeout(function() {
                        $(".alert").fadeTo(500, 0).slideUp(500, function(){
                            $(this).remove(); 
                        });
                    }, 2000);
                    </script>

                    <?php
                    // Get status message
                    if(!empty($_GET['status'])){
                        switch($_GET['status']){
                            case 'succ':
                                $statusType = 'alert-success';
                                $statusMsg = '<i class="fa fa-check-circle"></i>&nbsp;<b>Success!</b> Form has been submitted successfully.';
                                break;
                            case 'tag':
                                $statusType = 'alert-danger';
                                $statusMsg = '<i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Form tagging error.';
                                break;
                            case 'err':
                                $statusType = 'alert-danger';
                                $statusMsg = '<i class="fa fa-exclamation-triangle"></i>&nbsp;<b>Error!</b> Already encoded.';
                                break;
                            default:
                                $statusType = '';
                                $statusMsg = '';
                        }
                    }
                    ?>

                    <?php if(!empty($statusMsg)){ ?>
                    <div class="alert <?php echo $statusType; ?> alert-dismissable fade show" role="alert">
                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo $statusMsg; ?>
                    </div>
                    <?php } ?>

                    <style>
                        @media (max-width: 767.98px) {
                            .serial-no-container {
                                position: static !important;
                                margin-top: 10px;
                                text-align: center;
                            }
                        }
                        .upload-buttons {
                            display: flex;
                            gap: 5px;
                            flex-wrap: wrap;
                        }
                    </style>

                    <div class="d-flex justify-content-center align-items-center flex-wrap mb-2 position-relative">
                        <img class="img-fluid me-2 mb-2" alt="OMS Logo" src="img/oms.png" width="48">
                        <img class="img-fluid mb-2" alt="RGC Logo" src="img/carbon.png" width="170">

                        <!-- Serial No: absolute on desktop, static on mobile -->
                        <div class="serial-no-container" style="position: absolute; top: 0; left: 0;">
                            <?php
                            $last_query = mysqli_query($conn, "SELECT serial_no FROM tbl_customer WHERE tag = '$tag' ORDER BY serial_no DESC LIMIT 1");
                            $fetch_last = mysqli_fetch_assoc($last_query);

                            if (!$fetch_last) {
                                $fetch_last = $code . '-0001';
                            } else {
                                $last_serial_no = $fetch_last['serial_no'];
                                if (preg_match('/([A-Za-z]+)-(\d+)/', $last_serial_no, $matches)) {
                                    $prefix = $matches[1];
                                    $numeric_part = (int)$matches[2];
                                    $numeric_part++;
                                    $fetch_last = $prefix . '-' . str_pad($numeric_part, 4, '0', STR_PAD_LEFT);
                                } else {
                                    $fetch_last = $last_serial_no;
                                }
                            }

                            echo '<span class="me-2 text-danger"><i>Serial No.: <b>' . $fetch_last . '</b></i></span>';
                            ?>
                            <input type="text" name="serial_no" value="<?php echo $fetch_last; ?>" hidden>
                            <input type="text" name="code" value="<?php echo $code; ?>" hidden>
                        </div>
                    </div>

                    <!-- Customer Information Form -->
                    <div class="mb-2">
                        <div class="border p-2" style="border: 1px solid #ced4da;">
                            <div class="row">
                                <div class="col-12" style="background-color: #157aae;">
                                    <h5 class="text-center m-2 text-white">CUSTOMER INFORMATION FORM - 
                                        <?php
                                        if($tag == 'HCP INDIVIDUAL'){
                                            echo 'DOCTOR';
                                        }else if($tag == 'INSTITUTION'){
                                            echo 'HOSPITAL, CLINIC, LYING IN, LGU, OTHERS';
                                        }else if($tag == 'GASCON'){
                                            echo 'GASCON';
                                        }else{
                                        }
                                        ?>
                                    </h5>
                                </div>
                                <div class="col-12" style="background-color: #0c4562;">
                                    <h6 class="text-center m-1 text-white">TO BE FILLED-UP BY CUSTOMER</h6>
                                </div>
                                <div class="col-12 bg-light">
                                    <small class="m-1 text-dark"><center><b>TYPE OF APPLICATION</b></center></small>
                                </div>
                            </div>
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Type of Application:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <div class="col-12 d-flex flex-column flex-md-row gap-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="type" value="N" id="newChannel" required checked>
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
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> With EWT:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <div class="col-12 d-flex flex-column flex-md-row gap-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="ewt" value="1" id="ewt" unchecked>
                                                    <label class="form-check-label" for="ewt">YES</label>
                                                </div>
                                            </div>
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
                                    <?php
                                    if($tag == 'HCP INDIVIDUAL'){
                                        echo '<h6 class="mb-0"><small class="text-danger">*</small> Healthcare Professional Name:</h6>';
                                    }else{
                                        echo '<h6 class="mb-0"><small class="text-danger">*</small> Business Name:</h6>';
                                    }
                                    ?>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" id="business_name" name="business_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Business Type -->
                            <div class="row mt-2 align-items-center">
                                <?php if ($tag == 'HCP INDIVIDUAL') { ?>
                                    <div class="col-12 col-md-2">
                                        <h6 class="mb-0"><small class="text-danger">*</small> Health Care Professional Type:</h6>
                                    </div>
                                    <div class="col-12 col-md-2 text-center">
                                        <a href="../ins/" class="btn btn-sm btn-block btn-outline-danger"><i>*CLICK IF NOT A DOCTOR*</i></a>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <input type="text" class="form-control form-control-sm" name="business_type" value="Doctor" required readonly>
                                    </div>
                                <?php } else if ($tag == 'INSTITUTION'){ ?>
                                    <div class="col-12 col-md-2">
                                        <h6 class="mb-0"><small class="text-danger">*</small> Institutional Type:</h6>
                                    </div>
                                    <div class="col-12 col-md-2 text-center">
                                        <a href="../hcp/" class="btn btn-sm btn-block btn-outline-danger"><i>*CLICK IF A DOCTOR*</i></a>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <select class="form-control form-control-sm" name="business_type" id="institutionalType" autocomplete="off" required>
                                            <option value=""></option>
                                            <option value="Private Hospital">Private Hospital</option>
                                            <option value="Government Hospital">Government Hospital</option>
                                            <option value="Private Clinic">Private Clinic</option>
                                            <option value="Private Lying In">Private Lying In</option>
                                            <option value="Government Lying In">Government Lying In</option>
                                            <option value="LGU Health Office">LGU Health Office</option>
                                            <option value="Others">Others</option>
                                        </select>
                                        <!-- Hidden text input for 'Others' -->
                                        <input type="text" class="form-control form-control-sm mt-2" name="business_type_other" id="institutionalTypeOther" placeholder="Please specify" style="display: none;" required>
                                    </div>
                                <?php } else { ?>
                                    <div class="col-12 col-md-2">
                                        <h6 class="mb-0"><small class="text-danger">*</small> Gas Brand / Station:</h6>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <select class="form-control form-control-sm" name="business_type" id="institutionalType" autocomplete="off" required>
                                            <option value=""></option>
                                            <option value="Petron">Petron</option>
                                            <option value="Shell">Shell</option>
                                            <option value="Total">Total</option>
                                            <option value="Others">Others</option>
                                        </select>
                                        <!-- Hidden text input for 'Others' -->
                                        <input type="text" class="form-control form-control-sm mt-2" name="business_type_other" id="institutionalTypeOther" placeholder="Please specify" style="display: none;" required>
                                    </div>
                                <?php } ?>
                            </div>

                            <?php if ($tag == 'HCP INDIVIDUAL') { ?>
                            <!-- Delegate Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Clinic Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="clinic_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Clinic Address:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="address" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <?php } else if ($tag == 'INSTITUTION'){ ?>
                            <!-- Delegate Name -->
                            <div class="row mt-2 align-items-center" id="clinicNameRow">
                                <div class="col-12 col-md-2">
                                    <h6 id="clinicNameLabel" class="mb-0"><small class="text-danger">*</small> Medical Delegate / Representative Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="clinic_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 id="clinicAddressLabel" class="mb-0"><small class="text-danger">*</small> Business Address:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="address" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <?php } else { } ?>
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
                                            <input class="form-control form-control-sm" type="date" name="validity_date" required>
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
                                    <label id="prcLabel" style="display: block;">
                                        <input type="file" accept="image/*" name="prc_license" id="prcInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openCamera('prcInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="openGallery('prcInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                            </div>
                                        </div>
                                    </label>
                                    <img id="prcPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="PRC Preview">
                                </div>
                            </div>
                            <?php
                            }else if($tag == 'INSTITUTION' || $tag == 'GASCON'){
                            ?>
                            <!-- Wrap PRC fields in a container for toggling -->
                            <div id="prcFields" style="display:none;">
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
                                                <input class="form-control form-control-sm" type="date" name="validity_date" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PRC and Mayors Permit -->
                                <div class="row mt-0 align-items-center">
                                    <!-- Mayors Permit -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> PRC License Photo:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="prcLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="prc_license" id="prcInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openCamera('prcInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="openGallery('prcInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="prcPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="PRC Preview">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Mayors Permit -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> Mayors Permit:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="mayorsLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="mayors_permit_prc" id="mayorsInputPrc" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('mayorsInputPrc')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('mayorsInputPrc')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="mayorsprcPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="Mayors Permit Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wrap BIR and SEC -->
                            <div id="birSecDiv">
                                <!-- BIR and SEC -->
                                <div class="row mt-1 align-items-center">
                                    <!-- BIR -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> BIR (2303):</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="birLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="bir" id="birInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openCamera('birInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="openGallery('birInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="birPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="BIR Preview">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- SEC -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> Articles of Incorporation Registered with the SEC:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="secLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="sec" id="secInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('secInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('secInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="secPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="SEC Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Wrap Mayors Permit and ID Specimen -->
                            <div id="mayorIdDiv">
                                <!-- Mayors Permit and ID Specimen -->
                                <div class="row mt-0 align-items-center">
                                    <!-- Mayors Permit -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> Mayors Permit:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="mayorsLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="mayors_permit_main" id="mayorsInputMain" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('mayorsInputMain')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('mayorsInputMain')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="mayorsmainPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="Mayors Permit Preview">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- ID Speciment -->
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> ID with Specimen Signature of Signatory:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <label id="idLabel" style="display: block;">
                                                    <input type="file" accept="image/*" name="id_speciment" id="idInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('idInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('idInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                        </div>
                                                    </div>
                                                </label>
                                                <img id="idPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="ID Speciment Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- By Laws and GIS -->
                            <!-- <div class="row mt-0 align-items-center"> -->
                                <!-- By Laws -->
                                <!-- <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> By Laws:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <label id="bylawsLabel" style="display: block;">
                                                <input type="file" accept="image/*" name="by_laws" id="bylawsInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('bylawsInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('bylawsInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery</button>
                                                    </div>
                                                </div>
                                            </label>
                                            <img id="bylawsPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="By Laws Preview">
                                        </div>
                                    </div>
                                </div> -->
                                <!-- GIS -->
                                <!-- <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> GIS:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <label id="gisLabel" style="display: block;">
                                                <input type="file" accept="image/*" name="gis" id="gisInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('gisInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('gisInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery</button>
                                                    </div>
                                                </div>
                                            </label>
                                            <img id="gisPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="GIS Preview">
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            
                            <?php
                            }else{    
                            }

                            if($tag == 'HCP INDIVIDUAL' || $tag == 'INSTITUTION'){
                            ?>
                            <!-- Delegate Name -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Medical Delegate / Representative Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="delegate_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <?php
                            }else{
                            ?>
                            <!-- Facade and Station -->
                            <div class="row mt-0 align-items-center">
                                <!-- Facade -->
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Gas Convenience Store Facade:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <label id="facadeLabel" style="display: block;">
                                                <input type="file" accept="image/*" name="gas_facade" id="facadeInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openCamera('facadeInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="openGallery('facadeInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                    </div>
                                                </div>
                                            </label>
                                            <img id="facadePreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="Facade Preview">
                                        </div>
                                    </div>
                                </div>
                                <!-- Station -->
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Gas Station Photo:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <label id="stationLabel" style="display: block;">
                                                <input type="file" accept="image/*" name="gas_station" id="stationInput" required style="position: absolute; opacity: 0; width: 1px; height: 1px;" />
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-primary flex-fill btn-block" onclick="openCamera('stationInput')"><i class="fa fa-sm fa-solid fa-camera"></i> Camera</button>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="button" class="btn btn-sm btn-outline-success flex-fill btn-block" onclick="openGallery('stationInput')"><i class="fa fa-sm fa-solid fa-image"></i> Gallery / File</button>
                                                    </div>
                                                </div>
                                            </label>
                                            <img id="stationPreview" style="display:none; max-height:150px; margin-top:5px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;" alt="Station Preview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
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
                                    <h6 class="mb-0"><small class="text-danger">*</small> Building Name, Number, & Street:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="bldg_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Street -->
                            <!-- <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Number & Street:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="street" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div> -->
                            <!-- Landmark -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Nearest Landmark:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="landmark" autocomplete="off" required style="text-transform: uppercase;">
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
                                            <select id="provinceDropdown" class="form-control form-control-sm" name="province" required>
                                                <option value="">-- Select Province --</option>
                                            </select>
                                            <input type="hidden" name="province_name" id="provinceName">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Town / City:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <select id="cityDropdown" class="form-control form-control-sm" name="city" required disabled></select>
                                            <input type="hidden" name="city_name" id="cityName">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Barangay / District  and Zip -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Barangay / District:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <select id="brgyDropdown" class="form-control form-control-sm" name="brgy" required disabled></select>
                                            <input type="hidden" name="brgy_name" id="brgyName">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Zip Code:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="number" name="zip_code" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Area Code and Mobile Number -->
                            <div class="row mt-2 align-items-center">
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
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Mobile No:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="number" name="telephone" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Email Address:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="email" name="email" autocomplete="off" required style="text-transform: lowercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Confirm Email Address:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="email" name="email2" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if($tag == 'HCP INDIVIDUAL'){
                            ?>
                            <!-- Delivery Schedule -->
                            <div class="row mt-3 align-items-center">
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
                                            <input class="form-control form-control-sm" type="time" name="hours_from" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Clinic Hours To:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="time" name="hours_to" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }else{
                            ?>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <?php
                    if($tag == 'GASCON'){
                    }else{
                    ?>
                    <!-- Other Addresses -->
                    <div class="mb-2">
                        <div class="border p-2" style="border: 1px solid #ced4da;">
                            <div class="row">
                                <!-- <div class="col-12" style="background-color: #0c4562;">
                                    <h6 class="text-center m-1 text-white">OTHER DELIVERY ADDRESS (OPTIONAL)</h6>
                                </div> -->
                                <div class="col-12 bg-secondary text-light text-center py-0">
                                    <small><b>OTHER DELIVERY ADDRESS (OPTIONAL)</b></small>
                                </div>
                            </div>
                            <!-- Other Addresses -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0"> Delivery Address 2:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="del_address2" autocomplete="off" style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0"> Delivery Address 3:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="del_address3" autocomplete="off" style="text-transform: uppercase;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                    ?>

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
                                    <label class="form-label mb-0"> Same As Above: </label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <div class="col-12 d-flex flex-column flex-md-row gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="type" id="same" required>
                                            <label class="form-check-label" for="same"><i>check if same as above</i></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0"><small class="text-danger">*</small> Auth Signatory Name:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" id="signatory1_name" name="signatory1_name" autocomplete="off" required style="text-transform: uppercase;">
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
                                            <input class="form-control form-control-sm" type="text" id="signatory1_pos" name="signatory1_pos" autocomplete="off" required style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <label class="form-label mb-0"><small class="text-danger">*</small> Signatory Contact No:</label>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" id="signatory1_contact" name="signatory1_contact" autocomplete="off" required>
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
                                    <h6 class="text-center m-1 text-white">CONSENT</h6>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-dark text-center">
                                    <i>
                                        I / We wish to apply as customer of <b><u>Carbon Distribution Company Inc.</u></b>. For this purpose/ I / We hereby certify that all of the above information are true and correct.
                                        <br>
                                        I / We understood that all our purchases from <b><u>Carbon Distribution Company Inc.</u></b> shall be on online banking.
                                        <br>
                                        By signing this application form, I / We consent to the processing of any of our applicable personal information in accordance with the Data Privacy Act of 2012 and <a href="#" data-toggle="modal" data-target="#privacy" style="color:#66975f;"><u><b>Carbon's Privacy Notice.</b></u></a>  I / We also accept the terms and conditions of Carbon as stated. 
                                        <br>
                                        In addition, I / We confirm that our authorized representatives, as communicated to Carbon, are allowed to make any inquiries necessary to process this application.
                                    </i>
                                </div>
                            </div>
                            <hr>
                            <!-- Signature -->
                            <div class="row mt-1 align-items-center">
                                <div class="col-12 col-md-2">
                                    <label class="form-label mb-0">Business Name:</label>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" id="readonly_business_name" readonly style="text-transform: uppercase;">
                                </div>
                            </div>
                            <div class="row mt-2 align-items-center">
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
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 mb-2">
                                    <label class="form-label mb-0"><small class="text-danger">*</small> E-Signature:</label>
                                </div>
                                <div class="col-12">
                                    <div class="border rounded bg-white" style="width:100%; height:170px; position:relative;">
                                        <canvas id="signatureCanvas" style="width:100%; height:100%;"></canvas>
                                        <input type="hidden" name="signature_data" id="signature_data">
                                    </div>
                                    <div class="text-center mt-2 no-print">
                                        <center><input type="checkbox" id="termsCheckbox" class="text-center mb-4"> I agree to the <a href="#" data-toggle="modal" data-target="#privacy"><u> terms and conditions</u></a></center>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">Clear Signature</button>
                                        <span id="submitButton" class="btn btn-success btn-sm disabled"
                                              data-toggle="modal" data-target="#import"
                                              style="pointer-events: none; opacity: 0.65;">
                                            <i class="fa fa-check"></i> Submit
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Modal-->
                <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success">
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-check fa-sm"></i> Submit Form</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to submit this form?
                                <div id="loading" style="display: none; text-align: center;">
                                    <hr>
                                    <i class="fa fa-spinner fa-pulse fa-fw loading-icon" style="margin-bottom: 10px;"></i>
                                    <p>
                                        The system is gathering your requirements. 
                                        <br>Kindly wait 1 to 2 minutes for the completion of the transaction. 
                                        <br>
                                        Thank you for your patience!
                                    </p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="text" name="tag" value="<?php echo $tag; ?>" hidden>
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                <input type="submit" class="btn btn-success btn-sm" name="submit" value="Submit" id="submitButton">
                            </div>
                        </div>
                    </div>
                </div>
            </form> 

            <!-- Privacy Modal-->
            <div class="modal fade" id="privacy" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #0c4562;">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa-solid fa-list-ul"></i> Carbon's Privacy Notice</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <b>1. Sharing of Information</b>
                            <br>
                            <p>We do not sell or rent your personal information. However. we may share your information with:<p>
                            <ul>
                                <li>Service Providers — such as couriers, payment gateways, or CRM systems</li>
                                <li>Healthcare Partners — for verification or sampling coordination (with your consent)</li>
                                <li>Government or regulatory bodies — when required by law</li>
                                <li>Product Suppliers — for account verification and monitoring</li>
                            </ul>
                            <p>All third parties are bound by strict confidentiality and data protection agreements.</p>
                            <b>2. Data Security</b>
                            <br>
                            <p>We take appropriate security measures to protect your personal data from unauthorized access, disclosure, alteration, or destruction. This includes encryption, firewalls, and
                            regular monitoring.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
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
    
    <footer class="footer py-3 text-center bg-white">
        <div class="container">
            <?php
            $year = date("Y");
            $version_query = mysqli_query($conn, "
                SELECT version 
                FROM tbl_changelog 
                ORDER BY
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 1), 'v', -1) AS UNSIGNED) DESC,
                    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, '.', 2), '.', -1) AS UNSIGNED) DESC,
                    CAST(SUBSTRING_INDEX(version, '.', -1) AS UNSIGNED) DESC
                LIMIT 1
            ");
            $fetch_version = mysqli_fetch_assoc($version_query);

            echo '<small>&copy; 2024 - ' . $year . ' RGC OMS ' . $fetch_version['version'] . ' | Developed by CAB</small>';
            ?>
        </div>
    </footer>

</div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/heic2any/dist/heic2any.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("input[type=file]").forEach(input => {
            input.addEventListener("change", async function(e) {
                let file = e.target.files[0];
                if (file && file.name.toLowerCase().endsWith(".heic")) {
                    try {
                        let convertedBlob = await heic2any({ blob: file, toType: "image/jpeg" });
                        let newFile = new File([convertedBlob], file.name.replace(/\.heic$/i, ".jpg"), { type: "image/jpeg" });

                        // Replace file in the input
                        let dt = new DataTransfer();
                        dt.items.add(newFile);
                        e.target.files = dt.files;

                        console.log("Converted HEIC to JPG before upload.");
                    } catch (err) {
                        console.error("HEIC conversion error:", err);
                    }
                }
            });
        });
    });
    
    // For HCP INDIVIDUAL
    const hcpTypeSelect = document.getElementById('hcpType');
    if (hcpTypeSelect) {
        hcpTypeSelect.addEventListener('change', function () {
            const otherInput = document.getElementById('hcpTypeOther');
            if (this.value === 'Others') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                otherInput.value = '';
            }
        });
    }

    // For INSTITUTIONAL
    const institutionalTypeSelect = document.getElementById('institutionalType');
    if (institutionalTypeSelect) {
        institutionalTypeSelect.addEventListener('change', function () {
            const otherInput = document.getElementById('institutionalTypeOther');
            const prcFields = document.getElementById('prcFields');
            const prcInputs = prcFields ? prcFields.querySelectorAll("input") : [];

            const clinicNameLabel = document.querySelector('#clinicNameLabel');
            const clinicAddressLabel = document.querySelector('#clinicAddressLabel');
            const clinicNameRow = document.querySelector('#clinicNameRow');
            const clinicNameInput = clinicNameRow ? clinicNameRow.querySelector('input') : null;

            const birSecDiv = document.getElementById('birSecDiv'); 
            const mayorIdDiv = document.getElementById('mayorIdDiv'); 

            const mayorMain = document.getElementById('mayorsInputMain'); // normal mayor permit
            const mayorPrc = document.getElementById('mayorsInputPrc');   // PRC mayor permit
            const idSpec = document.getElementById('idInput');            // ID field

            // Handle "Others"
            if (this.value === 'Others') {
                otherInput.style.display = 'block';
                otherInput.required = true;
            } else {
                otherInput.style.display = 'none';
                otherInput.required = false;
                otherInput.value = '';
            }

            // Handle "Private Lying In"
            if (this.value === 'Private Lying In') {
                // Show PRC
                if (prcFields) {
                    prcFields.style.display = 'block';
                    prcInputs.forEach(input => input.required = true);
                }
                if (clinicNameRow) clinicNameRow.style.display = 'flex';
                if (clinicNameLabel) clinicNameLabel.innerHTML = '<small class="text-danger">*</small> Private Lying In Name:';
                if (clinicAddressLabel) clinicAddressLabel.innerHTML = '<small class="text-danger">*</small> Private Lying In Address:';
                if (clinicNameInput) clinicNameInput.required = true;

                // Hide BIR/SEC
                if (birSecDiv) {
                    birSecDiv.style.display = 'none';
                    birSecDiv.querySelectorAll('input').forEach(input => {
                        input.required = false;
                        input.value = '';
                    });
                    birSecDiv.querySelectorAll('img').forEach(img => img.style.display = 'none');
                }

                // Hide normal Mayor/ID
                if (mayorIdDiv) {
                    mayorIdDiv.style.display = 'none';
                    if (mayorMain) mayorMain.required = false;
                    if (idSpec) idSpec.required = false;
                    if (mayorMain) mayorMain.value = '';
                    if (idSpec) idSpec.value = '';
                    mayorIdDiv.querySelectorAll('img').forEach(img => img.style.display = 'none');
                }

                // Require PRC mayor permit
                if (mayorPrc) mayorPrc.required = true;

            } else {
                // Hide PRC
                if (prcFields) {
                    prcFields.style.display = 'none';
                    prcInputs.forEach(input => {
                        input.required = false;
                        input.value = '';
                    });
                    prcFields.querySelectorAll('img').forEach(img => img.style.display = 'none');
                }
                if (clinicNameRow) clinicNameRow.style.display = 'none';
                if (clinicNameInput) {
                    clinicNameInput.required = false;
                    clinicNameInput.value = '';
                }
                if (clinicAddressLabel) clinicAddressLabel.innerHTML = '<small class="text-danger">*</small> Business Address:';

                // Show BIR/SEC
                if (birSecDiv) {
                    birSecDiv.style.display = 'block';
                    birSecDiv.querySelectorAll('input').forEach(input => input.required = true);
                }

                // Show normal Mayor/ID
                if (mayorIdDiv) {
                    mayorIdDiv.style.display = 'block';
                    if (mayorMain) mayorMain.required = true;
                    if (idSpec) idSpec.required = true;
                }

                // PRC mayor permit optional here
                if (mayorPrc) mayorPrc.required = false;
            }
        });

        // Trigger once on page load to set initial state
        institutionalTypeSelect.dispatchEvent(new Event('change'));
    }

    $('#provinceDropdown').select2({
        theme: "bootstrap"
    });

    function openCamera(inputId) {
        const input = document.getElementById(inputId);
        input.setAttribute('capture', 'environment');
        input.click();
    }
    function openGallery(inputId) {
        const input = document.getElementById(inputId);
        input.removeAttribute('capture');
        input.click();
    }

    function setupPreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return; // Prevent errors if elements are missing

        input.addEventListener('change', async function () {
            if (this.files && this.files[0]) {
                let file = this.files[0];
                let ext = file.name.split('.').pop().toLowerCase();

                if (ext === "heic" || file.type === "image/heic") {
                    try {
                        let convertedBlob = await heic2any({ blob: file, toType: "image/jpeg" });
                        let newFile = new File([convertedBlob], file.name.replace(/\.heic$/i, ".jpg"), { type: "image/jpeg" });

                        // Replace the file in the input so upload will send JPG
                        let dt = new DataTransfer();
                        dt.items.add(newFile);
                        this.files = dt.files;

                        // Preview the converted JPG
                        preview.src = URL.createObjectURL(newFile);
                        preview.style.display = 'block';
                    } catch (err) {
                        console.error("HEIC preview conversion error:", err);
                        preview.style.display = 'none';
                    }
                } else {
                    // Normal preview (jpg/png/etc.)
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            }
        });
    }

    setupPreview('prcInput', 'prcPreview');
    setupPreview('birInput', 'birPreview');
    setupPreview('secInput', 'secPreview');
    setupPreview('bylawsInput', 'bylawsPreview');
    setupPreview('gisInput', 'gisPreview');
    setupPreview('mayorsInput', 'mayorsPreview');
    setupPreview('idInput', 'idPreview');
    setupPreview('mayorsInputPrc', 'mayorsprcPreview');
    setupPreview('mayorsInputMain', 'mayorsmainPreview');
    setupPreview('facadeInput', 'facadePreview');
    setupPreview('stationInput', 'stationPreview');

    const provinceDropdown = document.getElementById("provinceDropdown");
    const cityDropdown = document.getElementById("cityDropdown");
    const brgyDropdown = document.getElementById("brgyDropdown");

    const provinceNameInput = document.getElementById("provinceName");
    const cityNameInput = document.getElementById("cityName");
    const brgyNameInput = document.getElementById("brgyName");

    let provinces = [];
    let cities = [];
    let barangays = [];

    // Load data
    Promise.all([
        fetch('provinces.json').then(res => res.json()),
        fetch('cities.json').then(res => res.json()),
        fetch('barangays.json').then(res => res.json())
    ])
    .then(([provinceData, cityData, brgyData]) => {
        provinces = provinceData;
        cities = cityData;
        barangays = brgyData;

        // Populate provinces by name (value = prov_code)
        provinces.sort((a, b) => a.name.localeCompare(b.name));
        provinceDropdown.innerHTML = '<option value="">-- Select Province --</option>';
        provinces.forEach(prov => {
            const name = prov.name.trim().toUpperCase();
            $('#provinceDropdown').append(new Option(name, prov.prov_code));
        });

        // Initialize Select2
        $('#provinceDropdown').select2({
            placeholder: "-- Select Province --",
            width: '100%'
        });
    })
    .catch(err => {
        console.error("Error loading location data:", err);
        alert("Failed to load location data.");
    });

    // On province change
    $('#provinceDropdown').on('change', function () {
        const selectedProvCode = $(this).val();

        cityDropdown.innerHTML = '<option value="">-- Select City/Municipality --</option>';
        brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
        cityDropdown.disabled = !selectedProvCode;
        brgyDropdown.disabled = true;

        // Set hidden province name
        const prov = provinces.find(p => p.prov_code === selectedProvCode);
        provinceNameInput.value = prov ? prov.name.trim().toUpperCase() : "";

        if (selectedProvCode) {
            const filteredCities = cities
                .filter(city => city.prov_code === selectedProvCode)
                .sort((a, b) => a.name.localeCompare(b.name));

            filteredCities.forEach(city => {
                const cityName = city.name.trim().toUpperCase();
                const option = document.createElement("option");
                option.value = city.mun_code;  
                option.textContent = cityName;
                cityDropdown.appendChild(option);
            });
        }
    });

    // On city change
    cityDropdown.addEventListener("change", function () {
        const selectedMunCode = this.value;

        brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
        brgyDropdown.disabled = !selectedMunCode;

        // Set hidden city name
        const city = cities.find(c => c.mun_code === selectedMunCode);
        cityNameInput.value = city ? city.name.trim().toUpperCase() : "";

        if (selectedMunCode) {
            const filteredBrgys = barangays
                .filter(brgy => brgy.mun_code === selectedMunCode)
                .sort((a, b) => a.name.localeCompare(b.name));

            filteredBrgys.forEach(brgy => {
                const brgyName = brgy.name.trim().toUpperCase();
                const option = document.createElement("option");
                option.value = brgy.brgy_code; 
                option.textContent = brgyName;
                brgyDropdown.appendChild(option);
            });
        }
    });

    // On barangay change
    brgyDropdown.addEventListener("change", function () {
        const brgy = barangays.find(b => b.brgy_code === this.value);
        brgyNameInput.value = brgy ? brgy.name.trim().toUpperCase() : "";

        console.log("Full Address:", getFullAddress());
    });

    // Get full address (names only)
    function getFullAddress() {
        return [brgyNameInput.value, cityNameInput.value, provinceNameInput.value]
            .filter(Boolean)
            .join(", ");
    }

    document.getElementById('same').addEventListener('change', function () {
        const isChecked = this.checked;

        const authName = document.getElementById('authorized_name').value;
        const authPos = document.getElementById('authorized_pos').value;
        const authContact = document.querySelector('[name="authorized_contact"]').value;

        const signNameField = document.getElementById('signatory1_name');
        const signPosField = document.getElementById('signatory1_pos');
        const signContactField = document.getElementById('signatory1_contact');

        if (isChecked) {
            signNameField.value = authName;
            signPosField.value = authPos;
            signContactField.value = authContact;

            // Optional: disable to prevent editing
            signNameField.readOnly = true;
            signPosField.readOnly = true;
            signContactField.readOnly = true;
        } else {
            signNameField.value = '';
            signPosField.value = '';
            signContactField.value = '';

            signNameField.readOnly = false;
            signPosField.readOnly = false;
            signContactField.readOnly = false;
        }
    });

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

    canvas.addEventListener("mousedown", (e) => {
        e.preventDefault();
        startDrawing(e);
    });
    canvas.addEventListener("mouseup", stopDrawing);
    canvas.addEventListener("mousemove", draw);

    // Touch events with preventDefault to avoid page scroll
    canvas.addEventListener("touchstart", (e) => {
        e.preventDefault();
        startDrawing(e.touches[0]);
    }, { passive: false });

    canvas.addEventListener("touchmove", (e) => {
        e.preventDefault();
        draw(e.touches[0]);
    }, { passive: false });

    canvas.addEventListener("touchend", (e) => {
        e.preventDefault();
        stopDrawing();
    }, { passive: false });

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

    // Save signature before submission
    // function saveSignature() {
    //     const canvas = document.getElementById("signatureCanvas");
    //     const ctx = canvas.getContext("2d");

    //     const blank = document.createElement('canvas');
    //     blank.width = canvas.width;
    //     blank.height = canvas.height;
    //     const blankCtx = blank.getContext('2d');

    //     // Check if the signature is blank
    //     if (
    //         ctx.getImageData(0, 0, canvas.width, canvas.height).data.toString() ===
    //         blankCtx.getImageData(0, 0, blank.width, blank.height).data.toString()
    //     ) {
    //         alert("E-Signature is required.");
    //         $('#import').modal('hide'); // Close modal if open
    //         return false;
    //     }

    //     // Show the spinner and hide the submit button
    //     document.getElementById("loading").style.display = "block";
    //     document.getElementById("submitButton").style.display = "none"; // Hide the submit button

    //     // Save signature data
    //     const dataURL = canvas.toDataURL("image/png");
    //     document.getElementById("signature_data").value = dataURL;

    //     // Allow the form to submit after processing
    //     return true;  // Form will submit now
    // }

    function saveSignature() {
        // Get email values
        const email = document.querySelector('input[name="email"]').value.trim();
        const email2 = document.querySelector('input[name="email2"]').value.trim();

        // Check if emails match
        if (email !== email2) {
            alert("Email addresses do not match.");
            // Optionally, focus the email field
            document.querySelector('input[name="email"]').focus();
            return false; // Prevent form submission
        }

        // Existing code to check if signature is empty
        const ctx = document.getElementById("signatureCanvas").getContext("2d");
        const blank = document.createElement('canvas');
        blank.width = ctx.canvas.width;
        blank.height = ctx.canvas.height;
        const blankCtx = blank.getContext('2d');

        if (
            ctx.getImageData(0, 0, ctx.canvas.width, ctx.canvas.height).data.toString() ===
            blankCtx.getImageData(0, 0, blank.width, blank.height).data.toString()
        ) {
            alert("E-Signature is required.");
            $('#import').modal('hide'); // Close modal if open
            return false;
        }

        // Show loading spinner
        document.getElementById("loading").style.display = "block";
        document.getElementById("submitButton").style.display = "none";
        // Save signature data
        const dataURL = document.getElementById("signatureCanvas").toDataURL("image/png");
        document.getElementById("signature_data").value = dataURL;

        return true; // Proceed with form submission
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

    // Enable/Disable Submit Button based on checkbox
    const checkbox = document.getElementById('termsCheckbox');
    const submitBtn = document.getElementById('submitButton');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            // Enable the submit button
            submitBtn.style.pointerEvents = 'auto';
            submitBtn.style.opacity = '1';
            submitBtn.classList.remove('disabled');
        } else {
            // Disable the submit button
            submitBtn.style.pointerEvents = 'none';
            submitBtn.style.opacity = '0.65';
            submitBtn.classList.add('disabled');
        }
    });
</script>

<style>
    /* Spinner Styles */
    .loading-icon {
        font-size: 50px;
        color: #28a745;
        animation: spin 1.5s infinite linear;
    }

    .loading-text {
        margin-top: 20px;
        font-size: 18px;
        color: #333;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<!-- Bootstrap core JavaScript-->
<!-- <script src="vendor/jquery/jquery.min.js"></script> -->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>

</html>
