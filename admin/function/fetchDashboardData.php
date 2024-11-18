<?php

session_start();

include("../database/database.php");

// Fetch dashboard data
$sql = "
    SELECT 
        total_price, 
        order_id, 
        user_id, 
        order_date
    FROM orders
";

$result = $mysqli->query($sql);

// Error handling for the query
if (!$result) {
    die("Query failed: " . $mysqli->error);
}

// Initialize variables for calculations
$totalSales = 0;
$totalOrders = 0;
$totalCustomers = 0;
$currentMonth = date('m');
$currentYear = date('Y');

// Filter data for the current month and calculate values
while ($row = $result->fetch_assoc()) {
    $orderDate = strtotime($row['order_date']);
    
    // Check if the order date falls within the current month and year
    if (date('m', $orderDate) == $currentMonth && date('Y', $orderDate) == $currentYear) {
        $totalSales += $row['total_price'];
        $totalOrders++;
    }
}

// Calculate total customers by counting users in the 'users' table
$totalCustomersResult = $mysqli->query("SELECT COUNT(*) AS count FROM users");
if ($totalCustomersResult) {
    $totalCustomersRow = $totalCustomersResult->fetch_assoc();
    $totalCustomers = $totalCustomersRow['count'];
}

// Calculate average order value
$averageOrder = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

// Prepare data for JSON response
$data = [
    'totalSales' => number_format($totalSales, 2),
    'totalOrders' => $totalOrders,
    'totalCustomers' => $totalCustomers,
    'averageOrder' => number_format($averageOrder, 2),
];

// Fetch recent orders for the current month
 $recentOrdersSql = "
        SELECT 
            orders.order_id, 
            users.username, 
            orders.order_date, 
            orders.total_price, 
            orders.order_status 
        FROM 
            orders 
        JOIN 
            users ON orders.user_id = users.id
    ";

$recentOrdersResult = $mysqli->query($recentOrdersSql);

$recentOrders = [];
if ($recentOrdersResult && $recentOrdersResult->num_rows > 0) {
    while ($row = $recentOrdersResult->fetch_assoc()) {
        $orderDate = strtotime($row['order_date']);
        
        // Check if the order date falls within the current month and year
        if (date('m', $orderDate) == $currentMonth && date('Y', $orderDate) == $currentYear) {
            $recentOrders[] = $row;
        }
    }
}

// Add recent orders to the response data
$data['recentOrders'] = $recentOrders;

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($data);

?>
