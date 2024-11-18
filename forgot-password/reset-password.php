<?php
include("../database/database.php");
require_once("../function/password-validate.php");
require_once("../function/function.php");

session_start();

// Check if the token is present in the URL query parameters
if (isset($_GET['token'])) {
    if (!empty($_GET['token'])) {
        $_SESSION['token'] = $_GET['token'];
    }
}

// Check if the request method is POST (form submission)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_SESSION['token'];

     // Check if the token is not empty
    if (!empty($token)) {
        try {
            // Prepare SQL and check for token
            $sql = "SELECT * FROM users WHERE reset_token_hash = ?";

            // Hash the token to match it with the hashed version in the database
            $token_hash = hash("sha256", $token);  

            // Prepare and bind the SQL statement
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $token_hash);
            $stmt->execute();

            // Get the result and fetch the user data as an associative array
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

             // Sanitize and retrieve the new password and its confirmation from the form input
            $newPassword = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $newPasswordConfirm = filter_input(INPUT_POST, 'password_confirmation', FILTER_SANITIZE_SPECIAL_CHARS);

            // Check if the user exists (valid token)
            if (!$user) {
              // Store an error message in session and log the error
              $_SESSION['message'] = "An error has occurred. Please resend the email at your earliest convenience.";
              error_log("Error: Unable to catch token or invalid token\n", 3, "../var/log/reset_password.log");
              redirect("forgot-password.php");
            } else {
                // Check if the token has expired
                date_default_timezone_set("Asia/Kuala_Lumpur");
               if (strtotime($user["reset_token_expires_at"]) > time() ) {
                    
                  $isValid = passwordValidate($newPassword, $newPasswordConfirm);

                  $password_hash = $user['password_hash'];

                  if (password_verify($newPassword, $password_hash)){
                    $_SESSION['message']= "Your new password cannot be the same as old password. ";
                    redirect('reset-password.php');
                  }

                    
                  // If password validation fails, show an error message and redirect
                  if (!$isValid) {
                      $_SESSION['error']= "We apologize, but an error has occurred. Please contact us for further assistance.";
                      redirect('reset-password.php');
                  } else {
                      // If valid, hash the new password
                      $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                      // Update the user's password in the database and clear the token
                      $sql = "UPDATE users SET password_hash = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?";
                      $stmt = $mysqli->prepare($sql);
                      $stmt->bind_param("si", $newPasswordHash, $user["id"]);
                      $stmt->execute();

                      unset($_SESSION['token']);
                      $_SESSION['message'] = "Your password has been reset successfully.";
                      redirect("../login/login.php");
                  }
                } else {
                  // If token is expired, throw an exception
                  throw new Exception("Token expired");
                }
            }
        } catch (Exception $e) {
             // Log the exception error and display a message to the user
            error_log("Error: " . $e->getMessage() . "\n", 3, "../var/log/reset_password.log");
            $_SESSION['message'] = $e->getMessage().", Please send a new request";
            redirect('forgot-password.php');
        }
    } else {
      // If no token was found, show an error message
      $_SESSION['message'] = "We apologize, but an error has occurred. Please contact us for further assistance.";
      redirect('reset-password.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>

    <!-- css -->
    <link rel="stylesheet" href="../css/input-container.css">
    <link rel="stylesheet" href="../css/forgot-password.css">

    <!-- javascript -->
    <script src="../javascript/validation.js" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> 
     <!-- tick and circle symbol -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <!-- Forgot Password container -->
        <div class="forgot-password-container">
            <div>
                <h1>Reset Password</h1>
            </div>

            <div>
                <!-- Password Reset Form -->
              <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method='post'>
                  
                <!-- Password input field -->
                <div class="input-container">
                  <input type="password" placeholder=" " name="password" id="password" class="input">
                  <label for="password">New Password</label>
                </div>

                <!-- Password requirement block -->
                <div class="content" id="password-requirements" style="display: none;">
                  <ul class="requirement-list">
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span>At least 8 characters length</span>
                    </li>
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span>At least 1 number (0...9)</span>
                    </li>
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span>At least 1 lowercase letter (a...z)</span>
                    </li>
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span>At least 1 special symbol (!...$)</span>
                    </li>
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span>At least 1 uppercase letter (A...Z)</span>
                    </li>
                </ul>
                </div>

                <!-- Confirm Password input field -->
                <div class="input-container">
                  <input type="password" placeholder=" " name="password_confirmation" id="password_confirmation" class="input">
                  <label for="password_confirmation">Confirm New Password</label>
                </div>

                <!-- Password confirmation block -->
                <div class="content-2" id="password-requirements-2" style="display: none;">
                  <ul class="requirement-list-2">
                    <li>
                      <i class="fa-solid fa-circle"></i>
                      <span id="error">Passwords do not match</span>
                    </li>
                  </ul>
                </div>

                <!-- Display any error mesage -->
                <div class = "message">
                  <?php

                    if(!empty($_SESSION['message'])){
                      echo "<p>".$_SESSION['message']."</p>";
                    }
                    unset($_SESSION['message']);

                  ?>
                </div>

                <!-- reCAPTCHA -->
                <div class = "recaptcha"> 
                  <div class="g-recaptcha" data-sitekey="6LfAl0UqAAAAAJG3c7wwQKGFkU7eCUhWvkMGnHOL"
                    data-callback="enableSubmitbtn">
                  </div>
                </div>

                <!-- Submit button -->
                <button type="submit" class="submit-btn" id ="submit_btn">Submit</button>
              </form>
            </div>
        </div>
    </div>
</body>
</html>
