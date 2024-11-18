<?php
// Start session management
session_start();
include("../database/database.php"); 






// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error); // Show error message if the connection fails
}

// Fetch recent orders (limit to 10 latest orders)
$ordersListSql = "
    SELECT 
        o.order_id AS id, 
        u.username AS customer,  -- Assuming customer is a user, we use 'username' from the 'users' table
        o.order_date AS date, 
        o.total_price AS total, 
        o.order_status AS status
    FROM orders o
    JOIN users u ON o.user_id = u.id  -- Join with 'users' to get usernames (customer names)
    ORDER BY o.order_date DESC;
";


$ordersListResult = $mysqli->query($ordersListSql);

// Check if the query executed successfully
if (!$ordersListResult) {
    die("Query failed: " . $mysqli->error);
}

$ordersList = [];
if ($ordersListResult->num_rows > 0) {
    while ($row = $ordersListResult->fetch_assoc()) {
        $ordersList[] = $row;
    }
}

// Prepare data to return
$data = [
    'ordersList' => $ordersList
];

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);

?>
