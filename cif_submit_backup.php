<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include_once("dbconnect.php");

if(isset($_POST['submit'])){

    $tag = $_POST['tag'];
    $type = 'N';
    $business_name = strtoupper(trim($_POST['business_name']));
    $business_name = str_replace([".", "/", "'"], [",", "", ""], $business_name);

    $business_type = $_POST['business_type'];
    if ($business_type === 'Others') {
        $business_type = ucwords(str_replace("'", "", $_POST['business_type_other'])); // Remove single quote
    } else {
        $business_type = str_replace("'", "", $business_type); // Remove single quote
    }

    $clinic_name = isset($_POST['clinic_name']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['clinic_name']))) : '';
    $delegate_name = isset($_POST['delegate_name']) ? strtoupper(str_replace(["'", ","], "", trim($_POST['delegate_name']))) : '';
    $address = strtoupper(str_replace(["'", ","], "", trim($_POST['address'])));
    $tin_no = trim($_POST['tin_no']);
    $contact_no = trim($_POST['contact_no']);
    $license_no = isset($_POST['license_no']) ? strtoupper(trim($_POST['license_no'])) : '';
    $validity_date = '0000-00-00';

    if (!empty($_POST['validity_date'])) {
        $timestamp = strtotime($_POST['validity_date']);
        if ($timestamp !== false) {
            $validity_date = date("Y-m-d", $timestamp);
        }
    }

    $bldg_name = strtoupper(str_replace([",", "/", "'"], "", $_POST['bldg_name']));
    $landmark = strtoupper(str_replace([",", "/", "'"], "", $_POST['landmark']));
    $street = isset($_POST['street']) ? strtoupper(str_replace([",", "/", "'"], "", $_POST['street'])) : '';
    $province = strtoupper($_POST['province_name']);
    $city = strtoupper($_POST['city_name']);
    $brgy = strtoupper($_POST['brgy_name']);
    $zip_code = strtoupper(trim($_POST['zip_code']));
    $email = strtolower(trim($_POST['email']));
    $telephone = trim($_POST['telephone']);

    if(!empty($_POST['delivery_sched'])) {
        $delivery_sched_array = $_POST['delivery_sched'];
        $delivery_sched = implode(',', $delivery_sched_array);
    }else{
        $delivery_sched = '';
    }

    $hours_from = !empty($_POST['hours_from']) ? $_POST['hours_from'] : '00:00:00';
    $hours_to = !empty($_POST['hours_to']) ? $_POST['hours_to'] : '00:00:00';
    $authorized_name = strtoupper(trim($_POST['authorized_name']));
    $authorized_name = str_replace('/', ',', $authorized_name);
    $authorized_pos = strtoupper(trim($_POST['authorized_pos']));
    $authorized_contact = trim($_POST['authorized_contact']);
    $signatory1_name = strtoupper(trim($_POST['signatory1_name']));
    $signatory1_pos = strtoupper(trim($_POST['signatory1_pos']));
    $signatory1_contact = trim($_POST['signatory1_contact']);
    $application_date = date("Y-m-d");
    $del_address2 = !empty($_POST['del_address2']) ? strtoupper(trim($_POST['del_address2'])) : '';
    $del_address3 = !empty($_POST['del_address3']) ? strtoupper(trim($_POST['del_address3'])) : '';

    $last_query = mysqli_query($conn, "SELECT serial_no FROM tbl_customer WHERE tag = '$tag' ORDER BY serial_no DESC LIMIT 1");
    $fetch_last = mysqli_fetch_assoc($last_query);

    if (!$fetch_last) {
        $code = $_POST['code'];
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

    $serial_no = $fetch_last;

    // Check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_customer WHERE business_name = '$business_name' AND email = '$email' AND tag = '$tag'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1){
        $qstring = '?status=err';
    }else{

        // Directory
        // $directory = 'C:/xampp/htdocs/oms/upload/customers/';
        $directory = 'D:/public/www/oms.ramosco.net/upload/customers/';

        if (file_exists($directory.'/'.$tag)){}else{mkdir($directory.'/'.$tag);}
        if (file_exists($directory.'/'.$tag.'/'.$business_name)){}else{mkdir($directory.'/'.$tag.'/'.$business_name);}
        if (file_exists($directory.'/'.$tag.'/'.$business_name.'/'.$application_date)){}else{mkdir($directory.'/'.$tag.'/'.$business_name.'/'.$application_date);}

        // Upload photos
        $upload_fields = [
            "prc_license"        => "PRC_{$license_no}_{$validity_date}",
            "bir"                => "BIR_2303",
            "sec"                => "SEC",
            "by_laws"            => "BYLAWS",
            "gis"                => "GIS",
            "mayors_permit_main" => "PERMIT",
            "mayors_permit_prc"  => "PERMIT",
            "id_speciment"       => "ID",
            "gas_facade"         => "FACADE",
            "gas_station"        => "STATION"
        ];

        // File location
        // $filelocation = 'C:/xampp/htdocs/oms/upload/customers/'.$tag.'/'.$business_name.'/'.$application_date.'/';
        $filelocation = 'D:/public/www/oms.ramosco.net/upload/customers/'.$tag.'/'.$business_name.'/'.$application_date.'/';

        // Ensure the folder exists
        if (!file_exists($filelocation)) {
            mkdir($filelocation, 0777, true);
        }

        $uploaded_file_paths = [];

        // Initialize with empty strings
        foreach ($upload_fields as $field_name => $custom_name) {
            $uploaded_file_paths[$field_name] = ''; // default
        }

        // Handle uploaded files
        foreach ($upload_fields as $field_name => $custom_name) {
            if (isset($_FILES[$field_name]) && $_FILES[$field_name]['error'] == 0) {
                $original_name = $_FILES[$field_name]['name'];
                $tmp_name = $_FILES[$field_name]['tmp_name'];
                $ext = pathinfo($original_name, PATHINFO_EXTENSION);
                $new_filename = $custom_name . '.' . $ext;
                $destination = $filelocation . $new_filename;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $uploaded_file_paths[$field_name] = $destination;
                }
            }
        }

        // Access individual paths safely
        $prc_license_path = $uploaded_file_paths['prc_license'];
        $bir_path = $uploaded_file_paths['bir'];
        $sec_path = $uploaded_file_paths['sec'];
        $by_laws_path = $uploaded_file_paths['by_laws'];
        $gis_path = $uploaded_file_paths['gis'];
        $mayors_permit_main_path = $uploaded_file_paths['mayors_permit_main'];
        $mayors_permit_prc_path  = $uploaded_file_paths['mayors_permit_prc'];
        // $mayors_permit_path = $uploaded_file_paths['mayors_permit'];
        $id_speciment_path = $uploaded_file_paths['id_speciment'];
        $mayors_permit_path = $mayors_permit_main_path ?: $mayors_permit_prc_path;
        $facade_path = $uploaded_file_paths['gas_facade'];
        $station_path = $uploaded_file_paths['gas_station'];

        // Upload e-sig
        $data = $_POST['signature_data'];

        // Clean and decode Base64 string
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);
        $imageData = base64_decode($data);

        $filePath = $directory.'/'.$tag.'/'.$business_name.'/'.$application_date.'/ESIG_'.$authorized_name.'.png';

        // Save the image
        file_put_contents($filePath, $imageData);

        // Insert data
        mysqli_query($conn,"INSERT INTO tbl_customer (id,serial_no,tag,type,business_name,business_type,delegate_name,clinic_name,address,contact,license_no,prc_license,validity_date,bir,sec,by_laws,gis,mayors_permit,id_speciment,gas_facade,gas_station,tin_no,bldg_name,landmark,street,province,city,brgy,zip_code,area_code,email,telephone,delivery_sched,hours_from,hours_to,authorized_name,authorized_pos,authorized_contact,authorized_sign,signatory1_name,signatory1_pos,signatory1_contact,signatory1_sign,application_date,validated,validated_by,validated_date,del_address2,del_address3) VALUES (NULL,'$serial_no','$tag','$type','$business_name','$business_type','$delegate_name','$clinic_name','$address','$contact_no','$license_no','$prc_license_path','$validity_date','$bir_path','$sec_path','$by_laws_path','$gis_path','$mayors_permit_path','$id_speciment_path','$facade_path','$station_path','$tin_no','$bldg_name','$landmark','$street','$province','$city','$brgy','$zip_code','+63','$email','$telephone','$delivery_sched','$hours_from','$hours_to','$authorized_name','$authorized_pos','$authorized_contact','$filePath','$signatory1_name','$signatory1_pos','$signatory1_contact','','$application_date','0','','0000-00-00','$del_address2','$del_address3') ");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $recipient = strtolower(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

            if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $mail = new PHPMailer(true);

                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host       = 'mail.ramoscogroup.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'no-reply@ramoscogroup.com';
                    $mail->Password   = '&ZAThSNY+~U*';
                    $mail->SMTPSecure = 'ssl';
                    $mail->Port       = 465;

                    // Email formatting
                    $mail->CharSet = 'UTF-8';
                    $mail->isHTML(true);
                    if($tag == 'GASCON'){
                        $mail->setFrom('no-reply@ramoscogroup.com', 'Station Pro');
                    }else{
                        $mail->setFrom('no-reply@ramoscogroup.com', 'Carbon Mart');
                    }
                    $mail->addAddress($recipient);
                    $mail->addReplyTo('no-reply@ramoscogroup.com', 'Do Not Reply');

                    // Embed local image (logo)
                    $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
                    $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');

                    // Email subject and body
                    $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
                    $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');

                    // Email subject and body
                    if($tag == 'GASCON'){
                        $mail->Subject = 'Welcome to Station Pro';
                        $mail->Body = '
                            <div style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; padding:20px; background-color:#edfaf4;">
                                <div style="max-width:600px; margin:auto; padding:30px; border-radius:8px; background-color:#ffffff">
                                    <div style="text-align:left;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse; width:100%;">
                                            <tr>
                                                <td style="padding:0;">
                                                    <img src="cid:stationprologo" alt="Station Pro Logo" width="125" height="35" style="display:block; width:135px; height:35px; border:0; outline:none; text-decoration:none;">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    <small>Thank you for signing up!</small>
                                    <h4 style="margin-top: 10px;">'.$business_name.'</h4>
                                    <hr>

                                    <p style="font-size:13px;">Dear '.$business_name.',</p>

                                    <p style="font-size:13px;">Welcome to <strong>STATION PRO!</strong></p>
                                    <p style="font-size:13px;">Your documents were sent for approval.<br>
                                    You\'ll receive an activation email within 1–3 business days.</p>

                                    <hr>

                                    <p style="font-size:11px; color:#38782e;"><b><i>CARBON DISTRIBUTION COMPANY, INC.</i></b></p>
                                </div>
                                <p style="text-align:center; font-size:11px; color:#999;">Contact Us:<br>
                                    <b>LUZON: </b>+63 995 120 3891 | carbon1@ramoscogroup.com<br>
                                    <b>VISAYAS: </b>+63 995 120 3932 | carbon2@ramoscogroup.com<br>
                                    <b>MINDANAO: </b>+63 995 120 3868 | carbon3@ramoscogroup.com
                                </p>

                                <p style="text-align:center; font-size:10px; color:#999; margin-top:20px;">
                                  <i>This is an automated message. Please do not reply to this email.</i>
                                </p>
                                <center><img src="cid:carbonlogo" alt="Carbon Logo" width="95" height="25" style="display:block; width:95; height:25; border:0; outline:none; text-decoration:none;">
                                <p style="text-align:center; font-size:11px; color:#666; margin-top:5px;">
                                    Powered by <a href="https://www.ramoscogroup.com" style="color:#ff5900;">RAMOSCO GROUP OF COMPANIES</a>
                                </p>
                            </div>
                        ';
                    } else {
                        $mail->Subject = 'Welcome to Carbon Mart';
                        $mail->Body = '
                            <div style="font-family:Helvetica Neue, Helvetica, Arial, sans-serif; padding:20px; background-color:#edfaf4;">
                                <div style="max-width:600px; margin:auto; padding:30px; border-radius:8px; background-color:#ffffff">
                                    <div style="text-align:left;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                                            <tr>
                                                <td style="padding:0;">
                                                    <img src="cid:carbonlogo" alt="Carbon Logo" width="135" height="35" style="display:block; width:135px; height:35px; border:0; outline:none; text-decoration:none;">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>
                                    <small>Thank you for signing up!</small>
                                    <h4 style="margin-top: 10px;">'.$business_name.'</h4>
                                    <hr>

                                    <p style="font-size:13px;">Dear '.$business_name.',</p>

                                    <p style="font-size:13px;">Welcome to <strong>CARBON MART!</strong></p>
                                    <p style="font-size:13px;">Your documents were sent for approval.<br>
                                    You\'ll receive an activation email within 1–3 business days.</p>

                                    <hr>

                                    <p style="font-size:11px; color:#38782e;"><b><i>CARBON DISTRIBUTION COMPANY, INC.</i></b></p>
                                </div>
                                <p style="text-align:center; font-size:11px; color:#999;">Contact Us:<br>
                                    <b>LUZON: </b>+63 995 120 3891 | carbon1@ramoscogroup.com<br>
                                    <b>VISAYAS: </b>+63 995 120 3932 | carbon2@ramoscogroup.com<br>
                                    <b>MINDANAO: </b>+63 995 120 3868 | carbon3@ramoscogroup.com
                                </p>

                                <p style="text-align:center; font-size:10px; color:#999; margin-top:20px;">
                                  <i>This is an automated message. Please do not reply to this email.</i>
                                </p>
                                <p style="text-align:center; font-size:11px; color:#666; margin-top:5px;">
                                    Powered by <a href="https://www.ramoscogroup.com" style="color:#ff5900;">RAMOSCO GROUP OF COMPANIES</a>
                                </p>
                            </div>
                        ';
                    }

                    $mail->send();
                    // echo 'Email sent successfully.';
                } catch (Exception $e) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            }
        }

        // Telegram bot details
        $botToken = "8417317558:AAHtoFSG5McK1FOMzQrSq3IYhk6zso9ihbU";  // your bot token
        $chatID   = "-4928381014";          // your chat id

        // Build the message
        if($tag == 'HCP INDIVIDUAL'){
            $text = "📩 New Form Submission:\n"
                  . "🏢 $tag\n\n"
                  . "HCP Name: $business_name\n"
                  . "HCP Type: $business_type\n"
                  . "Clinic Name: $clinic_name\n"
                  . "Clinic Address: $address\n"
                  . "Delegate Name: $delegate_name\n"
                  . "Email Address: $email\n"
                  . "Contact No: $contact_no\n\n"
                  . "✅ Please validate this user on:\n"
                  . "https://oms.ramosco.net:8443/";

        }else if($tag == 'INSTITUTION'){
            if($business_type == 'Private Lying In'){
                $text = "📩 New Form Submission:\n"
                      . "🏢 $tag\n\n"
                      . "HCP Name: $business_name\n"
                      . "HCP Type: $business_type\n"
                      . "Lying In Name: $clinic_name\n"
                      . "Lying In Address: $address\n"
                      . "Delegate Name: $delegate_name\n"
                      . "Email Address: $email\n"
                      . "Contact No: $contact_no\n\n"
                      . "✅ Please validate this user on:\n"
                      . "https://oms.ramosco.net:8443/";
            }else{
                $text = "📩 New Form Submission:\n"
                      . "🏢 $tag\n\n"
                      . "HCP Name: $business_name\n"
                      . "HCP Type: $business_type\n"
                      . "Business Address: $address\n"
                      . "Delegate Name: $delegate_name\n"
                      . "Email Address: $email\n"
                      . "Contact No: $contact_no\n\n"
                      . "✅ Please validate this user on:\n"
                      . "https://oms.ramosco.net:8443/";
            }
        }else{

        }

        // Send to Telegram
        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $data = [
            'chat_id' => $chatID,
            'text'    => $text,
            'parse_mode' => 'HTML'
        ];

        $options = [
            "http" => [
                "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
                "method"  => "POST",
                "content" => http_build_query($data),
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        header("Location: cif_success.php");
    }
}else{
    $qstring = '?status=tag';
    header("Location: index.php".$qstring);
}