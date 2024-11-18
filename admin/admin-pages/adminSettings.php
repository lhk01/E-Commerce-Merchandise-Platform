<?php
// Start the session
session_start();

include ("header.php");

if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

// Database credentials
include("../database/database.php");

$adminId = $_SESSION['admin_id']; 

// Fetch only the profile picture
$sql = "SELECT name, profile_picture FROM admins WHERE admin_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$stmt->bind_result($name,$profilePicture);
$stmt->fetch();
$stmt->close();

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../css/adminSettings.css">
</head>
<body>

    
    <div class="main-content">
        
        <div class="settingsList">
        <div class="admin-header">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($profilePicture); ?>" alt="Admin Icon" class="admin-icon" id="profilePicturePreview">
            <h1><?php echo htmlspecialchars($name); ?></h1>
        </div>


            <div class="nav-section">
                <h2>Account Management</h2>
                <ul>
                    <li><a href="adminProfile.php"> Profile</a></li>
                </ul>
            </div>

            <div class="nav-section">
                <h2>About</h2>
                <ul>
                    <li><a href="adminFAQ.php">FAQ</a></li>
                    <li><a href="adminT&C.php">Terms and Conditions</a></li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
