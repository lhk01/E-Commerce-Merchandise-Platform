<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>

  <!-- CSS -->
  <link rel="stylesheet" href="../css/input-container.css">
  <link rel="stylesheet" href="../css/forgot-password.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito:wght@200;400;600&display=swap" rel="stylesheet">
</head>

<body>
  <!-- return button -->
  <a href = "cant-sign-in.php">
    <button class="back-button">
      &lt;
    </button>
  </a>

   <!-- Main container for the reset password page -->
  <div class="container">
    <!-- Forgot Password container -->
    <div class = "forgot-password-container">
      <div>
        <h1>Forgot Username</h1>
      </div>

      <div>
         <!-- Form to send the email address for password reset -->
        <form action="send-username.php" method="post">
          <input type="hidden" name="action" value="sended">
          
           <!-- Input field for the user to enter their email address -->
          <div class="input-container">
            <input type="email" placeholder=" " name="email_address" id="email_address" class="input">
            <label for="email_address">Enter Email Address</label>
          </div>

          <!-- Section for displaying messages -->
          <div class = "message">
            <?php
              if(!empty($_SESSION['message'])){
                echo "<p>" . $_SESSION['message'] . "</p>";
              }
              unset($_SESSION['message']);
            ?>
          </div>

          <!-- Submit button -->
          <button type="submit" class="submit-btn">Submit</button>
       </form>
      </div>
    </div>
  </div>
</body>
</html>