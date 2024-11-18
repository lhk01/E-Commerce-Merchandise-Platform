<?php
session_start();
include("../database/database.php");


if (!isset($_SESSION["user_id"]) && empty($_SESSION["user_id"])){
    redirect('../login/login.php');
  }

$username = $_POST['username'];
$user_id =$_SESSION["user_id"]; /* Set the user's ID here, if it's stored in a session or passed in some way */;
$target_dir = "../upload/";
$image_updated = false;

if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
    // Generate a unique name for the file and save it in the uploads directory
    $target_file = $target_dir . basename($_FILES['profileImage']['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if the uploaded file is an image
    $check = getimagesize($_FILES['profileImage']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $target_file)) {
            // Update the image path in the database
            $image_updated = true;
            $sql = "UPDATE users SET account_image='$target_file' WHERE id='$user_id'";
            $mysqli->query($sql);
        }
    }
}

// Update the username
$sql = "UPDATE users SET username='$username' WHERE id='$user_id'";
$mysqli->query($sql);

if ($image_updated || $mysqli->affected_rows > 0) {
    header("Location: ../profile.php?update=success");
} else {
    echo "Failed to update profile.";
}
?>
