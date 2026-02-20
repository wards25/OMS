<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include_once("dbconnect.php");
$user = $_SESSION['name'];

$link = $_POST['link'];
$serial_no = $_POST['serial_no'];
$order_no = $_POST['order_no'];
$uploader_name = $_POST['uploader_name'];
$remarks = ucfirst($_POST['remarks']);

mysqli_query($conn,"UPDATE tbl_ewt_raw SET status='3',validated_by='$user',validated_date=NOW(),remarks='$remarks' WHERE serial_no='$serial_no'");

$action = "VALIDATED: EWT ".$serial_no;
$module = "FORMS";
mysqli_query($conn, "INSERT INTO tbl_history VALUES (NULL, '$user', '$action', '$module', '$client_ip', '$mac', '$device', '$model', NOW())");

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
            $mail->setFrom('no-reply@ramoscogroup.com', 'Check Submission - Rejected');
            $mail->addAddress($recipient);
            $mail->addReplyTo('no-reply@ramoscogroup.com', 'Do Not Reply');

            // Embed local image (logo)
            $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
            $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');

            // Email subject and body
            $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
            $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');
            $mail->Subject = 'Check Submission';

            // Email subject and body
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
                        <small>EWT Submission</small>
                        <hr>

                        <p style="font-size:13px;">Dear '.$uploader_name.',</p>

                        <p style="font-size:13px;">Your Check for the transcation <strong>'.$order_no.'</strong> has been rejected!</p>
                        <p style="font-size:13px;"><br>Reason: '.$remarks.'</p>
                        <p style="font-size:13px;"><br>
                        To submit Check again: https://oms.ramosco.net:8443/payment/</p>

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

            $mail->send();
            // echo 'Email sent successfully.';
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }
}

$qstring = '?status=reject';

// Redirect to the listing page
header("Location: ".$link.$qstring);