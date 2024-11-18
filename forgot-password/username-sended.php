<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Check Your Mailbox</title>

  <!-- CSS -->
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
          <p class = "message">Your username has been sent to the email address associated with your account. If the provided email address is correct, you should receive it shortly</p>
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
          onclick = "startCountdown('Send it again ', 'Resend Email','send-username.php')">
          Send it again
        </button>
        </p> 
      </div>
    </div>
  </div>
</body>
</html>