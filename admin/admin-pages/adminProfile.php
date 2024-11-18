<?php
// Start the session
session_start();
include ("header.php");

// Database credentials
include("../database/database.php");

if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

$adminId = $_SESSION['admin_id']; 

// Fetch only the profile picture
$sql = "SELECT profile_picture FROM admins WHERE admin_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$stmt->bind_result($profilePicture);
$stmt->fetch();
$stmt->close();

// Close the database connection

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="../css/adminProfile.css">
    <script src="../javascript/adminProfile.js"></script>
</head>
<body>

    
    <div class="main-content">
        <div class="main-content2">
            <section class="profile-section">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($profilePicture); ?>" alt="Admin Icon" class="admin-icon" id="profilePicturePreview">
                
                <form id="profileForm" action="../function/updateProfile.php" enctype="multipart/form-data" method="POST">
                    <label for="profilePicture">Profile Picture:</label>
                    <input type="file" id="profilePicture" name="profilePicture" accept="image/*" onchange="previewProfilePicture(event)">
                    
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required minlength="2" placeholder="Enter your name">
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                    
                    <label for="contact">Contact Number:</label>
                    <input type="tel" id="contact" name="contact" required pattern="[0-9]{10}" placeholder="Enter your contact number">
                    
                    <button type="submit">Update Profile</button>
                </form>
               
            </section>
        </div>
    </div>

    <script src="../javascript/adminProfile.js"></script>
</body>
</html>
