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

if(isset($_GET['transaction'])){
    if($_GET['transaction'] == 'CHECK DEPOSIT'){
         $transaction = $_GET['transaction'];
         $code = 'CHECK';
    }else if($_GET['transaction'] == 'PDC'){
         $transaction = $_GET['transaction'];
         $code = 'PDC';
    }else{
    }
}
?>

    <!-- Begin Page Content -->
    <div id="content-wrapper" class="d-flex flex-column min-vh-100" style="background-color: #edfaf4;">
        <div class="container-fluid my-4">
        <form onsubmit="return saveSignature()" action="../check_submit.php" method="POST" enctype="multipart/form-data">

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
                            <!-- <?php
                            $last_query = mysqli_query($conn, "SELECT serial_no FROM tbl_ewt_raw ORDER BY serial_no DESC LIMIT 1");
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
                            ?> -->
                            <!-- <input type="text" name="serial_no" value="<?php echo $fetch_last; ?>" hidden> -->
                        </div>
                    </div>

                    <!-- Customer Information Form -->
                    <div class="mb-2">
                        <div class="border p-2" style="border: 1px solid #ced4da;">
                            <div class="row">
                                <div class="col-12" style="background-color: #157aae;">
                                    <h5 class="text-center m-2 text-white">DEPOSIT SLIP SUBMISSION FORM
                                    </h5>
                                </div>
                            </div>
                            <!-- General Information -->
                            <div class="row">
                                <div class="col-12" style="background-color: #0c4562;">
                                    <h6 class="text-center m-1 text-white">ORDER INFORMATION</h6>
                                </div>
                            </div>
                            <!-- Website -->
                            <?php
                            if(empty($transaction)){
                            ?>
                                <div class="row mt-2 align-items-center">
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> Website:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <select class="form-control form-control-sm" name="website" autocomplete="off" required>
                                                    <option value="CARBONMART">CARBONMART</option>
                                                    <option value="STATIONPRO">STATIONPRO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-0">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-4 col-md-4">
                                                <h6 class="mb-0"><small class="text-danger">*</small> Transaction Type:</h6>
                                            </div>
                                            <div class="col-12 col-sm-8 col-md-8">
                                                <select class="form-control form-control-sm" name="transaction" autocomplete="off" required>
                                                    <option value="CHECK DEPOSIT">CHECK DEPOSIT</option>
                                                    <option value="PDC">PDC</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }else{
                            ?>
                                <div class="row mt-2 align-items-center">
                                    <div class="col-12 col-md-2">
                                        <h6 class="mb-0"><small class="text-danger">*</small> Website:</h6>
                                    </div>
                                    <div class="col-12 col-md-10">
                                        <select class="form-control form-control-sm" name="website" autocomplete="off" required>
                                            <option value="CARBONMART">CARBONMART</option>
                                            <option value="STATIONPRO">STATIONPRO</option>
                                        </select>
                                    </div>
                                </div>

                                <input type="text" name="transaction" value="<?php echo $transaction; ?>" hidden>
                            <?php
                            }
                            ?>
                            <!-- Station Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Station Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <?php 
                                    $query = "SELECT * FROM tbl_branch WHERE type IN ('SHELL', 'PETRON') ORDER BY branch ASC";
                                    $result = $conn->query($query);
                                    ?>
                                    <select id="stationDropdown" class="form-control form-control-sm" name="station_code" required>
                                        <?php 
                                        echo '<option value=""></option>';
                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['code']) . '">' . htmlspecialchars($row['branch']) . '</option>';
                                            }
                                        } else {
                                            echo '<option disabled>No stations found</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Uploader Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Uploader Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="uploader_name" autocomplete="off" required style="text-transform: uppercase;">
                                </div>
                            </div>
                            <!-- Order -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Order No:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-primary text-white" style="font-size: 12px;"><b>CDCI MT-</b></span>
                                        <input class="form-control form-control-sm" type="number" name="order_no" placeholder="Enter a valid order number..." autocomplete="off" required style="font-style: italic;">
                                    </div>
                                </div>
                            </div>
                            <!-- Amount and Date -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Amount:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" 
                                               type="number" 
                                               name="amount" 
                                               step="0.01" 
                                               autocomplete="off" 
                                               required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Deposit Date:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="date" name="deposit_date" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="row mt-2 align-items-center">
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
                                            <h6 class="mb-0"><small class="text-danger">*</small> Confirm Email Address:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="email" name="email2" autocomplete="off" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- EWT -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-2">
                                    <?php
                                    if(isset($_GET['transaction'])){
                                        if($_GET['transaction'] == 'CHECK DEPOSIT'){
                                            echo '<h6 class="mb-0"><small class="text-danger">*</small> Deposit Slip:</h6>';
                                        }else if($_GET['transaction'] == 'PDC'){
                                            echo '<h6 class="mb-0"><small class="text-danger">*</small> Picture of Check:</h6>';
                                        }else{
                                            echo '<h6 class="mb-0"><small class="text-danger">*</small> Attachment:</h6>';
                                        }
                                    }else{
                                        echo '<h6 class="mb-0"><small class="text-danger">*</small> Attachment:</h6>';
                                    }
                                    ?>
                                </div>
                                <div class="col-12 col-md-10">
                                    <label id="prcLabel" style="display:block;">
                                        <input 
                                            type="file" 
                                            accept="image/*" 
                                            name="ewt[]" 
                                            id="prcInput" 
                                            multiple 
                                            style="position:absolute;opacity:0;width:1px;height:1px;" 
                                        />
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-block" onclick="openCamera('prcInput')">
                                                    <i class="fa fa-camera"></i> Camera
                                                </button>
                                            </div>
                                            <div class="col-6">
                                                <button type="button" class="btn btn-sm btn-outline-success btn-block" onclick="openGallery('prcInput')">
                                                    <i class="fa fa-image"></i> Gallery / File
                                                </button>
                                            </div>
                                        </div>
                                    </label>

                                    <div id="prcPreviewContainer" class="d-flex flex-wrap mt-2 gap-2"></div>

                                    <!-- <div class="mt-0">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addMorePhotos('prcInput')">
                                            <i class="fa fa-plus"></i> Add More Photos
                                        </button>
                                    </div> -->
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
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-center mt-2 no-print">
                                        <center><input type="checkbox" id="termsCheckbox" class="text-center mb-4"> I agree to the <a href="#" data-toggle="modal" data-target="#privacy"><u> terms and conditions</u></a></center>
                                        <!-- <button type="button" class="btn btn-sm btn-secondary" onclick="clearSignature()">Clear Signature</button> -->
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
                                <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fa fa-check fa-sm"></i> Submit</h6>
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
    $('#stationDropdown').select2({
        theme: "bootstrap"
    });

    let selectedFiles = [];
    const MAX_FILES = 5;

    document.addEventListener("DOMContentLoaded", () => {
        setupPreview('prcInput', 'prcPreviewContainer');

        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            // Validate file count
            if (selectedFiles.length < 1) {
                e.preventDefault();
                alert("Please attach at least one EWT image.");
                return false;
            }
            if (selectedFiles.length > MAX_FILES) {
                e.preventDefault();
                alert(`You can only upload up to ${MAX_FILES} attachments.`);
                return false;
            }

            // Make sure selected files are attached before submitting
            updateInputFiles('prcInput');
            return saveSignature();
        });
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

    function addMorePhotos(inputId) {
        const input = document.getElementById(inputId);
        input.removeAttribute('capture');
        input.click();
    }

    function setupPreview(inputId, containerId) {
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);
        if (!input || !container) return;

        input.addEventListener('change', async function () {
            const newFiles = Array.from(this.files);

            if (selectedFiles.length + newFiles.length > MAX_FILES) {
                alert(`You can only upload up to ${MAX_FILES} attachments.`);
                this.value = "";
                return;
            }

            for (let file of newFiles) {
                if (file.name.toLowerCase().endsWith(".heic")) {
                    try {
                        const convertedBlob = await heic2any({ blob: file, toType: "image/jpeg" });
                        file = new File([convertedBlob], file.name.replace(/\.heic$/i, ".jpg"), { type: "image/jpeg" });
                    } catch (err) {
                        console.error("HEIC conversion error:", err);
                        continue;
                    }
                }
                selectedFiles.push(file);
            }

            updatePreview(containerId, inputId);
            this.value = "";
        });
    }

    function updateInputFiles(inputId) {
        const input = document.getElementById(inputId);
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }

    function updatePreview(containerId, inputId) {
        const container = document.getElementById(containerId);
        container.innerHTML = "";

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const wrapper = document.createElement("div");
                wrapper.classList.add("position-relative", "m-1");
                wrapper.style.width = "120px";

                wrapper.innerHTML = `
                    <div style="position:relative;">
                        <span class="badge bg-dark position-absolute top-0 start-0" style="font-size:12px;">${index + 1}</span>
                        <img src="${e.target.result}" class="img-thumbnail" style="width:100%;height:100px;object-fit:cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0"
                            style="border-radius:50%;width:22px;height:22px;padding:0;"
                            onclick="removePhoto(${index}, '${containerId}', '${inputId}')">
                            &times;
                        </button>
                    </div>
                `;
                container.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        updateInputFiles(inputId);
    }

    function removePhoto(index, containerId, inputId) {
        selectedFiles.splice(index, 1);
        updatePreview(containerId, inputId);
    }

    function saveSignature() {
        const email = document.querySelector('input[name="email"]').value.trim();
        const email2 = document.querySelector('input[name="email2"]').value.trim();

        if (email !== email2) {
            alert("Email addresses do not match.");
            document.querySelector('input[name="email"]').focus();
            return false;
        }

        document.getElementById("loading").style.display = "block";
        document.getElementById("submitButton").style.display = "none";
        return true;
    }

    // ✅ Enable submit only after checkbox is checked
    const checkbox = document.getElementById('termsCheckbox');
    const submitBtn = document.getElementById('submitButton');

    checkbox.addEventListener('change', function () {
        if (this.checked) {
            submitBtn.style.pointerEvents = 'auto';
            submitBtn.style.opacity = '1';
            submitBtn.classList.remove('disabled');
        } else {
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
