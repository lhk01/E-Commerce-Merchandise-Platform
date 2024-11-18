<?php
// Database credentials
include("../database/database.php"); 
include("../../phpMailer/mailerShipped.php");
include("../../phpMailer/mailerDelivered.php");

// Read JSON input from the POST request
$data = json_decode(file_get_contents("php://input"), true);

// Check for JSON decoding errors
if ($data === null) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Validate required fields
$order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;
$order_status = isset($data['order_status']) ? $data['order_status'] : null;

if (!$order_id || !$order_status) {
    echo json_encode(['error' => 'Missing required fields: order_id or order_status']);
    exit;
}

try {
    // Prepare SQL statement to update the order status
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Failed to prepare SQL statement: ' . $mysqli->error]);
        exit;
    }

    $stmt->bind_param("si", $order_status, $order_id);

    // Execute the update statement
    if ($stmt->execute()) {
        // Check if the update affected any rows
        if ($stmt->affected_rows > 0) {
            if ($order_status === "Shipping") {
                // Retrieve user_id for the given order_id
                $user_sql = "SELECT user_id FROM orders WHERE order_id = ?";
                $user_stmt = $mysqli->prepare($user_sql);

                if ($user_stmt) {
                    $user_stmt->bind_param("i", $order_id);
                    $user_stmt->execute();
                    $user_stmt->bind_result($user_id);
                    $user_stmt->fetch();
                    $user_stmt->close();

                    // Retrieve email_address for the user_id
                    $email_sql = "SELECT email_address FROM users WHERE id = ?";
                    $email_stmt = $mysqli->prepare($email_sql);

                    if ($email_stmt) {
                        $email_stmt->bind_param("i", $user_id);
                        $email_stmt->execute();
                        $email_stmt->bind_result($email_address);
                        $email_stmt->fetch();
                        $email_stmt->close();

                        // Send email notification
                        $title = "Your Order has Shipped";
                        mailerShipped($email_address, $title, $title, $order_id);
                    } else {
                        echo json_encode(['error' => 'Failed to retrieve email: ' . $mysqli->error]);
                        exit;
                    }
                } else {
                    echo json_encode(['error' => 'Failed to retrieve user ID: ' . $mysqli->error]);
                    exit;
                }
            }

            if ($order_status === "Delivered") {
                // Retrieve user_id for the given order_id
                $user_sql = "SELECT user_id, fullname, shipping_address FROM orders WHERE order_id = ?";
                $user_stmt = $mysqli->prepare($user_sql);

                if ($user_stmt) {
                    $user_stmt->bind_param("i", $order_id);
                    $user_stmt->execute();
                    $user_stmt->store_result(); // Important: Store result to check number of rows
                    if ($user_stmt->num_rows > 0) {
                        $user_stmt->bind_result($user_id, $fullname, $shipping_address);
                        $user_stmt->fetch();
                        $user_stmt->close();

                        // Retrieve email_address for the user_id
                        $email_sql = "SELECT email_address FROM users WHERE id = ?";
                        $email_stmt = $mysqli->prepare($email_sql);

                        if ($email_stmt) {
                            $email_stmt->bind_param("i", $user_id);
                            $email_stmt->execute();
                            $email_stmt->store_result(); // Store result
                            if ($email_stmt->num_rows > 0) {
                                $email_stmt->bind_result($email_address);
                                $email_stmt->fetch();
                                $email_stmt->close();

                                // Send email notification
                                $title = "Your Order has Shipped";
                                mailerDelivered($email_address, $title, $title, $fullname, $order_id, $shipping_address);
                            } else {
                                echo json_encode(['error' => 'Email not found for user_id: ' . $user_id]);
                                exit;
                            }
                        } else {
                            echo json_encode(['error' => 'Failed to prepare email query: ' . $mysqli->error]);
                            exit;
                        }
                    } else {
                        echo json_encode(['error' => 'No order found for order_id: ' . $order_id]);
                        exit;
                    }
                } else {
                    echo json_encode(['error' => 'Failed to prepare user query: ' . $mysqli->error]);
                    exit;
                }
            }


            echo json_encode(['success' => true, 'message' => 'Order status updated successfully!']);
        } else {
            echo json_encode(['error' => 'Order not found or status unchanged']);
        }
    } else {
        echo json_encode(['error' => 'Failed to update order status: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
}

// Close the database connection
$mysqli->close();
?>
