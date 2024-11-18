<?php
// Database connection
include("../database/database.php");

// Query to fetch notifications from orders, refund_requests, and reviews
$sql = "
   SELECT 
    'Order' AS type, 
    order_id AS id, 
    CONCAT('New Order ', order_id, ': ', total_price, ' (Status: ', order_status, ')') AS message, 
    order_date AS date,
    NULL AS product_id  -- product_id is NULL for orders
FROM orders

UNION ALL

SELECT 
    'Refund' AS type, 
    refund_request_id AS id, 
    CONCAT('Refund requested for Order ', order_id, ': ', refund_reason, ' (Status: ', status, ')') AS message, 
    created_at AS date,
    NULL AS product_id  -- product_id is NULL for refunds
FROM refund_requests

UNION ALL

SELECT 
    'Review' AS type, 
    review_id AS id, 
    CONCAT('New review for Product ', product_id, ': ', comment, ' (Score: ', review_score, ')') AS message, 
    review_date AS date, 
    product_id  -- product_id is directly selected for reviews
FROM reviews

ORDER BY date DESC;
";

$result = $mysqli->query($sql);
?>

<?php
// Return the result set for further processing in the HTML file
if ($result->num_rows > 0) {
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
} else {
    $notifications = [];
}



?>
