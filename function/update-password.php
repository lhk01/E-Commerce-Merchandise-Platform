<?php
session_start();
include("../database/database.php");
require_once("../function/password-validate.php");

if (isset($_POST['verify_current_password'])) {
    $current_password = $_POST['current_password'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

    // Fetch current hashed password from the database
    $query = "SELECT password_hash FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($password_hash);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (password_verify($current_password, $password_hash)) {
        $_SESSION['password_verified'] = true; // Set session variable to confirm password verified
        header("Location: update-password.php?step=2");
        exit();
    } else {
        $_SESSION['password_verified'] = false;
        header("Location:../pages/my-account.php");
        exit();
    }
}

if (isset($_POST['update_password']) && $_SESSION['password_verified']) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_new_password'];
    $user_id = $_SESSION['user_id'];
    
    $isValid = passwordValidate($new_password, $confirm_password); // Validate password match and criteria

    if ($isValid) {
        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password in the database
        $update_query = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("si", $new_password_hash, $user_id);
        if ($stmt->execute()) {
            $_SESSION['password_verified'] = false; // Reset password verification session
            header("Location: my-account.php?message=Password successfully updated");
            exit();
        } else {
            echo "Error updating password.";
        }
    } else {
        echo "Passwords do not match or do not meet criteria.";
        header("Location: update-password.php?step=2");
        exit();
    }
}
?>
<?php
// Show new password form if the password is verified
if (isset($_GET['step']) && $_GET['step'] == 2 && $_SESSION['password_verified']) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Password</title>
        <link rel="stylesheet" href="update-password.css">
        <style>
          /* update-password.css */

body {
    font-family: Arial, sans-serif;
    background-color: #f7f7f7;
    color: #333;
    margin: 0;
    padding: 0;
}

.content-section {
    width: 100%;
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.top h3 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.top p {
    font-size: 14px;
    color: #666;
}

.bottom form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.bottom label {
    font-weight: bold;
    font-size: 16px;
}

.bottom input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.bottom input[type="file"] {
    font-size: 14px;
}

button {
    padding: 12px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

#password-requirements {
    margin-top: 10px;
}

.requirement-list {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.requirement-list li {
    display: flex;
    align-items: center;
    font-size: 14px;
}

.requirement-list li i {
    margin-right: 8px;
}

.requirement-list-2 {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.requirement-list-2 li {
    display: flex;
    align-items: center;
    font-size: 14px;
}

.requirement-list-2 li i {
    margin-right: 8px;
    color: #777;
}

#password-requirements-2 {
    margin-top: 15px;
}

#password-requirements-2 p {
    font-size: 14px;
    color: #ff0000;
}

        </style>
    </head>
    <body>
        <div id="changePassword" class="content-section">
            <div class="top">
                <h3>Change Password</h3>
                <p>To protect your account security, please enter your new password below.</p>
            </div>
            <div class="bottom">
                <form action="update-password.php" method="POST" enctype="multipart/form-data">
                    <label>New Password:</label>
                    <input type="password" id="password" name="new_password" required>
                    <label>Confirm New Password:</label>
                    <input type="password" id="password_confirmation" name="confirm_new_password" required>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <button type="submit" name="update_password" id="submit_btn" disabled>Update Password</button>
                    <div id="password-requirements" style="display: none;">
                        <ul class="requirement-list">
                            <li><i class="fa-solid fa-circle"></i> Minimum 8 characters</li>
                            <li><i class="fa-solid fa-circle"></i> At least one number</li>
                            <li><i class="fa-solid fa-circle"></i> At least one lowercase letter</li>
                            <li><i class="fa-solid fa-circle"></i> At least one special character</li>
                            <li><i class="fa-solid fa-circle"></i> At least one uppercase letter</li>
                        </ul>
                    </div>
                    <div id="password-requirements-2" style="display: none;">
                        <p id="error"></p>
                        <ul class="requirement-list-2">
                            <li><i class="fa-solid fa-circle"></i> Passwords must match</li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>

        <script src="../javascript/validation.js" defer></script>
    </body>
    </html>
<?php
}
?>
