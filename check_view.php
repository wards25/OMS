<?php
session_start();
include_once("header.php");
include_once("dbconnect.php");
$user = $_SESSION['name'];

if(isset($_SESSION['id']) && in_array(180, $permission))
{
include_once("nav_forms.php");

$order_no = $_GET['order_no'];
$deposit_date = $_GET['deposit_date'];
$link = $_GET['link'];

$update_query = mysqli_query($conn,"SELECT * FROM tbl_check_raw WHERE order_no = '$order_no' AND deposit_date = '$deposit_date'");
$fetch_update = mysqli_fetch_assoc($update_query);
$status = $fetch_update['status'];
?>

    <!-- Begin Page Content -->
    <div id="content-wrapper" class="d-flex flex-column min-vh-100" style="background-color: #edfaf4;">
        <div class="container-fluid my-1">
        <!-- <form onsubmit="return saveSignature()" action="form_submit.php" method="POST" enctype="multipart/form-data"> -->
            <!-- Page Heading -->

            <div class="card shadow mb-3">
                <!-- <div class="d-sm-flex card-header justify-content-between bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Select CSV File</h6>
                </div> -->
                <div class="card-body" style="position: relative;">

                    <?php
                    $ribbonText = '';
                    $ribbonColor = '';

                    if ($status == '0') {
                        $ribbonText = 'NOT VALIDATED';
                        $ribbonColor = 'rgba(231, 74, 59, 0.8)'; // red
                    } elseif ($status == '1') {
                        $ribbonText = 'VALIDATED';
                        $ribbonColor = 'rgba(40, 167, 69, 0.8)'; // green
                    } elseif ($status == '2') {
                        $ribbonText = 'INVOICED';
                        $ribbonColor = 'rgba(78, 115, 223, 0.8)'; // purple
                    } elseif ($status == '3') {
                        $ribbonText = 'REJECTED';
                        $ribbonColor = 'rgba(113, 115, 132, 0.8)'; // gray
                    }
                    ?>

                    <?php if ($ribbonText != ''): ?>
                    <style>
                        .ribbon {
                            width: 160px;
                            height: 160px;
                            overflow: hidden;
                            position: absolute;
                            top: 0;
                            right: 0;
                            z-index: 10;
                            pointer-events: none;
                        }
                        .ribbon span {
                            position: absolute;
                            display: block;
                            width: 220px;       /* tighter width */
                            height: 30px;       /* fixed height for vertical centering */
                            line-height: 30px;  /* match height for perfect vertical centering */
                            background: <?= $ribbonColor ?>;
                            color: #fff;
                            font-weight: bold;
                            text-align: center;
                            transform: rotate(45deg);
                            top: 40px;
                            right: -55px;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                            font-size: 0.9rem;
                            letter-spacing: 1px;
                        }
                    </style>
                    <div class="ribbon"><span><?= $ribbonText ?></span></div>
                    <?php endif; ?>

                    <!-- General Information -->
                    <div class="mb-2">
                        <div class="border p-2" style="border: 1px solid #ced4da;">
                            <div class="row">
                                <div class="col-12" style="background-color: #157aae;">
                                    <h5 class="text-center m-2 text-white"><?php echo $order_no; ?></h5>
                                </div>
                            </div>
                            <!-- General Information -->
                            <div class="row">
                                <div class="col-12" style="background-color: #0c4562;">
                                    <h6 class="text-center m-1 text-white">CHECK INFORMATION</h6>
                                </div>
                            </div>
                            <!-- Website -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-6 mb-0">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Website:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <select class="form-control form-control-sm" name="website" autocomplete="off" required disabled>
                                        <option value="<?php echo $fetch_update['website']; ?>"><?php echo $fetch_update['website']; ?></option>
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
                                            <input class="form-control form-control-sm" type="text" name="transaction" autocomplete="off" required style="text-transform: uppercase;" value="<?php echo $fetch_update['transaction']; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Station Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Station Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="station_name" autocomplete="off" required style="text-transform: uppercase;" value="<?php echo $fetch_update['branch_code'].' - '.$fetch_update['branch_name']; ?>" readonly>
                                </div>
                            </div>
                            <!-- Uploader Name -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Uploader Name:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="uploader_name" autocomplete="off" required style="text-transform: uppercase;" value="<?php echo $fetch_update['uploader']; ?>" readonly>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> Email Address:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <input class="form-control form-control-sm" type="text" name="email" autocomplete="off" required value="<?php echo $fetch_update['uploader_email']; ?>" readonly>
                                </div>
                            </div>
                            <!-- Order No and Date -->
                            <div class="row mt-2 align-items-center">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Amount:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control form-control-sm" type="text" name="order_no" value="<?php echo number_format($fetch_update['amount'], 2); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Deposit Date:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" name="order_date" autocomplete="off" required value="<?php echo $fetch_update['deposit_date']; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit Date and Time -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Submit Date:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <div class="input-group input-group-sm">
                                                <input class="form-control form-control-sm" type="text" name="order_no" value="<?php echo $fetch_update['upload_date']; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-sm-4 col-md-4">
                                            <h6 class="mb-0"><small class="text-danger">*</small> Submit Time:</h6>
                                        </div>
                                        <div class="col-12 col-sm-8 col-md-8">
                                            <input class="form-control form-control-sm" type="text" name="order_date" autocomplete="off" required value="<?php echo $fetch_update['upload_time']; ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- EWT -->
                            <div class="row mt-0 align-items-center">
                                <div class="col-12 col-md-2">
                                    <h6 class="mb-0"><small class="text-danger">*</small> EWT Attachment:</h6>
                                </div>
                                <div class="col-12 col-md-10">
                                    <a type="button" href="#" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#ewt1"><i class="fa fa-paperclip"></i> View Attachment</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2 no-print">
                        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <?php
                        if($fetch_update['status'] == 0 && in_array(179, $permission)){
                            echo '<a type="button" href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#validate"><i class="fa fa-check"></i> Validate</a>';
                            echo '&nbsp;<a type="button" href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#reject"><i class="fa fa-times"></i> Reject</a>';
                        }
                        // echo ' <a type="button" href="#" class="btn btn-warning btn-sm text-dark" data-toggle="modal" data-target="#email"><i class="fa fa-envelope"></i> Resend Confirmation Mail</a>';
                        ?>
                    </div>
                </div>

            </div>

                <!-- EWT1 modal -->
                <div class="modal fade" id="ewt1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-info">
                                <h6 class="modal-title text-light" id="exampleModalLabel1"><i class="fas fa-image"></i> Attachment</h6>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true"><small>×</small></span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php
                                $fileBase = 'upload/check/'.$fetch_update['website'].'/'.$fetch_update['order_no'].'/' . $fetch_update['order_no'].'_'.$fetch_update['deposit_date'].'_1';

                                if (file_exists($fileBase.'.pdf')) {
                                    echo '<p class="text-center"><i>This file is a PDF.</i><br>
                                            <a href="'.$fileBase.'.pdf" target="_blank" class="btn btn-primary btn-sm">
                                                <i class="fas fa-file-pdf"></i> Open PDF
                                            </a>
                                          </p>';
                                } elseif (file_exists($fileBase.'.jpg')) {
                                    echo '<img class="img-fluid" src="'.$fileBase.'.jpg">';
                                } elseif (file_exists($fileBase.'.jpeg')) {
                                    echo '<img class="img-fluid" src="'.$fileBase.'.jpeg">';
                                } elseif (file_exists($fileBase.'.png')) {
                                    echo '<img class="img-fluid" src="'.$fileBase.'.png">';
                                } elseif (file_exists($fileBase.'.heic')) {
                                    echo '<img class="img-fluid" src="'.$fileBase.'.heic">';
                                } else {
                                    echo '<p class="text-danger text-center">No file available</p>';
                                }
                                ?>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
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
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-check"></i> Validate Check</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Do you want to validate this Check?</h6>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            <form method="POST" action="check_validate.php">
                            <input type="text" name="link" value="<?php echo $link; ?>" hidden>
                            <input type="text" name="serial_no" value="<?php echo $fetch_update['serial_no']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="uploader_name"  style="text-transform: uppercase;" value="<?php echo $fetch_update['uploader']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="email" autocomplete="off" value="<?php echo $fetch_update['uploader_email']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="order_no" autocomplete="off" value="<?php echo $fetch_update['order_no']; ?>" hidden>
                            <button type="submit" class="btn btn-success btn-sm">Validate</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject modal -->
            <div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger">
                            <h6 class="modal-title text-light" id="exampleModalLabel"><i class="fas fa-times"></i> Reject Check</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <form method="POST" action="check_reject.php">
                        <div class="modal-body">
                            <label>Rejection Remarks:</label>
                            <textarea class="form-control form-control-sm" name="remarks" autocomplete="off" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            <input type="text" name="link" value="<?php echo $link; ?>" hidden>
                            <input type="text" name="serial_no" value="<?php echo $fetch_update['serial_no']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="uploader_name"  style="text-transform: uppercase;" value="<?php echo $fetch_update['uploader']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="email" autocomplete="off" value="<?php echo $fetch_update['uploader_email']; ?>" hidden>
                            <input class="form-control form-control-sm" type="text" name="order_no" autocomplete="off" value="<?php echo $fetch_update['order_no']; ?>" hidden>
                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resend modal -->
            <div class="modal fade" id="email" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h6 class="modal-title text-dark" id="exampleModalLabel"><i class="fas fa-envelope"></i> Resend Confirmation</h6>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true"><small>×</small></span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h6>Do you want to resend confirmation email?</h6>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
                            <form method="POST" action="cif_email.php">
                            <input type="text" name="link" value="<?php echo $link; ?>" hidden>
                            <input type="text" name="email" value="<?php echo $fetch_update['email']; ?>" hidden>
                            <input type="text" name="business_name" value="<?php echo $fetch_update['business_name']; ?>" hidden>
                            <input type="text" name="tag" value="<?php echo $_GET['tag']; ?>" hidden>
                            <button type="submit" class="btn btn-warning text-dark btn-sm">Resend</button>
                            </form>
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

        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                preview.src = URL.createObjectURL(this.files[0]);
                preview.style.display = 'block';
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

    // const provinceDropdown = document.getElementById("provinceDropdown");
    // const cityDropdown = document.getElementById("cityDropdown");
    // const brgyDropdown = document.getElementById("brgyDropdown");

    // const provinceNameInput = document.getElementById("provinceName");
    // const cityNameInput = document.getElementById("cityName");
    // const brgyNameInput = document.getElementById("brgyName");

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

    //     // Populate provinces by name (value = prov_code)
    //     provinces.sort((a, b) => a.name.localeCompare(b.name));
    //     provinceDropdown.innerHTML = '<option value="">-- Select Province --</option>';
    //     provinces.forEach(prov => {
    //         const name = prov.name.trim().toUpperCase();
    //         $('#provinceDropdown').append(new Option(name, prov.prov_code));
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
    //     const selectedProvCode = $(this).val();

    //     cityDropdown.innerHTML = '<option value="">-- Select City/Municipality --</option>';
    //     brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
    //     cityDropdown.disabled = !selectedProvCode;
    //     brgyDropdown.disabled = true;

    //     // Set hidden province name
    //     const prov = provinces.find(p => p.prov_code === selectedProvCode);
    //     provinceNameInput.value = prov ? prov.name.trim().toUpperCase() : "";

    //     if (selectedProvCode) {
    //         const filteredCities = cities
    //             .filter(city => city.prov_code === selectedProvCode)
    //             .sort((a, b) => a.name.localeCompare(b.name));

    //         filteredCities.forEach(city => {
    //             const cityName = city.name.trim().toUpperCase();
    //             const option = document.createElement("option");
    //             option.value = city.mun_code;  
    //             option.textContent = cityName;
    //             cityDropdown.appendChild(option);
    //         });
    //     }
    // });

    // // On city change
    // cityDropdown.addEventListener("change", function () {
    //     const selectedMunCode = this.value;

    //     brgyDropdown.innerHTML = '<option value="">-- Select Barangay --</option>';
    //     brgyDropdown.disabled = !selectedMunCode;

    //     // Set hidden city name
    //     const city = cities.find(c => c.mun_code === selectedMunCode);
    //     cityNameInput.value = city ? city.name.trim().toUpperCase() : "";

    //     if (selectedMunCode) {
    //         const filteredBrgys = barangays
    //             .filter(brgy => brgy.mun_code === selectedMunCode)
    //             .sort((a, b) => a.name.localeCompare(b.name));

    //         filteredBrgys.forEach(brgy => {
    //             const brgyName = brgy.name.trim().toUpperCase();
    //             const option = document.createElement("option");
    //             option.value = brgy.brgy_code; 
    //             option.textContent = brgyName;
    //             brgyDropdown.appendChild(option);
    //         });
    //     }
    // });

    // // On barangay change
    // brgyDropdown.addEventListener("change", function () {
    //     const brgy = barangays.find(b => b.brgy_code === this.value);
    //     brgyNameInput.value = brgy ? brgy.name.trim().toUpperCase() : "";

    //     console.log("Full Address:", getFullAddress());
    // });

    // // Get full address (names only)
    // function getFullAddress() {
    //     return [brgyNameInput.value, cityNameInput.value, provinceNameInput.value]
    //         .filter(Boolean)
    //         .join(", ");
    // }

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

<?php
}else{
    header("Location: denied.php");
}