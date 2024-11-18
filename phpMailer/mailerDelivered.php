<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'vendor/autoload.php';
  function mailerDelivered($to, $title, $subject, $name,$order_id,$address){
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'merchsystem@gmail.com'; // Your email
        $mail->Password = 'fhccajpanmsuhugw';   // Your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('merchsystem@gmail.com', $title);
        $mail->addAddress($to, 'User');

        $mail->isHTML(true);
        $mail->Subject = $subject;

        $mail->addEmbeddedImage('../picture/logo.png', 'logo_cid'); // CID

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
                        <h1 style='font-size: 20px; color: #000000;'>Your Order Has Been Delivered</h1>
                    </div>

                    <!-- Delivery confirmation message -->
                    <div style='text-align: center; margin-top: 10px;'>
                        <h4 style='font-size: 16px; margin: 10px 0; color: #000000;'>Hello $name,</h4>
                        <p style='font-size: 14px; color: #000000;'>We're delighted to inform you that your order <strong>#$order_id</strong> has been successfully delivered!</p>
                    </div>

                    <!-- Order details section -->
                    <div style='text-align: left; margin: 20px auto; max-width: 500px; padding: 15px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9;'>
                        <h4 style='font-size: 16px; color: #000000; margin-bottom: 10px;'>Order Details:</h4>
                        <p style='font-size: 14px; color: #000000; margin: 5px 0;'><strong>Order Number:</strong> #$order_id</p>
                        <p style='font-size: 14px; color: #000000; margin: 5px 0;'><strong>Delivery Address:</strong> $address</p>
                    </div>

                    <!-- Call-to-action section -->
                    <div style='text-align: center; margin-top: 20px;'>
                        <p style='font-size: 14px; color: #000000;'>We hope you enjoy your purchase! If you have any questions or concerns, feel free to <a href='http://localhost/merchsystem-final-draft/pages/contact-us.php' style='color: #007BFF; text-decoration: none;'>contact our support team</a>.</p>
                    </div>

                    <!-- Footer section -->
                    <div style='border-top: 1px solid #ccc; margin-top: 40px; padding-top: 10px;'>
                        <p style='font-size: 13px; color: #555; margin: 5px 0;'>Thank you for shopping with us!</p>
                        <p style='font-size: 13px; color: #555; margin: 5px 0;'>This message is automatically generated, so please don't reply to it.</p>

                        <!-- Invisible spacer to avoid content collapse -->
                        <img src='cid:spacer_cid' alt='' style='width: 1px; height: 1px; display: block; margin: 10px auto;'>
                        <!-- Adding a visible line break to help with display -->
                        <div style='height: 10px;'></div> 
                        <div style='height: 10px;'></div>
                    </div>

                </div>
            </div>";




        $mail->Body = $htmlBody;

        $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send email: " . $mail->ErrorInfo . "\n", 3, "../var/log/app_debug.log");
    }
}

