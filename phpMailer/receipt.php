<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;

  require 'vendor/autoload.php';
  function mailerReceipt($to, $title, $subject, $items,$total,$payment_method,$transaction_id,$date,$discount){
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


      $htmlBody = "
        <div style=\"font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; text-align: center; max-width: 600px; margin: 0 auto;\">
            <!-- Main container -->
            <div style=\"max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);\">

                <!-- Logo section -->
                <div style=\"text-align: center; margin-bottom: 20px;\">
                    <img src=\"cid:logo_cid\" style=\"width: 60px;\" alt=\"Logo\">
                </div>

                <!-- Receipt Header -->
                <div style=\"text-align: center; margin-bottom: 20px;\">
                    <h1 style=\"font-size: 22px; color: #000;\">Receipt</h1>
                    <p style=\"font-size: 14px; color: #555; margin: 5px 0;\">Thank you for your purchase!</p>
                </div>

                <!-- Transaction Details -->
                <div style=\"margin-bottom: 20px;\">
                    <table style=\"width: 100%; border-collapse: collapse;\">
                        <tr style=\"background-color: #f8f8f8; text-align: left;\">
                            <th style=\"padding: 10px; font-size: 14px; color: #333;\">Description</th>
                            <th style=\"padding: 10px; font-size: 14px; color: #333;\">Quantity</th>
                            <th style=\"padding: 10px; font-size: 14px; color: #333; text-align: right;\">Amount</th>
                        </tr>";
                        
        foreach ($items as $item) {
            $htmlBody .= "
                        <tr>
                            <td style=\"padding: 10px; font-size: 14px; color: #555; text-align: left;\">{$item['name']}</td>
                        ";    

            if($item['category'] !==  'Apparel'){
                 $htmlBody .= "<td style=\"padding: 10px; font-size: 14px; color: #555; text-align: center;\">{$item['quantity']}</td>";
            }else{
                if($item['size_m'] !== null){
                     $htmlBody .= "<td style=\"padding: 10px; font-size: 14px; color: #555; text-align: center;\">{$item['size_m']}</td>";
                }

                if($item['size_l'] !== null){
                     $htmlBody .= "<td style=\"padding: 10px; font-size: 14px; color: #555; text-align: center;\">{$item['size_l']}</td>";
                }

                if($item['size_xl'] !== null){
                     $htmlBody .= "<td style=\"padding: 10px; font-size: 14px; color: #555; text-align: center;\">{$item['size_xl']}</td>";
                }
            }


               $htmlBody .="<td style=\"padding: 10px; font-size: 14px; color: #555; text-align: right;\">RM {$item['amount']}</td>
                        </tr>
                        <tr style=\"background-color: #f8f8f8;\">
                            <td colspan=\"2\"></td>
                        </tr>";
        }

        $htmlBody .= "
                        <tr>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\">Shipping fee</td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\"></td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000; text-align: right;\">RM 5</td>
                        </tr>
                        ";


                        if($discount > 0){
                            $htmlBody .= "
                            <tr>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\">Voucher</td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\"></td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000; text-align: right;\">- RM {$discount}</td>
                            </tr>";
                        }

        $htmlBody .= "   <tr>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\">Total</td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000;text-align: left;\"></td>
                            <td style=\"padding: 10px; font-size: 14px; font-weight: bold; color: #000; text-align: right;\">RM {$total}</td>
                        </tr>
                    </table>
                </div>

                <!-- Payment Details -->
                <div style=\"margin-top: 20px; text-align: left;\">
                    <p style=\"font-size: 14px; color: #555; margin: 5px 5;\">Transaction ID: <b>{$transaction_id}</b></p>
                    <p style=\"font-size: 14px; color: #555; margin: 5px 5;\">Payment Method: <b>{$payment_method}</b></p>
                    <p style=\"font-size: 14px; color: #555; margin: 5px 5;\">Date: <b>{$date}</b></p>
                </div>

                <!-- Footer section -->
                <div style=\"border-top: 1px solid #ccc; margin-top: 40px; padding-top: 10px;\">
                    <p style=\"font-size: 13px; color: #555; margin: 5px 0;\">If you have any questions about your purchase, contact us at <a href=\"mailto:merchsystem@gmail.com\" style=\"color: #007bff;\">merchsystem@gmail.com</a>.</p>
                    <p style=\"font-size: 13px; color: #555; margin: 5px 0;\">This is an automated message; please do not reply.</p>
                </div>

            </div>
        </div>";




        $mail->Body = $htmlBody;

        $mail->send();
    } catch (Exception $e) {
        error_log("Failed to send email: " . $mail->ErrorInfo . "\n", 3, "../var/log/app_debug.log");
    }
}

