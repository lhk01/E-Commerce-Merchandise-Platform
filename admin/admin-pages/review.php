<?php
session_start();

include("../database/database.php");
require_once("../function/function.php");

if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }

// Get the product_id from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';

if (empty($product_id)) {
    echo "No product selected.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Review</title>
    <link rel="stylesheet" href="../css/review.css">
</head>
<body>

<div class="main">

    <h3>Product Review for Product ID: <?php echo htmlspecialchars($product_id); ?></h3>

    <!-- Average Rating Section -->
    <div class="product-Rating">
        <?php
        $sql = 'SELECT * FROM reviews WHERE product_id = ?';

        $index = 0;
        $total = 0;
        $average = 0;
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $total += $row['review_score'];
                $index++;
            }
            $average = $total / $index;
        }
        
        echo '<div class = "line"><hr></div>';
        echo '<div class="average">';
        echo 'Average Rating: ';
        printf("%.2f", $average);
        echo '</div>';
        echo '<div class = "line"><hr></div>';
        ?>
    </div>

    <!-- Reviews Table -->
    <div class="product-Rating">
        <?php
        $sql = "
            SELECT r.*, u.username 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ?
        ";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<h2>Product Reviews</h2>";
                echo '<div class = "line"><hr></div>';
                echo "<table>";
                echo "<tr>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                      </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td data-title='Name'>" . htmlspecialchars($row['username']) . "</td>";

                    // Display stars based on the review score
                    echo "<td data-title='Rating'>";
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $row['review_score']) {
                            echo '<img class="star" src="../picture/star-full.png" alt="Star">';
                        } else {
                            echo '<img class="star" src="../picture/star.png" alt="Empty Star">';
                        }
                    }
                    echo "</td>";

                    echo "<td data-title='Comment'>" . htmlspecialchars($row['comment']) . "</td>";
                    echo "<td data-title='Date'>" . htmlspecialchars($row['review_date']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No reviews found.";
            }
            $stmt->close();
        }
        ?>
    </div>
</div>

</body>
</html>
