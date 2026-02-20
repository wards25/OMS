<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$link = $_POST['link'];
$business_name = $_POST['business_name'];
$tag = $_POST['tag'];

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
            $qstring = '?status=resend';
        } catch (Exception $e) {
            $qstring = '?status=error';
        }
    }
}

header("Location: ".$link.$qstring);

?>
