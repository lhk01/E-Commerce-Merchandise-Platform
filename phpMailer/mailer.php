<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer files manually
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function mailer($to, $title, $subject, $body, $time) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Your email
        $mail->Password = 'your_app_password';   // Your email app password (16 digits)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('your_email@gmail.com', $title); // Your email
        $mail->addAddress($to, 'User');

        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->addEmbeddedImage('../picture/logo.png', 'logo_cid');

        // HTML email body with inline CSS and CID for the image
        $htmlBody = "
           <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; text-align: center; max-width: 600px; margin: 0 auto;'>
                <!-- Main container -->
                <div style='max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>

                    <!-- Logo section -->
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <img src='cid:logo_cid' style='width: 60px;' alt='Logo'>
                    </div>
                    
                    <!-- Subject line -->
                    <div style='text-align: center;'>
                        <h1 style='font-size: 20px; color: #000000;'>$subject</h1>
                    </div>

                    <!-- OTP message -->
                    <div style='text-align: center; margin-top: 10px;'>
                        <h4 style='font-size: 16px; margin: 10px 0; color: #000000;'>$body</h4>
                    </div>

                    <!-- OTP instructions -->
                    <div style='text-align: center; margin-top: 5px;'>
                        <p style='font-size: 14px; color: #000000; margin-top: 20px;'>$time</p>
                    </div>

                    <!-- Footer section -->
                    <div style='border-top: 1px solid #ccc; margin-top: 40px; padding-top: 10px;'>
                        <!-- Updated footer content to reduce chances of collapsing -->
                        <p style='font-size: 13px; color: #555; margin: 5px 0;'>Keep this email safe! No one from our team will ever ask for your password, OTP, or banking information.</p>
                        
                        <!-- Customizing the automated message -->
                        <p style='font-size: 13px; color: #555; margin: 5px 0;'>This message is automatically generated, so please don't reply to it.</p>

                        <!-- Invisible spacer to avoid content collapse -->
                        <img src='cid:spacer_cid' alt='' style='width: 1px; height: 1px; display: block; margin: 10px auto;'>
                        <!-- Adding a visible line break to help with display -->
                        <div style='height: 10px;'></div> 
                        <div style='height: 10px;'></div>
                    </div>

                </div>
            </div>


            ";



        $mail->Body = $htmlBody;

        $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send email: " . $mail->ErrorInfo . "\n", 3, "../var/log/app_debug.log");
    }
}

