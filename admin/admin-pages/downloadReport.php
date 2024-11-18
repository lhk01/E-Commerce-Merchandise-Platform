<?php
include("../database/database.php");
// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="monthly_report.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add report metadata headings
fputcsv($output, ['Report Summary']);
fputcsv($output, ['Total Sales', 'Total Orders', 'Total Customers', 'Average Sales']);

// Fetch all sales data
$salesSql = "
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

$result = $mysqli->query($salesSql);

// Initialize variables for calculations
$totalSales = 0;
$totalOrders = 0;
$totalCustomers = [];
$salesData = [];

// Check if there are results and filter for the current month
if ($result->num_rows > 0) {
    $currentMonth = date('m');
    $currentYear = date('Y');

    while ($row = $result->fetch_assoc()) {
        // Get the order date and check if it falls within the current month and year
        $orderDate = strtotime($row['date']);
        if (date('m', $orderDate) == $currentMonth && date('Y', $orderDate) == $currentYear) {
            // Update total sales and order count
            $totalSales += $row['total'];
            $totalOrders++;
            $totalCustomers[$row['customer']] = true; // Unique customers
            
            // Store sales data for CSV
            $salesData[] = $row;
        }
    }

    // Calculate average sales
    $averageSales = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

    // Write summary data to the CSV
    fputcsv($output, [number_format($totalSales, 2), $totalOrders, count($totalCustomers), number_format($averageSales, 2)]);

    // Add column headings for sales data
    fputcsv($output, ['Order ID', 'Customer Name', 'Order Date', 'Total Price', 'Status']);
    
    // Write sales data to the CSV
    foreach ($salesData as $data) {
        fputcsv($output, $data);
    }
} else {
    // If no data found, write a message
    fputcsv($output, ['No data found for the current month.']);
}

// Close the output stream and the database connection
fclose($output);

