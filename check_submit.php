<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

include_once("dbconnect.php");

if(isset($_POST['submit'])){

    if($_POST['transaction'] == 'CHECK DEPOSIT'){
         $transaction = $_POST['transaction'];
         $code = 'CHK';
    }else if($_POST['transaction'] == 'PDC'){
         $transaction = $_POST['transaction'];
         $code = 'PDC';
    }else{

    }

    $website = $_POST['website'];
    $transaction = $_POST['transaction'];
    $station_code = $_POST['station_code'];

    $census_query = mysqli_query($conn,"SELECT branch FROM tbl_branch WHERE code = '$station_code'");
    $fetch_census = mysqli_fetch_assoc($census_query);
    $station_name = $fetch_census['branch'];

    $uploader_name = strtoupper(trim($_POST['uploader_name']));
    $uploader_name = str_replace([".", "/", "'"], [",", "", ""], $uploader_name);
    $uploader_email = trim($_POST['email']);
    $order_no = trim($_POST['order_no']);
    $order_no = 'CDCI MT-'.$order_no;
    $amount = $_POST['amount'];
    $deposit_date = $_POST['deposit_date'];
    $upload_date = date("Y-m-d");
    $upload_time = date("H:i:s");

    $last_query = mysqli_query($conn, "SELECT serial_no FROM tbl_check_raw ORDER BY serial_no DESC LIMIT 1");
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

    $serial_no = $fetch_last;

    // Check if already submitted
    $check_query = mysqli_query($conn,"SELECT * FROM tbl_check_raw WHERE order_no = '$order_no' AND status <= '0'");
    $row = mysqli_num_rows($check_query);

    if ($row >= 1){
        $qstring = '?status=err';
    }else{

        // Directory
        // $directory = 'C:/xampp/htdocs/oms/upload/check/';
        $directory = 'D:/public/www/oms.ramosco.net/upload/check/';

        // Ensure vendor folder exists
        $vendor_folder = $directory . $website;
        if (!file_exists($vendor_folder)) {
            mkdir($vendor_folder, 0777, true);
        }

        // Ensure order folder exists
        $filelocation = $vendor_folder . '/' . $order_no . '/';
        if (!file_exists($filelocation)) {
            mkdir($filelocation, 0777, true);
        }

        // Initialize 5 empty paths for ewt1–ewt5
        $uploaded_file_paths = ["", "", "", "", ""];

        // Handle multiple uploaded files
        if (isset($_FILES['ewt']) && is_array($_FILES['ewt']['name'])) {
            $total_files = count($_FILES['ewt']['name']);
            $total_files = min($total_files, 5); // limit to 5

            for ($i = 0; $i < $total_files; $i++) {
                if ($_FILES['ewt']['error'][$i] === 0 && !empty($_FILES['ewt']['name'][$i])) {
                    $original_name = $_FILES['ewt']['name'][$i];
                    $tmp_name = $_FILES['ewt']['tmp_name'][$i];
                    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

                    // New filename format
                    $new_filename = "{$order_no}_{$deposit_date}_" . ($i + 1) . "." . $ext;
                    $destination = $filelocation . $new_filename;

                    // Move file to final destination
                    if (move_uploaded_file($tmp_name, $destination)) {
                        // ✅ Use relative path for database (so it works on the web)
                        $uploaded_file_paths[$i] = "upload/check/$website/$order_no/$new_filename";
                    }
                }
            }
        }

        // Assign variables safely
        $ewt1 = $uploaded_file_paths[0] ?? '';
        $ewt2 = $uploaded_file_paths[1] ?? '';
        $ewt3 = $uploaded_file_paths[2] ?? '';
        $ewt4 = $uploaded_file_paths[3] ?? '';
        $ewt5 = $uploaded_file_paths[4] ?? '';

        // Insert record
        $query = "
        INSERT INTO tbl_check_raw 
        (id, website, transaction, serial_no, branch_code, branch_name, uploader, uploader_email, order_no,
         amount, deposit_date, check_file, upload_date, upload_time, 
         validated_by, validated_date, invoiced_by, invoiced_date, status, remarks)
        VALUES 
        (NULL, '$website', '$transaction', '$serial_no', '$station_code', '$station_name', '$uploader_name', '$uploader_email',
         '$order_no', '$amount', '$deposit_date', '$ewt1', '$upload_date', '$upload_time', '', '', '', '', '0', '')";

        mysqli_query($conn, $query) or die("Insert Error: " . mysqli_error($conn));

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
                    $mail->setFrom('no-reply@ramoscogroup.com', 'Check Submission');
                    $mail->addAddress($recipient);
                    $mail->addReplyTo('no-reply@ramoscogroup.com', 'Do Not Reply');

                    // Embed local image (logo)
                    $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
                    $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');

                    // Email subject and body
                    $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
                    $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');
                    $mail->Subject = 'EWT Submission';

                    // Email subject and body
                    if($website == 'STATIONPRO'){
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
                                    <small>Thank you for submitting your Check!</small>
                                    <hr>

                                    <p style="font-size:13px;">Dear '.$uploader_name.',</p>

                                    <p style="font-size:13px;">Your Check for the transcation <strong>'.$order_no.'</strong> has been submitted successfully!</p>
                                    <p style="font-size:13px;">Your document was sent for approval.<br>
                                    You will receive an approval email within 1-2 business days once the documents submitted have been reviewed.</p>

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
                                    <small>Thank you for submitting your Check!</small>
                                    <hr>

                                    <p style="font-size:13px;">Dear '.$uploader_name.',</p>

                                    <p style="font-size:13px;">Your Check for the transcation <strong>'.$order_no.'</strong> has been submitted successfully!</p>
                                    <p style="font-size:13px;">Your document was sent for approval.<br>
                                    You will receive an approval email within 1-2 business days once the documents submitted have been reviewed.</p>

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
                    error_log("Mailer Error: " . $mail->ErrorInfo);
                }
            }
        }

        header("Location: check_success.php");
    }
}else{
    $qstring = '?status=tag';
    header("Location: index.php".$qstring);
}