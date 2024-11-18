<?php

session_start();

include("../database/database.php");


// Fetch sales data
$salesSql = "
    SELECT 
        DATE(order_date) AS date, 
        SUM(total_price) AS totalSales 
    FROM orders 
    GROUP BY DATE(order_date) 
    ORDER BY DATE(order_date)
";

$salesResult = $mysqli->query($salesSql);

$salesData = [
    'labels' => [],
    'values' => []
];

// Get the current month and year
$currentMonth = date('m');
$currentYear = date('Y');

if ($salesResult && $salesResult->num_rows > 0) {
    while ($row = $salesResult->fetch_assoc()) {
        // Convert the date string to a timestamp for comparison
        $orderDate = strtotime($row['date']);

        // Check if the order date falls within the current month and year
        if (date('m', $orderDate) == $currentMonth && date('Y', $orderDate) == $currentYear) {
            $salesData['labels'][] = $row['date'];
            $salesData['values'][] = $row['totalSales'];
        }
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($salesData);


?>
