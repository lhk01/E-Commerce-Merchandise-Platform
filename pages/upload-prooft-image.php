<?php

session_start();
include("../header-footer/header.php");
include("../database/database.php");
require_once("../function/password-validate.php");
require_once("../function/function.php");

if (!isset($_SESSION["user_id"])) {
    redirect("../login/login.php");
    exit;
}


$error = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION["user_id"];
    // Process multiple image uploads
    $totalFiles = count($_FILES['image']['name']);
    $filesArray = array();
    
    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['image']['error'][$i] === 0) {
            $imageName = $_FILES['image']['name'][$i];
            $tmpName = $_FILES['image']['tmp_name'][$i];

            // Ensure valid image extension
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            if (!in_array($imageExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $error[] = "Invalid file type for image: $imageName";
                continue; // Skip this file
            }

            // Generate a unique name for the image
            $newImageName = uniqid() . '.' . $imageExtension;
            $uploadPath = "../upload/prooft/" . $newImageName;

            // Move the file to the upload folder
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $filesArray[] = $newImageName;
            } else {
                $_SESSION['msg'] = ["Error uploading one or more image files."];
                error_log("Error uploading image: $imageName\n", 3, "../var/log/app_errors.log");
                break;
            }
        } else {
            $_SESSION['msg'] = ["No image file uploaded or an error occurred."];
            error_log("No image file uploaded or an error occurred.\n", 3, "../var/log/app_errors.log");
            break;
        }
    }

    // Check if there are any uploaded files to save in the database
    if (!empty($filesArray)) {
        // Convert the files array to a comma-separated string
        $images = implode(",", $filesArray);
        $order_id = $_GET['order_id'];
        // Prepare and execute the database update query
        $sql = "UPDATE orders SET proof_Image = ? WHERE order_id = ?";

        try {
            $stmt = $mysqli->prepare($sql);
            // Bind parameters
            $stmt->bind_param("si", $images, $order_id);

            // Execute the query
            if ($stmt->execute()) {
                redirect("success.php");
            } else {
                throw new Exception("Error updating the order: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['msg'] = [$e->getMessage()];
            // error_log($e->getMessage() . "\n", 3, "../var/log/app_errors.log");
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proof Image</title>
    <link rel="stylesheet" href="../css/upload-prooft-image.css">
</head>
<body>
    <div class="container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h2>Upload Proof Image</h2>
            <div class="proof-image">
                <label for="input-file" id="drop-area">
                    <input type="file" accept="image/*" id="input-file" name="image[]" hidden required multiple>
                    <div id="img-view">
                        <img src="../picture/upload.png" alt="Upload Icon" class="upload-pic">
                        <p>Drag and Drop or click here<br>to upload images</p>
                        <span>Upload multiple images</span>
                    </div>
                </label>
                <button type="submit">Submit</button>
            </div>
        </form>

        <?php
        // Display error or success messages
        if (isset($_SESSION['msg'])) {
            foreach ($_SESSION['msg'] as $message) {
                echo "<p class='error'>$message</p>";
            }
            unset($_SESSION['msg']);
        }
        ?>
    </div>
    <script src="../javascript/upload-image.js"></script>
</body>
</html>
<?php include("../header-footer/footer.php");?> 
