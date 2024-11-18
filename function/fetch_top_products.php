<?php
session_start();

include("../database/database.php"); // Include the database connection

// Query to fetch all order items
$sql = "SELECT * FROM order_items";
$result = $mysqli->query($sql);

// Initialize an array to store the total quantity per product
$product_quantities = array();

// Process each order item and sum up the quantities for each product
while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $quantity = $row['quantity'];

    // Add up the quantities of different sizes (assuming NULL values default to 0)
    $size_m = isset($row['size_M']) ? $row['size_M'] : 0;
    $size_l = isset($row['size_L']) ? $row['size_L'] : 0;
    $size_xl = isset($row['size_XL']) ? $row['size_XL'] : 0;

    // Total quantity for the product considering all sizes
    $total_quantity = $quantity + $size_m + $size_l + $size_xl;
    
    // If the product already exists in the array, add the quantity, otherwise set it
    if (isset($product_quantities[$product_id])) {
        $product_quantities[$product_id] += $total_quantity;
    } else {
        $product_quantities[$product_id] = $total_quantity;
    }
}

// Sort the product quantities in descending order to get the top 5 products
arsort($product_quantities);
$top_5_products = array_slice($product_quantities, 0, 5, true);

// Now, we need to fetch product details from the products table based on the top 5 product IDs
$top_5_product_ids = array_keys($top_5_products);

// Prepare the SQL query to get product details if we have product IDs
$top_5_product_details = array();

if (count($top_5_product_ids) > 0) {
    $placeholders = implode(',', array_fill(0, count($top_5_product_ids), '?'));
    $sql_products = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $stmt = $mysqli->prepare($sql_products);

    // Dynamically bind the product IDs to the statement
    $stmt->bind_param(str_repeat('s', count($top_5_product_ids)), ...$top_5_product_ids);

    // Execute the query
    $stmt->execute();
    $result_products = $stmt->get_result();

    // Store product details in an array with total sales included
    while ($product = $result_products->fetch_assoc()) {
        $product_id = $product['product_id'];
        $product['total_sales'] = $top_5_products[$product_id]; // Add total sales quantity to each product
        $top_5_product_details[$product_id] = $product;
    }

    // If fewer than 5 products, fetch random products to fill the gap
    if (count($top_5_product_details) < 5) {
        $remaining_count = 5 - count($top_5_product_details);

        // Fetch random products from the products table
        $sql_random_products = "SELECT * FROM products WHERE product_id NOT IN ($placeholders) ORDER BY RAND() LIMIT ?";
        $stmt_random = $mysqli->prepare($sql_random_products);

        // Combine all parameters into one array for bind_param
        $bind_params = array_merge($top_5_product_ids, [$remaining_count]);

        // Generate the types string dynamically
        $types = str_repeat('s', count($top_5_product_ids)) . 'i';

        // Bind parameters using call_user_func_array
        $stmt_random->bind_param($types, ...$bind_params);

        // Execute the query
        $stmt_random->execute();
        $result_random_products = $stmt_random->get_result();

        // Add random products to fill the gap
        while ($random_product = $result_random_products->fetch_assoc()) {
            $random_product['total_sales'] = 0; // Random products will have 0 total sales
            $top_5_product_details[$random_product['product_id']] = $random_product;
        }
    }
} else {
    // If no top products, load random products directly
    $sql_random_products = "SELECT * FROM products ORDER BY RAND() LIMIT 5";
    $result_random_products = $mysqli->query($sql_random_products);

    // Store random products in the array with 0 total sales
    while ($random_product = $result_random_products->fetch_assoc()) {
        $random_product['total_sales'] = 0;
        $top_5_product_details[$random_product['product_id']] = $random_product;
    }
}

// Sort $top_5_product_details by total_sales in descending order
usort($top_5_product_details, function($a, $b) {
    return $b['total_sales'] <=> $a['total_sales'];
});

// Output the $top_5_product_details array as JSON
header('Content-Type: application/json');
echo json_encode($top_5_product_details);

?>
