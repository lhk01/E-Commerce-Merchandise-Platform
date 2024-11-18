<?php
session_start();
include("../database/database.php");
include("../phpMailer/mailer.php");
require_once("../function/function.php");

// Check if 'email_address' is set from the form submission
if (isset($_POST['email_address'])) {
  // If email address is not empty, sanitize and store it in session
    if (!empty($_POST['email_address'])) {
        $_SESSION['email_res'] = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
    } else {
       // If email is empty, set an error message and redirect back to the forgot-password page
      $_SESSION["message"] = "Email is Empty. Please Try again";
      redirect("forgot-password.php");
    }
}

// Handle the POST request when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the action is 'sended', meaning the form to reset password was submitted
    if (isset($_POST['action']) && $_POST['action'] === 'sended') {
        $email = $_SESSION['email_res']; // Get email from session
        $title = "Reset-password"; 
        $time = "Please reset your password within 5 minutes.";

        try {
          // Prepare the SQL statement
          $sql = "SELECT * FROM users WHERE email_address = ? AND is_verified = 1";
          $stmt = $mysqli->prepare($sql);
          
          if (!$stmt) {
            throw new Exception("Statement preparation failed: " . $mysqli->error);
          }
          
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();
          
          // If the email exists in the database
          if ($result->num_rows > 0) {
            // Generate a random token for resetting the password
            $token = bin2hex(random_bytes(16));

             // Hash the token for security
            $token_hash = hash("sha256", $token);

            date_default_timezone_set("Asia/Kuala_Lumpur");

            // Set an expiry time for the token (5 minutes from the current time)
            $expiry = date("Y-m-d H:i:s", time() + 60 * 5);

            // Update the user record with the new reset token and expiry time
            $sql = "UPDATE users   
                    SET reset_token_hash = ?, reset_token_expires_at = ?
                    WHERE email_address = ?";
            $stmt = $mysqli->prepare($sql);

            $stmt->bind_param("sss", $token_hash, $expiry, $email);
            $stmt->execute();
            
             // Send the reset email to the user with the token link
            mailer($email, $title, "Password Reset", 
            "To Reset Password <br><br>Click <a href='http://localhost/merchsystem-final-draft/forgot-password/reset-password.php?token=$token'>here</a> 
            to reset your password.", $time);

            redirect("../forgot-password/send-password-reset.php");

          } 
          
        } catch (Exception $e) {
            // Catch any exceptions that occur and log them, then redirect with an error message
            error_log("Error: " . $e->getMessage()."\n", 3, "../var/log/send_password_reset.log");
            $_SESSION["message"] = "We apologize, but an error has occurred. Please contact us for further assistance.";
            redirect("forgot-password.php");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check Your Mailbox</title>

  <!-- css -->
  <link rel="stylesheet" href="../css/input-container.css">
  <link rel="stylesheet" href="../css/forgot-password.css">

  <!-- javascript -->
  <script src="../javascript/countdown.js"></script>

</head>
<body>
   <!-- Main container for the page content -->
  <div class = "container">
    <div class = "comfirmation-container">
      <!-- information container -->
      <div>
        <!-- Display logo -->
        <div class = "logo">
          <img src="../picture/logo.png" style="width: 80px;" alt="Company Logo">
        </div>
        
        <div class = "message-container">
          <p class = "header">Please check your inbox for further details<p>
          <p class = "message">A password reset email has been sent to the address associated with your account. If the provided email address is correct, you should receive it shortly.</p>
        </div>

         <!-- Button to go back to the login page -->
        <div>
          <button class="button" id="back-to-login-page" onclick="window.location.href='../login/login.php';">Sure</button>
        </div>
      </div>

       <!-- Resend button in case the user did not receive the email -->
      <div>
        <p class = 'resend'> Didn't get e-mail? 
          <button type="submit" id="send-email" class="otp-btn" 
          onclick = "startCountdown('Send it again ', 'Resend Email','send-password-reset.php')">
          Send it again
        </button>
        </p> 
      </div>
    </div>
  </div>
</body>
</html>