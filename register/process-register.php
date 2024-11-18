<?php

  session_start();

  include("../database/database.php");
  require_once("../function/password-validate.php");

  // Initialize a variable to check if the form is valid
  $isValid = true;
  $username = $email = $password = $confirmPassword = $user_icon = "";

  // Retrieve and sanitize the user input
  $username = filter_input(INPUT_POST,'username');
  $email = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
  $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);  
  $confirmPassword = filter_input(INPUT_POST, 'password_confirmation', FILTER_SANITIZE_SPECIAL_CHARS); 
   
  // Check if the username is empty
  if(empty($username)){
    $isValid = false;  
  }

  //Check if the email is empty
  if (empty($email)) {
    $isValid = false; 
  }

  // validate the password again
  $isValid = passwordValidate($password, $confirmPassword);

  if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    // Get file details
    $fileTmpPath = $_FILES['profile_image']['tmp_name'];
    $fileName = $_FILES['profile_image']['name'];
    $fileType = $_FILES['profile_image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // Allowed file extensions
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    // Check if the uploaded file is an image and has a valid extension
    if (!in_array($fileExtension, $allowedExtensions)) {
       $isValid = false;
    }

    // If no errors, move the file to the "picture" directory
    if ($isValid) {
      // Generate a unique name for the file
      $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
      $uploadDir = '../upload/user_icon/';
      $destination = $uploadDir . $newFileName;

      // Moves the user icon to the user icons folder, if successful then stores the user icon to a variable ($user_icon)
      if (move_uploaded_file($fileTmpPath, $destination)) {
          $user_icon = $destination;
      } else {
          $isValid = false;
      }
    }
  } else {
      $isValid = false;
  }


  // If any validation fails, set an error message and redirect to the registration page
  if(!$isValid){
    $_SESSION['error'] = "We apologize, but an error has occurred. Please contact us for further assistance.";
    redirect('register.php');
  }

  try {
    // Prepare an SQL query to check if the email already exists in the database
    $sql = 'SELECT * FROM users WHERE email_address = ?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // If the email already exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // If the email is already verified, redirect to the login page with an error message
        if ($row['is_verified'] == 1) {
          $_SESSION['message'] = "Your email is already registered in our system. Kindly log in to proceed.";
          redirect('../login/login.php');

        }else{
          
          // If the user is registered but has not verified their account, update the user details and redirect to the opt page
          $update_sql = "UPDATE users SET username = ?, account_image = ?, password_hash = ? WHERE email_address = ?";
          $update_stmt = $mysqli->prepare($update_sql);
          $update_stmt->bind_param("ssss", $username,$user_icon, $password_hash, $email);

          if ($update_stmt->execute()) {
            // store the email in session storage
            $_SESSION['email_reg'] = $email;
            redirect('otp-verify.php');
          }
        }

    }else{
      // new account register
      $otp = null;  
      $otp_generated_at = null;
      $sql = "INSERT INTO users (username, account_image,email_address, password_hash, otp, otp_generated_at, is_verified)
              VALUES (?, ? ,?, ?, ?, ?, 0)";
      $stmt = $mysqli->prepare($sql);
      $stmt->bind_param("ssssss", $username,$user_icon, $email, $password_hash, $otp, $otp_generated_at);

      if ($stmt->execute()) {
        $_SESSION['email_reg'] = $email;
        redirect('otp-verify.php');
      }
    }
  }catch (Exception $e) {
     // Log the error if any exception occurs and redirect to the registration page with an error message
    error_log("Registration Error: " . $e->getMessage() . "\n", 3, "../var/log/register_error.log");
    $_SESSION['error'] = "We apologize, but an error has occurred. Please contact us for further assistance.";
    redirect('register.php');
  }