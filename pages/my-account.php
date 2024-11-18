<?php

  session_start();
  include("../header-footer/header.php");
  include("../database/database.php");
  require_once('../function/function.php');
  require_once("../function/password-validate.php");

  if (!isset($_SESSION["user_id"]) && empty($_SESSION["user_id"])){
    redirect('../login/login.php');
  }

  try{
    $user_id = $_SESSION["user_id"];
    $sql = "SELECT id, username, account_image, email_address FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Store the result to fetch it
    $stmt->store_result();

    
    if ($stmt->num_rows > 0) {
        // Bind each column to a variable
        $stmt->bind_result($id,  $username, $account_image , $email_address);
        
        // Fetch the data
        $stmt->fetch();
    }

  }catch(Exception $e){
     echo "Error: " . $e->getMessage();
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];

    switch ($type) {
        case "profile-account":
            $username = $_POST['username'];
            $email_address = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            $isValid = true; // Flag to track validity of inputs
            $user_icon = $account_image; // Default to the original image

            if (isset($_FILES['account_image']) && $_FILES['account_image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['account_image']['tmp_name'];
                $fileName = $_FILES['account_image']['name'];
                $fileType = $_FILES['account_image']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Allowed file extensions
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                // Validate file extension
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $isValid = false;
                    $_SESSION['msg'] = "Invalid file extension. Only JPG, JPEG, PNG, and GIF are allowed.";
                    redirect("my-account.php");
                }

                // Proceed if validation passes
                if ($isValid) {
                    // Generate a unique name for the file
                    $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
                    $uploadDir = '../upload/user_icon/';

                    // Ensure the upload directory exists
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $destination = $uploadDir . $newFileName;

                    // Move uploaded file to the destination directory
                    if (move_uploaded_file($fileTmpPath, $destination)) {
                        $user_icon = $destination; // Store the uploaded file path
                    } else {
                        $isValid = false;
                    }
                }
            } elseif ($_FILES['account_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors
                $isValid = false;
                $_SESSION['msg'] ="Error uploading file. ";
                 redirect("my-account.php");
            }

            // Update user data if validation is successful
            if ($isValid) {
                try {
                    $sqlUpdate = "UPDATE users SET username = ?, email_address = ?, account_image = ? WHERE id = ?";
                    $stmtUpdate = $mysqli->prepare($sqlUpdate);
                    $stmtUpdate->bind_param("sssi", $username, $email_address, $user_icon, $user_id);
                    $stmtUpdate->execute();

                    if ($stmtUpdate->affected_rows > 0) {
                        $_SESSION['msg'] = "Profile updated successfully.";
                    } else {
                        $_SESSION['msg'] = "No changes were made to the profile.";
                    }
                } catch (Exception $e) {
                    $_SESSION['msg'] = "Error updating profile: ";
                }
            } else {
                $_SESSION['msg'] = "Profile update failed due to validation errors.";
            }
             redirect("my-account.php");

            break;

          case "password-update":
            $currentPassword = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_SPECIAL_CHARS);
            $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_SPECIAL_CHARS);
            $confirmPassword = filter_input(INPUT_POST, 'confirm_new_password', FILTER_SANITIZE_SPECIAL_CHARS);

            $isValid = true; // Initialize as true for validation

            // Get user details
            $user_id = $_SESSION["user_id"];
            $sql = "SELECT password_hash FROM users WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($passwordHash);
            $stmt->fetch();
            $stmt->close();

            // Verify current password
            if (!password_verify($currentPassword, $passwordHash)) {
                $_SESSION['msg'] =  "Current password is incorrect.";
                redirect("my-account.php");
            }

            // Check if the new password is the same as the old password
            if (password_verify($newPassword, $passwordHash)) {
                $_SESSION['msg'] =  "The new password cannot be the same as the old password.";
                redirect("my-account.php");
            }


            // Validate new password
            $isValid = passwordValidate($newPassword, $confirmPassword);

            if (!$isValid) {
                $_SESSION['msg'] =  "The password format is incorrect.";
                redirect("my-account.php");
            }
    
            // Hash and update new password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateSql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $updateStmt = $mysqli->prepare($updateSql);

            if ($updateStmt) {
                $updateStmt->bind_param("si", $newPasswordHash, $user_id);
                if ($updateStmt->execute()) {
                    $_SESSION['msg'] =  "Password updated successfully.";
                } else {
                    $_SESSION['msg'] =   "Failed to update password.";
                }
                $updateStmt->close();
            } else {
                $_SESSION['msg'] =   "Failed to prepare update statement.";
            }
          
            redirect("my-account.php");
            break;
          
          case "delete-account":

            $currentPassword = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_SPECIAL_CHARS);

            $user_id = $_SESSION["user_id"];
            $sql = "SELECT password_hash FROM users WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($passwordHash);
            $stmt->fetch();
            $stmt->close();

            // Verify current password
            if (!password_verify($currentPassword, $passwordHash)) {
              $_SESSION['msg'] =  "Current password is incorrect.";
              redirect("my-account.php");
            }

            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $user_id);

             if ($stmt->execute()) {

              session_destroy();
              redirect("../login/login.php");

            } else {
                $_SESSION['msg'] = "An error occurred while deleting your account. Please try again later.";
            }


            break;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/my-account.css">
  <script src="../javascript/content-display.js" defer></script>
  <link rel="stylesheet" href="../css/checkout.css">
  <link rel="stylesheet" href="../css/input-container.css">

  <title>Document</title>
</head>
<body>

  <div class = "acontainer">
    
    <div class = "account-container">
      
      <div class="account-left-side">
        <h1>Account Management</h1>
        <div class="icon" data-target="my-account">
          <img src="../picture/account.png" alt="My Account"><p>My Account</p>
        </div>

        <div class="icon" data-target="reset-password">
          <img src="../picture/password.png" alt="Reset Password"><p>Reset Password</p>
        </div>

        <div class="icon" data-target="deactivate-account">
          <img src="../picture/reset.png" alt="Deactivate"><p>Delete Account</p>
        </div>
      </div>

      <div class="account-right-side">
        <div id="my-account" class="content">
          <div class="my-account">
            <form id="updateForm" method="POST" enctype="multipart/form-data">
              <!-- Profile Image -->
              <img src="<?php echo $account_image; ?>" alt="Profile Image" id="currentImage" style="cursor: pointer;">
              <input hidden name = 'type' value = "profile-account">
              <input type="file" name="account_image" id="accountImageInput" style="display: none;">
              
              <!-- User Details -->
              <div class="account-details">
                  <label>Username:</label>
                  <input type="text" name="username" value="<?php echo $username; ?>">
                  <label>Email:</label>
                  <input type="text" name="email" value="<?php echo $email_address; ?>">
                  <div class = "message">
                    <?php
                      if(isset($_SESSION['msg']) && !empty($_SESSION['msg'])){
                        echo "<p>".$_SESSION['msg']."</p>";
                        unset($_SESSION['msg']);
                      }
                    ?>
                  </div>
                  <button type="submit">Save Changes</button>
              </div>
            </form>
        </div>

        </div>
        
        <form action="" method="POST">
        <div id="reset-password" class="content" style="display: none;">
          <div class="password-container">
                <input hidden name = 'type' value = "password-update">
                  <label>Current Password</label>
                  <input type="password" name="current_password" required>

                  <label>New Password</label>
                  <input type="password" name="new_password" required>

                  <label>Confirm New Password</label>
                  <input type="password" name="confirm_new_password" required>

                  <button type="submit">Change Password</button>
          </div>
          </form>
        </div>

        <form action="" method="POST">
        <div id="deactivate-account" class="content" style="display: none;">
          <div class ="account-delete">
            <input hidden name = 'type' value = "delete-account">
              <label>Delete Account</label>
              <p>Deleting your account will permanently remove your profile, data, and any 
                associated information. This action cannot be undone.
              </p>

              <label>Enter Password</label>
              <input type="password" name="current_password" required>
        
              <button>Delete</button>
          </div>
          </form>
      
        </div>
      </div>
        

  </div>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
  // Select all left-side icons
  const icons = document.querySelectorAll(".account-left-side .icon");
  // Select all right-side content sections
  const contents = document.querySelectorAll(".account-right-side .content");

  // Function to hide all content sections
  const hideAllContents = () => {
    contents.forEach(content => {
      content.style.display = "none";
    });
  };

  // Function to show a specific content section
  const showContent = (id) => {
    hideAllContents();
    const targetContent = document.getElementById(id);
    if (targetContent) {
      targetContent.style.display = "block";
    }
  };

  // Set default content to "My Account"
  showContent("my-account");

  // Add click event listeners to icons
  icons.forEach(icon => {
    icon.addEventListener("click", () => {
      const targetId = icon.getAttribute("data-target");
      showContent(targetId);
    });
  });
});

 document.getElementById('currentImage').addEventListener('click', function() {
        document.getElementById('accountImageInput').click();
    });

    document.getElementById('accountImageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('currentImage').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

  </script>

</body>
</html>
<?php include("../header-footer/footer.php");?> 



 


