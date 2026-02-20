<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$recipient = 'it@ramoscogroup.com';

if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'mail.ramoscogroup.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'no-reply@ramoscogroup.com'; // your email
        $mail->Password   = '&ZAThSNY+~U*';       // your email password
        $mail->SMTPSecure = 'ssl';                       // or 'tls' if using port 587
        $mail->Port       = 465;

        // Email formatting
        $mail->CharSet = 'UTF-8'; // Prevents character corruption
        $mail->isHTML(true);
        $mail->setFrom('no-reply@ramoscogroup.com', 'Station Pro');
        $mail->addAddress($recipient); // Recipient from form submission

        // Embed local image (logo)
        $mail->AddEmbeddedImage('img/carbon.png', 'carbonlogo');
        $mail->AddEmbeddedImage('img/stationpro.png', 'stationprologo');

        // Set business name to SHELL
        $business_name = 'SHELL';

        // Email subject and body
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

        $mail->send();
        echo 'Email sent successfully.';
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

?>
