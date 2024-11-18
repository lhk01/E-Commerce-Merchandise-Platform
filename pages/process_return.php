<?php

session_start();
include("../database/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $order_id = $_POST['order_id'];
    $return_items = $_POST['return_items'];  
    $refund_reason = $_POST['refund_reason'];

    // Handle file uploads
    $upload_dir = '../upload/refund_proofs/';
    $filesArray = array();

    $totalFiles = count($_FILES['proof_image']['name']);
    
    for ($i = 0; $i < $totalFiles; $i++) {
        if ($_FILES['proof_image']['error'][$i] === 0) {
            $imageName = $_FILES['proof_image']['name'][$i];
            $tmpName = $_FILES['proof_image']['tmp_name'][$i];

            // Get the file extension and create a new unique name
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            $newImageName = uniqid() . '.' . $imageExtension;

            // Define the upload path
            $uploadPath = $upload_dir . $newImageName;

            // Move the file to the upload directory
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $filesArray[] = $newImageName;  // Store the uploaded file names in the array
            } else {
                // Handle file upload failure
                $_SESSION['message'] = ["Error uploading one or more image files."];
                error_log("Error uploading one or more image files.\n", 3, "../var/log/app_errors.log");
                break;
            }
        } else {
            // Handle errors or no file uploaded
            $_SESSION['message'] = ["No image file uploaded or an error occurred."];
            error_log("No image file uploaded or an error occurred.\n", 3, "../var/log/app_errors.log");
            break;
        }
    }

    // If the images are uploaded successfully, proceed with the refund request
    if (!empty($filesArray) && !empty($order_id) && !empty($return_items) && !empty($refund_reason)){
        $proof_images = implode(',', $filesArray);  // Join all image names to store in the database

        // Insert refund request into the database
        $sql = "INSERT INTO refund_requests (order_id, refund_reason, proof_images, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $mysqli->prepare($sql);
        if ($stmt === false) {
            $_SESSION['message'] = ["Failed to prepare refund request statement."];
            error_log("Failed to prepare refund request statement: " . $mysqli->error . "\n", 3, "../var/log/app_errors.log");
            header("Location: refund-form.php?order_id=$order_id");
            exit;
        }

        $stmt->bind_param("iss", $order_id, $refund_reason, $proof_images);
        if ($stmt->execute()) {
            // Get the last inserted refund request ID
            $refund_request_id = $mysqli->insert_id;

            // Fetch the order_item_id associated with the order_id and product_id
            $fetch_item_sql = "SELECT order_item_id FROM order_items WHERE order_id = ? AND product_id = ?";
            $fetch_item_stmt = $mysqli->prepare($fetch_item_sql);
            $fetch_item_stmt->bind_param("is", $order_id, $return_items);
            $fetch_item_stmt->execute();
            $fetch_item_stmt->bind_result($order_item_id);
            $fetch_item_stmt->fetch();
            $fetch_item_stmt->close();

            // Check if an order_item_id was found
            if (!empty($order_item_id)) {
                // Insert the refund item into the refund_items table
                $item_sql = "INSERT INTO refund_items (refund_request_id, order_item_id) VALUES (?, ?)";
                $item_stmt = $mysqli->prepare($item_sql);

                $item_stmt->bind_param("ii", $refund_request_id, $order_item_id);
                $item_stmt->execute();

                // Redirect or show success message
                $_SESSION['message'] = ["Refund request submitted successfully."];
                header("Location: return-page.php");
                exit;
            } else {
                $_SESSION['message'] = ["Order item not found for the given product."];
                error_log("Order item not found for order_id: $order_id and product_id: $return_items\n", 3, "../var/log/app_errors.log");
                header("Location: refund-form.php?order_id=$order_id");
                exit;
            }

        } else {
            $_SESSION['message'] = ["Error processing refund request."];
            error_log("Error inserting refund request: " . $stmt->error . "\n", 3, "../var/log/app_errors.log");
            header("Location: refund-form.php?order_id=$order_id");
            exit;
        }
    }
}
