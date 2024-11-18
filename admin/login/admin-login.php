<?php

include("../database/database.php");
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = ""; // Initialize error message as an empty string

// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
  // Check if the reCAPTCHA response is set and not empty
  if (isset($_POST["g-recaptcha-response"]) && !empty($_POST["g-recaptcha-response"])) {
      
    // Secret key for reCAPTCHA
    $secretKey = "6LfAl0UqAAAAAAN-xWgbIAtqSRJ00VZ1Bq6_Ik55";

    // Make a request to Google's reCAPTCHA API to verify the response
    $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . 
                                        $secretKey . '&response=' . $_POST["g-recaptcha-response"]);
    
    // Decode the JSON response from the reCAPTCHA API
    $response = json_decode($verifyResponse);
      
    // Check if the reCAPTCHA verification was successful
    if ($response->success) {

      // Prepare an SQL statement to select the user based on email
      $sql = "SELECT * FROM admins WHERE email = ?";
      
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS); 
      
      $stmt = $mysqli->prepare($sql);
      
      if ($stmt) {
          $stmt->bind_param("s", $email);
          $stmt->execute();
          
          $result = $stmt->get_result();
          $user = $result->fetch_assoc();
          
          // Check if a user was found
          if ($user) {
              // Verify the entered password with the stored password
              $stored_password = $user["password"];
              
              // If passwords are hashed, use password_verify. Otherwise, do a direct comparison.
              if ($stored_password === $password) {
                  session_regenerate_id(true); 
                  
                  $_SESSION["admin_id"] = $user["admin_id"]; // Store the admin ID in the session
                  
                  // Redirect the user to the homepage
                  header("Location: ../admin-pages/adminHome.php");
                  exit;
                  
              } else {
                  $error_message = "Your email or password may be incorrect.";
              }
          } else {
              $error_message = "Your email or password may be incorrect.";
          }
          $stmt->close();
      } else {
          $error_message = "Database query error.";
      }

    } else {
      $error_message = "reCAPTCHA verification failed. Please try again.";
    }

  } else {
    $error_message = "Please complete the reCAPTCHA.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link rel="stylesheet" href="../css/adminLogin.css">
  <link rel="stylesheet" href="../css/input-container.css">
  <script src="../javascript/scripts.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>
  <div class="container">
    <div class="login_container">
      <div class="message">
        <h1 class="login-message">Login</h1>
      </div>

      <div>
        <!-- Login form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
          <!-- Email input field -->
          <div class="input-container">
            <input type="email" placeholder=" " name="email" id="email" class="input" required>
            <label for="email">Email</label>
          </div>

          <!-- Password input field -->
          <div class="input-container">
            <input type="password" placeholder=" " name="password" id="password" class="input" required>
            <label for="password">Password</label>
          </div>

          <!-- Display the error message (if any) -->
          <div class="message">
            <?php 
            if (!empty($error_message))
              echo "<p>$error_message</p>";
            ?>
          </div>

          <!-- Recaptcha -->
          <div class="recaptcha"> 
            <div class="g-recaptcha" data-sitekey="6LfAl0UqAAAAAJG3c7wwQKGFkU7eCUhWvkMGnHOL" data-callback="enableSubmitbtn">
            </div>
          </div>

          <!-- Login button -->
          <div class="submit">
            <button type="submit" class="submit-btn" id="submit_btn" disabled="disabled">Login</button>
          </div>

        </form>
      </div>  
      
    </div>
  </div>
  
</body>
</html>
