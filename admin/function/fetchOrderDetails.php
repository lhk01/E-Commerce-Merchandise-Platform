<?php
include("../database/database.php"); 
// Get the order_id from the GET request
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if ($order_id) {
    // Prepare and execute SQL query to fetch order details with customer name
    $sql = "
       SELECT 
        o.*, 
        u.username AS customer_name,
        u.email_address AS contact_number
    FROM 
        orders o 
    JOIN 
        users u ON o.user_id = u.id  
    WHERE 
        o.order_id = ?
    ";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $order_id); // Assuming order_id is an integer
    if (!$stmt->execute()) {
        die(json_encode(['error' => 'Query execution failed: ' . $stmt->error]));
    }
    
    $result = $stmt->get_result();

    // Check if order exists
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();

        // Prepare to fetch product details for the order using JOIN
        $sql_products = "
            SELECT 
                o.product_id,
                p.productName,
                p.product_Image,
                o.price,
                o.quantity,
                o.size_M,
                o.size_L,
                o.size_XL
            FROM 
                order_items o
            JOIN 
                products p ON o.product_id = p.product_id
            WHERE 
                o.order_id = ?
        ";

        $stmt_products = $mysqli->prepare($sql_products);
        $stmt_products->bind_param("i", $order_id); // Assuming order_id is an integer
        if (!$stmt_products->execute()) {
            die(json_encode(['error' => 'Query execution failed: ' . $stmt_products->error]));
        }

        $result_products = $stmt_products->get_result();

        // Fetch products
        $products = [];
        while ($row = $result_products->fetch_assoc()) {
            // Prepare size details
            $sizes = [];
            if (isset($row['size_M']) && $row['size_M'] > 0) {
                $sizes[] = "M: " . $row['size_M'];
            }
            if (isset($row['size_L']) && $row['size_L'] > 0) {
                $sizes[] = "L: " . $row['size_L'];
            }
            if (isset($row['size_XL']) && $row['size_XL'] > 0) {
                $sizes[] = "XL: " . $row['size_XL'];
            }

            $products[] = [
                'id' => $row['product_id'],
                'name' => $row['productName'],
                'price' => $row['price'],
                'image' => $row['product_Image'],
                'quantity' => $row['quantity'],
                'sizes' => implode(', ', $sizes) // Combine sizes into a string
            ];
        }

        // Structure the order details to send back
        $response = [
            'orderDetails' => [
                'order_id' => $order['order_id'],
                'user_id' => $order['user_id'],
                'customer_name' => $order['customer_name'],
                'shipping_address' => $order['shipping_address'],
                'contact_number' => $order['contact_number'],
                'order_status' => $order['order_status'],
                'order_date' => $order['order_date'],
                'payment_method' => $order['payment_method'],
                'products' => $products
            ]
        ];

        // Send the data back as JSON
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} else {
    echo json_encode(['error' => 'Order ID is missing']);
}

?>
