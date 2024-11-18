<?php
session_start();
include("../database/database.php");
include("../header-footer/header.php");

// Assuming the user is logged in and you have the user_id available
$user_id = $_SESSION['user_id']; // Or any other method to get the logged-in user's ID

// Set default sort column and order
$sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'created_at';  // Default sort by created_at
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';  // Default order is ascending

// Toggle sorting order (ASC <-> DESC)
$new_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

// Query to fetch only the refunded products for the logged-in user with sorting
$query = "
    SELECT 
        users.username, 
        products.productName, 
        products.product_Image, 
        refund_requests.status, 
        refund_requests.created_at 
    FROM refund_requests
    JOIN orders ON refund_requests.order_id = orders.order_id
    JOIN users ON orders.user_id = users.id
    JOIN refund_items ON refund_items.refund_request_id = refund_requests.refund_request_id
    JOIN order_items ON order_items.order_item_id = refund_items.order_item_id
    JOIN products ON order_items.product_id = products.product_id
    WHERE users.id = ?
    ORDER BY $sort_column $sort_order
";

// Initialize an array to store the fetched data
$refunds = [];

if ($stmt = $mysqli->prepare($query)) {
    // Bind the user_id as a parameter
    $stmt->bind_param('i', $user_id);

    // Execute the query
    $stmt->execute();

    // Bind the results to variables
    $stmt->bind_result($username, $product_name, $product_image, $refund_status, $created_at);

    // Fetch the results and store them in the array
    while ($stmt->fetch()) {
        $refunds[] = [
            'username' => $username,
            'productName' => $product_name,
            'product_image' => $product_image,
            'refund_status' => $refund_status,
            'created_at' => $created_at
        ];
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Requests</title>
    <link rel="stylesheet" href="../css/return-page.css">
</head>
<body>

    <div class="container"> 
        <h2>My Return</h2>

        <div class="return-container">
            <?php 
                if (!empty($refunds)) {
                    echo "<table border='1' cellpadding='10' cellspacing='0'>";
                    echo "<thead>";
                    echo "<tr>";

                    // Product Name Column with Arrow
                    echo "<th><a href=\"?sort_column=productName&sort_order=$new_sort_order\">Product Name ";
                    echo ($sort_column == 'productName') ? ($sort_order == 'ASC' ? '↓' : '↑') : '';
                    echo "</a></th>";

                    // Product Image Column
                    echo "<th>Product Image</th>";

                    // Status Column with Arrow
                    echo "<th><a href=\"?sort_column=refund_status&sort_order=$new_sort_order\">Status ";
                    echo ($sort_column == 'refund_status') ? ($sort_order == 'ASC' ? '↓' : '↑') : '';
                    echo "</a></th>";

                    // Created At Column with Arrow
                    echo "<th><a href=\"?sort_column=created_at&sort_order=$new_sort_order\">Request Date ";
                    echo ($sort_column == 'created_at') ? ($sort_order == 'ASC' ? '↓' : '↑') : '';
                    echo "</a></th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";

                    foreach ($refunds as $refund) {
                        // Split product images if there are multiple images
                        $images = explode(",", $refund['product_image']);
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($refund['productName']) . "</td>";
                        echo "<td><img src='../upload/product_image/" . htmlspecialchars($images[0]) . "' alt='Product Image'/></td>";
                        echo "<td>" . htmlspecialchars($refund['refund_status']) . "</td>";
                        echo "<td>" . htmlspecialchars($refund['created_at']) . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                } else {
                    echo "<p>No refund requests found.</p>";
                }
            ?>
        </div>

    </div>

</body>
</html>
<?php include("../header-footer/footer.php");?> 