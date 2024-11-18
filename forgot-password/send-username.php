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
    // Check if the action is 'sended', meaning the form to get username was submitted
    if (isset($_POST['action']) && $_POST['action'] === 'sended') {
        $email = $_SESSION['email_res']; // Get email from session
        $title = "Forgot Username"; 
        $time = "";

        try {
          // Prepare the SQL statement to fetch the username (no need for is_verified check if we don't care about verification)
          $sql = "SELECT username FROM users WHERE email_address = ?";
          $stmt = $mysqli->prepare($sql);
          
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();
          
          // If the email exists in the database
          if ($result->num_rows > 0) {
            // Fetch the username from the result
            $row = $result->fetch_assoc();
            $username = $row['username'];

            // Send the username to the user's email
            mailer($email, $title, "Recover Username Request", 
            "Your username is: <b>$username</b><br><br>", $time);
          }

          redirect("../forgot-password/username-sended.php");
        } catch (Exception $e) {
            // Catch any exceptions that occur and log them, then redirect with an error message
            error_log("Error: " . $e->getMessage()."\n", 3, "../var/log/send_username.log");
            $_SESSION["message"] = "We apologize, but an error has occurred. Please contact us for further assistance.";
            redirect("forgot-username.php");
        }
    }
}


