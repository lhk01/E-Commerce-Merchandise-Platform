<?php
    session_start();
    include("../database/database.php");
     include("../header-footer/header.php");

    // Check if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login/login.php");
        exit;
    }

    // Get order ID and user ID
    $order_id = $_GET['order_id'];
    $user_id = $_SESSION["user_id"];

    // Fetch order details
    try {
        // Fetch products in the order
        $sql = "SELECT order_items.*, products.productName, products.product_image, products.price 
                FROM order_items 
                JOIN products ON order_items.product_id = products.product_id 
                WHERE order_items.order_id = ?";
        $stmt = $mysqli->prepare($sql); 
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch basic order information
        $order_sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
        $order_stmt = $mysqli->prepare($order_sql);
        $order_stmt->bind_param("ii", $order_id, $user_id);
        $order_stmt->execute();
        $order_info = $order_stmt->get_result()->fetch_assoc();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../css/order-details.css">
</head>
<body>
    
    <div class="container">
        <a href = "my-order.php">
            <button class = "back-btn">Back</button>
        </a>
        <div class="order-details-container">
            
            <div class="title">
                <h3>Order Details</h3>
                <p>Order Id: <?php echo $order_id; ?></p>
                <p>Order Date: <?php echo $order_info['order_date']; ?></p>
                <p>Total Price: RM<?php echo $order_info['total_price']; ?></p>
                <P>Order Status: <?php echo $order_info['order_status'] ?></P>
                <P>Payment Method: <?php echo $order_info['payment_method'] ?></P>
            </div>
            <div class="order-items">
                
                <?php
                $payment_pending = false;
                $cancel = false;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $images = explode(",", $row['product_image']);
                        $firstImage = trim($images[0]);
                        $quantity = 0;
                        $sizeQuantities = [];
                        $number = 0;
                        $sizes = [
                            'M' => $row["size_M"],
                            'L' => $row["size_L"],
                            'XL' => $row["size_XL"]
                        ];

                        echo '<div class="container-1">';

                        // Image container
                        echo '<div class="img">';
                        echo '<img src="../upload/product_image/' . $firstImage . '" alt="' . $row['productName'] . '"<br>';
                        echo '</div>';

                        // Product details container
                        echo '<div class="product-details">';
                        echo '<p>' . $row["productName"] . '</p>';
                        echo '<div class="quantity-container">';

                        // Display the quantity
                        foreach ($sizes as $size => $quantity) {
                            if (!empty($quantity)) {
                                $sizeQuantities[] = "{$size}: {$quantity}";
                                $number = $quantity;
                            }
                        }

                        if (!empty($sizeQuantities)){
                            echo '<div class="quantity-display">';
                            echo implode(", ", $sizeQuantities);
                            echo '</div>';
                        }else{
                            echo '<div class="quantity-display">';
                            echo "Quantity: " . $row["quantity"];
                            echo '</div>';
                        }

                        echo '</div>'; // End of quantity-container
                        echo '</div>'; // End of product-details container
                        
                        if (!empty($sizeQuantities)){
                            echo '<div class="container-2">';
                            echo '<div class="price">';
                            echo "RM" . $row["price"] * $number;
                            echo '</div>';
                        }else{
                            // Price and delete container
                            echo '<div class="container-2">';
                            echo '<div class="price">';
                            echo "RM" . $row["price"] * $row["quantity"];
                            echo '</div>';
                        }

                        if ($order_info['order_status'] === "Delivered") {
                            // Check if the user has already reviewed this product
                            $review_sql = "SELECT * FROM reviews WHERE user_id = ? AND product_id = ?";
                            $review_stmt = $mysqli->prepare($review_sql);
                            $review_stmt->bind_param("is", $user_id, $row["product_id"]);  // 'i' for integer (user_id), 's' for string (product_id)
                            $review_stmt->execute();
                            $review_result = $review_stmt->get_result();

                            // Fetch the order_item_id associated with this order and product
                            $fetch_item_sql = "SELECT order_item_id FROM order_items WHERE order_id = ? AND product_id = ?";
                            $fetch_item_stmt = $mysqli->prepare($fetch_item_sql);
                            $fetch_item_stmt->bind_param("is", $order_id, $row["product_id"]);
                            $fetch_item_stmt->execute();
                            $fetch_item_stmt->bind_result($order_item_id);
                            $fetch_item_stmt->fetch();
                            $fetch_item_stmt->close();

                            // Check if the user has already submitted a refund request for this order item
                            $check_refund_sql = "SELECT * FROM refund_items WHERE order_item_id = ?";
                            $check_refund_stmt = $mysqli->prepare($check_refund_sql);
                            $check_refund_stmt->bind_param("i", $order_item_id);
                            $check_refund_stmt->execute();
                            $refund_exists = $check_refund_stmt->get_result()->num_rows > 0;
                            $check_refund_stmt->close();
                            

                            // Display review button or "already reviewed" status
                            echo '<div class="review">';
                            if ($review_result->num_rows === 0) {
                                // If no review found, display the "Review" button
                                echo "<form action='review.php' method='post'>";
                                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'>";
                                echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') . "'>";
                                echo "<button type='submit'>Review</button>";
                                echo "</form>";
                            } else {
                                // If already reviewed, display "Review" button with a link
                                echo '<a href="product-page.php?id=' . $row["product_id"] . '"><button type="button">View Review</button></a>';

                            }
                            echo '</div>';

                            // Display refund button or "refund requested" status
                            echo '<div class="refund">';
                            if (!$refund_exists) {
                                // Display "Return/Refund" button if no refund request exists
                                echo "<form action='return.php' method='post'>";
                                echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($row["product_id"], ENT_QUOTES, 'UTF-8') . "'>";
                                echo "<input type='hidden' name='order_id' value='" . htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') . "'>";
                                echo "<button type='submit'>Return/Refund</button>";
                                echo "</form>";
                            } else {
                                // Indicate that a refund request has already been submitted
                                echo '<a href="return-page.php"><button type="button">View Status</button></a>';
                            }
                            echo '</div>';
                        }
                        
                        if($order_info['proof_Image'] === null){
                            $payment_pending = true;
                        }
                        
                        if($order_info['order_status'] === "Pending" || $order_info['order_status'] === "Pending Payment"){
                            $cancel =true;
                        }

                       

                        echo '</div>'; // End of container-2    
                        echo '</div>'; // End of main container
                    
                    }
                } else {
                    echo "<p>No items found in this order.</p>";
                }
                ?>
            </div>
        </div>
        <div>
                <?php
                    echo "<div class = 'button'>";
                    if($cancel){;
                        echo "<input hidden value = '$order_id' id = 'cancel_id'>";
                        echo "<button class = 'cancel'onclick='cancel_order()'>Cancel Order</button>";
                    }

                    if($payment_pending){
                        echo "<button  class= 'pay-now' onclick=\"window.location.href='reattempt-payment.php?order_id=$order_id';\">Pay Now</button>";
                    }
                    echo "</div>";
                ?>
        </div>
    </div>
    
    <script>
        function cancel_order(){
            let confirmation = confirm("ARE YOU SURE YOU WANT TO CANCEL ORDER?");
            const order_id  = document.getElementById('cancel_id').value;

            if(confirmation){
               window.location.href = `cancel-order.php?order_id=${order_id}`;
            }
        }
    </script>

</body>
</html>
<?php include("../header-footer/footer.php");?>
