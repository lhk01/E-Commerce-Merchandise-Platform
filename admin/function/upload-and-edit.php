<?php
session_start();
include("../database/database.php");
require_once("../function/password-validate.php");
require_once("../function/function.php");

$error = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Sanitize and validate inputs
    $stock = null;
    $selection = false;
    $sizeM = $sizeL = $sizeXL = null;
    $isEmpty = false;
    
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_STRING);
    $productName = filter_input(INPUT_POST, 'product-name', FILTER_SANITIZE_STRING);
    $productPrice = filter_input(INPUT_POST, 'product-price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = filter_input(INPUT_POST, 'product-description', FILTER_SANITIZE_STRING); 
    $categories = filter_input(INPUT_POST, 'dropdown', FILTER_SANITIZE_STRING);

    if($categories === 'Apparel'){
        $sizeM = filter_input(INPUT_POST, 'size-m', FILTER_SANITIZE_NUMBER_INT);
        $sizeL = filter_input(INPUT_POST, 'size-l', FILTER_SANITIZE_NUMBER_INT);
        $sizeXL = filter_input(INPUT_POST, 'size-xl', FILTER_SANITIZE_NUMBER_INT);
        }else{
        $stock = filter_input(INPUT_POST, 'stock', FILTER_SANITIZE_NUMBER_INT);
        }

        $error = checkIFEmpty($productName, $productPrice, $description, $categories, $stock, $sizeM,
                            $sizeL, $sizeXL);

    if(!empty($error)){
        $_SESSION['msg'] = $error;
        redirect("../admin-pages/edit-product.php");
    }

    // Process multiple image uploads
    $totalFiles = count($_FILES['image']['name']);
    $filesArray = array();
    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['image']['error'][$i] === 0) {
            $imageName = $_FILES['image']['name'][$i];
            $tmpName = $_FILES['image']['tmp_name'][$i];

            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $newImageName = uniqid() . '.' . $imageExtension;

            $uploadPath = "../../upload/product_image/" . $newImageName;
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $filesArray[] = $newImageName;
            } else {
                $_SESSION['msg'] = ["Error uploading one or more image files."];
                error_log("Error uploading one or more image files.\n", 3, "../var/log/app_errors.log");
                break;
            }
        }
    }

        if (isset($_POST['submit'])){
            if (!empty($filesArray)) {
        // Convert files array to a comma-separated string to store in the database
        $images = implode(",", $filesArray);

        // Insert product details into the database
        $sql = "INSERT INTO products 
                (productName, categories, price, product_Image, stock, size_M, size_L, size_XL, product_description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }

            // Bind parameters
            $stmt->bind_param("ssdsiiiis", $productName, $categories, $productPrice, $images, $stock, $sizeM, $sizeL, $sizeXL, $description);

            // Execute
            if ($stmt->execute()) {
                $_SESSION['message'] = ["Product and images uploaded successfully!"];
                redirect("../admin-pages/adminProduct.php");
            } else {
                throw new Exception("Error adding product: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['message'] = [$e->getMessage()];
            error_log($e->getMessage() . "\n", 3, "../var/log/app_errors.log");
        }
        
    } else {
        $_SESSION['msg'] = ["No image file uploaded or an error occurred."];
        error_log("No image file uploaded or an error occurred.\n", 3, "../var/log/app_errors.log");
    }


    }

       if (isset($_POST['update'])){

        if (!empty($filesArray)) {
        // Convert files array to a comma-separated string to store in the database
        $images = implode(",", $filesArray);

        // Update product details in the database
        $sql = "UPDATE products 
                SET productName = ?, categories = ?, price = ?, product_Image = ?, stock = ?, size_M = ?, size_L = ?, size_XL = ?, product_description = ? 
                WHERE product_id = ?"; 

        try {
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }

            // Bind parameters including the 'product_id' at the end
            $stmt->bind_param("ssdsiiiiss", $productName, $categories, $productPrice, $images, $stock, $sizeM, $sizeL, $sizeXL, $description, $productId);

            // Execute
            if ($stmt->execute()) {
                $_SESSION['message'] = ["Product and images updated successfully!"];
                redirect("../admin-pages/edit-product.php");
            } else {
                throw new Exception("Error updating product: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['message'] = [$e->getMessage()];
            error_log($e->getMessage() . "\n", 3, "../var/log/app_errors.log");
        }
    }else{
         // Update product details in the database
        $sql = "UPDATE products 
                SET productName = ?, categories = ?, price = ?, stock = ?, size_M = ?, size_L = ?, size_XL = ?, product_description = ? 
                WHERE product_id = ?"; 

        try {
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $mysqli->error);
            }

            // Bind parameters including the 'product_id' at the end
            $stmt->bind_param("ssdiiiiss", $productName, $categories, $productPrice, $stock, $sizeM, $sizeL, $sizeXL, $description, $productId);

            // Execute
            if ($stmt->execute()) {
                $_SESSION['message'] = ["Product and images updated successfully!"];
                redirect("../admin-pages/edit-product.php");
            } else {
                throw new Exception("Error updating product: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['message'] = [$e->getMessage()];
            error_log($e->getMessage() . "\n", 3, "../var/log/app_errors.log");
        }
    }
    }
    
}