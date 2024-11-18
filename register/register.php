<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  
  <!-- css -->
  <link rel="stylesheet" href="../css/register.css">
  <link rel="stylesheet" href="../css/input-container.css">

  <!-- recapcha -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <!-- javascript -->
  <script src="../javascript/validation.js" defer></script>

  <!-- fontsize -->
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito:wght@200;400;600&display=swap" rel="stylesheet">

  <!-- tick and circle symbol -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
  
</head>
<body>
  <!-- Return Button -->
  <a href = "../login/login.php">
    <button class="back-button">
      &lt;
    </button>
  </a>

  <!-- container -->
  <div class = "container">

    <!-- register container -->
    <div class="register-container">
      <h1>Register</h1>

      <!-- form -->
      <form action="process-register.php" method="post" enctype="multipart/form-data">

        <!-- Image upload field -->
        <div class="image-upload-container">
          <div class="image-preview" id="imagePreview">
            <img id="previewImage" src="../images/default-avatar.png" alt="Profile Image" class="preview-image">
            <span class="upload-text">Choose Profile Image</span>
          </div>
          <label for="profile_image" class="custom-upload-btn">Upload Image</label>
          <input type="file" name="profile_image" id="profile_image" accept="image/*" class="input" onchange="previewFile()">
        </div>

        <!-- Username input field -->
        <div class="input-container">
          <input type="text" placeholder=" " name="username" id="username" class="input">
          <label for="username">Username</label>        
        </div>

        <!-- Email input field -->
        <div class="input-container">
          <input type="email" placeholder=" " name="email_address" id="email_address" class="input">
          <label for="email_address">Email</label>
        </div>

        <!-- Password input field -->
        <div class="input-container">
          <input type="password" placeholder=" " name="password" id="password" class="input">
          <label for="password">Password</label>
        </div>

        <!-- Password requirement block -->
        <div class="content" id="password-requirements" style="display: none;">
          
          <!-- requirement list for password validation  -->
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
          <label for="password_confirmation">Confirm Password</label>
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
       
        <!-- Display Error message if any -->
        <div class = "error-message">
          <?php
            if (!empty($_SESSION['error'])) {
              echo "<p>{$_SESSION['error']}</p>";
            }
            unset($_SESSION['error']); 
          ?>
        </div>

        <!-- reCAPTCHA -->
        <div class = "recaptcha"> 
          <div class="g-recaptcha" data-sitekey="6LfAl0UqAAAAAJG3c7wwQKGFkU7eCUhWvkMGnHOL"
            data-callback="enableSubmitbtn">
          </div>
        </div>

        <!-- Submit button -->
        <div>
          <button type="submit" class="submit-btn" id="submit_btn" disabled="disabled">Register</button>
        </div>

      </form>
    </div>
  </div>
  
  <!-- script for upload file -->
  <script>
    function previewFile() {
  const file = document.getElementById("profile_image").files[0];
  const preview = document.getElementById("previewImage");
  const uploadText = document.querySelector(".upload-text");

  if (file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
      preview.src = e.target.result;
      preview.style.display = "block";
      uploadText.style.display = "none"; // Hide text once image is uploaded
    };
    
    reader.readAsDataURL(file);
  }
}
  </script>
</body>
</html>