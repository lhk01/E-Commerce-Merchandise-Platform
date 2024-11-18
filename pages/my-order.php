<?php
    session_start();
    include("../database/database.php");
    include("../header-footer/header.php");

    // Check if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login/login.php");
        exit();
    }

    // Get user id
    $user_id = $_SESSION["user_id"];

    // Set default sort column and order
    $sort_column = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'order_id';  // Default sort by order_id
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';  // Default order is ascending

    // Toggle sorting order (ASC <-> DESC)
    $new_sort_order = ($sort_order === 'ASC') ? 'DESC' : 'ASC';

    // Check all orders for the user with sorting
    try {
        $sql = "SELECT order_id, total_price, order_date, order_status FROM orders WHERE user_id = ? ORDER BY $sort_column $sort_order";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order</title>
    <link rel="stylesheet" href="../css/my-order.css">
</head>
<body>
    <!-- container -->
    <div class="container">
        <!-- order details container -->
        <div class="order-details">
            <div class="title">
                <p>My Order</p>
            </div>
            <div>
                <?php
                    if ($result->num_rows > 0) {
                        echo '<table>';
                        echo '<thead>';
                        echo '<tr>';
                        
                        // Order ID Column with Arrow
                        echo '<th><a href="?sort_column=order_id&sort_order=' . $new_sort_order . '">Order ID ';
                        echo ($sort_column == 'order_id') ? ($sort_order == 'ASC' ? '&#9662;' : '&#9652') : '';
                        echo '</a></th>';
                        
                        // Total Price Column with Arrow
                        echo '<th><a href="?sort_column=total_price&sort_order=' . $new_sort_order . '">Total Price ';
                        echo ($sort_column == 'total_price') ? ($sort_order == 'ASC' ? '&#9662;' : '&#9652') : '';
                        echo '</a></th>';   
                        
                        // Order Date Column with Arrow
                        echo '<th><a href="?sort_column=order_date&sort_order=' . $new_sort_order . '">Order Date ';
                        echo ($sort_column == 'order_date') ? ($sort_order == 'ASC' ? '&#9662;' : '&#9652') : '';
                        echo '</a></th>';
                        
                        // Order Status Column with Arrow
                        echo '<th><a href="?sort_column=order_status&sort_order=' . $new_sort_order . '">Status ';
                        echo ($sort_column == 'order_status') ? ($sort_order == 'ASC' ? '&#9662;' : '&#9652') : '';
                        echo '</a></th>';

                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($row = $result->fetch_assoc()) {
                            // Fetch refund status if any
                            $order_id = $row['order_id'];
                            $sql = "SELECT status FROM refund_requests WHERE order_id = ?";
                            $stmt = $mysqli->prepare($sql);
                            $stmt->bind_param("i", $order_id);
                            $stmt->execute();
                            $gteOrder = $stmt->get_result();

                            if ($gteOrder->num_rows > 0) {
                                $order = $gteOrder->fetch_assoc();
                                $refund_status = $order['status'];
                            }
                            
                            echo '<tr onclick="window.location.href=\'order-details.php?order_id=' . $row['order_id'] . '\'">';
                            echo '<td>' . $row['order_id'] . '</td>';
                            echo '<td>RM ' . number_format($row['total_price'], 2) . '</td>';
                            echo '<td>' . date("Y-m-d H:i", strtotime($row['order_date'])) . '</td>';
                            echo '<td>' . $row['order_status'] . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo '<p>You have no orders yet.</p>';
                    }
                ?>

            </div>
        </div>
    </div>
</body>
</html>
<?php include("../header-footer/footer.php");?>


