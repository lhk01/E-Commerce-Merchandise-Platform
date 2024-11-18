<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'vendor/autoload.php';
  function mailerShipped($to, $title, $subject,$orderNumber){
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
                            <h1 style='font-size: 20px; color: #000000;'>Order Shipped: $orderNumber</h1>
                        </div>
                        
                        <!-- Shipping Message -->
                        <div style='text-align: center; margin-top: 10px;'>
                            <h4 style='font-size: 16px; margin: 10px 0; color: #000000;'>Good news! Your order has been shipped and is on its way.</h4>
                        </div>

                        <!-- Order details -->
                        <div style='text-align: center; margin-top: 20px;'>
                            <p style='font-size: 14px; color: #000000;'><strong>Order Number:</strong> $orderNumber</p>
                            <p style='font-size: 14px; color: #000000;'><strong>Shipping Method:</strong> J&T Express</p>
                        </div>

                        <!-- Tracking link -->
                        <div style='text-align: center; margin-top: 20px;'>
                            <a href='https://www.jtexpress.my/tracking' style='font-size: 14px; color: #1a73e8; text-decoration: none;'>Track Your Package</a>
                        </div>


                        <!-- Footer section -->
                        <div style='border-top: 1px solid #ccc; margin-top: 40px; padding-top: 10px;'>
                            <!-- Updated footer content to reduce chances of collapsing -->
                            <p style='font-size: 13px; color: #555; margin: 5px 0;'>Keep this email safe! No one from our team will ever ask for your password, tracking information, or sensitive data.</p>
                            
                            <!-- Customizing the automated message -->
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

