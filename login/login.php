<?php
  session_start();
  require_once("../function/function.php");
  
  if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])) {
        redirect("../pages/homepage.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../css/login.css">

  <!-- javascript -->
  <script src="../javascript/scripts.js"></script>
  <script src="../javascript/login.js"defer></script>

  
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>


  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito:wght@200;400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome for the icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
  <div class="container">
    <div class="login-container">
      <div class="loading" id="loading" style="display: none;">
        <i class="fas fa-spinner fa-spin"></i> Loading...
      </div>
      <div class="login-form">
        <h3>Login</h3>

        <div class="user-input">
          <form id="login-form">
            <div class="input-container">
              <input type="text" placeholder=" " name="email_or_username" id="email_or_username" class="input">
              <label for="email_or_username">Email or Username</label>
            </div>

            <div class="input-container">
              <input type="password" placeholder=" " name="password" id="password" class="input">
              <label for="password">Password</label>
              <!-- Password Toggle Icon -->
              <button type="button" class="toggle-password" onclick="togglePassword()">
                <i class="fas fa-eye"></i> <!-- Font Awesome Eye Icon -->
              </button>
            </div>

            <!-- Recaptcha -->
            <div class="recaptcha"> 
              <div class="g-recaptcha" data-sitekey="6LfAl0UqAAAAAJG3c7wwQKGFkU7eCUhWvkMGnHOL" data-callback="enableSubmitbtn"></div>
            </div>
          </form>

          <!-- Error message display -->
          <div id="error-message" class = "error-message"></div>
          <!-- Display the error message (if any) -->
          <div class = "message">
            <?php 
            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
              echo "<p>".$_SESSION['message']."</p>";
              unset($_SESSION['message']);
            }

            ?>
          </div>

          <!-- Login button (outside the form) -->
          <div class="submit">
            <button type="button" class="submit-btn" id="submit_btn">Login</button>
          </div>

           <!-- login register container -->
            <div class = "forgotAndSignup-container">
              <div class = "forgot-container">
                <a href="../forgot-password/cant-sign-in.php" class="forgot_password">Can't Sign In?</a>
              </div>
              <div>
                <a  href="../register/register.php" class="register">Create an account</a>
              </div>
            </div>

        </div>
      </div>
    </div>
  </div>

  <script>
    // Function to toggle the visibility of the password
    
  </script>
</body>
</html>
