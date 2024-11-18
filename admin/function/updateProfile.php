<?php
// Start the session
session_start();


include("../database/database.php");

// Use admin ID = 1 directly
$adminId = 1;
//$adminId = $_SESSION['admin_id']; 

// Validate and sanitize input data
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : null;

// Handle file upload for profile picture
$newProfilePicture = null;

if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    // Get the file data
    $fileTmpPath = $_FILES['profilePicture']['tmp_name'];
    $fileData = file_get_contents($fileTmpPath); // Read the file content
    $newProfilePicture = $fileData; // Store the image data
}

// Prepare SQL statement to update the admin profile
$sql = "UPDATE admins SET name = ?, email = ?, contact = ?" . ($newProfilePicture ? ", profile_picture = ?" : "") . " WHERE admin_id = ?";
$stmt = $mysqli->prepare($sql);

// Bind parameters
if ($newProfilePicture) {
    $stmt->bind_param("ssssi", $name, $email, $contact, $newProfilePicture, $adminId);
} else {
    $stmt->bind_param("sssi", $name, $email, $contact, $adminId);
}

// Execute the update statement
if ($stmt->execute()) {
    // Return success response
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update profile: ' . $stmt->error]);
}


?>
