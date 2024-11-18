<?php
// Database configuration
include("../database/database.php");

// Handle different request methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch customer data
    $sql = "
        SELECT 
            id, 
            username, 
            email_address AS email, 
            is_verified
        FROM users
        ";

    $result = $mysqli->query($sql);

    $customers = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row; // Collect data into an array
        }
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($customers);

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Handle deletion of a customer
    parse_str(file_get_contents("php://input"), $data); // Parse the input for DELETE request
    $customerId = $data['id'] ?? null; // Get customer ID

    if ($customerId) {
        // Prepare and execute the delete statement
        $stmt = $mysqli->prepare("DELETE FROM customers WHERE user_id = ?");
        $stmt->bind_param("i", $customerId); // "i" specifies the variable type is integer
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Deletion successful
            http_response_code(204); // No Content
        } else {
            // Deletion failed (ID may not exist)
            http_response_code(404); // Not Found
            echo json_encode(["message" => "Customer not found."]);
        }

    } else {
        // Customer ID not provided
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Customer ID is required."]);
    }
} else {
    // Method not allowed
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Only GET and DELETE methods are allowed."]);
}


?>
