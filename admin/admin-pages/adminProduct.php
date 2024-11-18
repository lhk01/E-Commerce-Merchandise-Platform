<?php
session_start();
include("../database/database.php"); // Ensure this file connects to the database
include ("header.php");
// Check for database connection
    if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }

// Query to get all products
$sql = "SELECT * FROM products";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="../css/adminProduct.css">
    <script type="text/javascript">
        function confirmDelete() {
            return confirm("Are you sure you want to delete this product?");
        }
    </script>
</head>
<body>

    <div class="main-content">
        <section class="add-product">
            <ul>
                <li><a href="adminAddProduct.php">Add Product</a></li>
            </ul>
        </section>
        <section class="product-list">
            <h1>Product List</h1>
            
            <?php
            if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
                // Check if $_SESSION['msg'] is a string
                if (is_string($_SESSION['msg'])) {
                    echo '<div class="message">' . htmlspecialchars($_SESSION['msg'], ENT_QUOTES, 'UTF-8') . '</div>';
                } else {
                    // Handle cases where $_SESSION['msg'] is not a string
                    echo '<div class="message">Invalid message format.</div>';
                }
                unset($_SESSION['msg']); // Clear the message after displaying
            }
            ?>


            <div class="product-table">
                <table>
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Image</th>
                            <th>Status</th>   
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            // Fetch and display each row of the table
                            while ($row = $result->fetch_assoc()) {
                                $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
                                $price = number_format($row['price'], 2);
                                $productImages = $row['product_Image'];
                                $imageArray = explode(",", $productImages);
                                $firstImage = htmlspecialchars($imageArray[0], ENT_QUOTES, 'UTF-8');
                                $productId = $row['product_id']; // Get the product_id
                                $status = (int)$row['active'];

                                if($status === 1){
                                    $active = "Active";
                                }else{
                                    $active = "Deactivated";
                                }

                                echo "<tr>";
                                echo "<td data-title='Product'>$productId</td>";
                                echo "<td data-title='Name'>$productName</td>";
                                echo "<td data-title='Price'>RM$price</td>";
                                echo "<td data-title='Image'><img src='../../upload/product_image/$firstImage' alt='$productName' width='120'></td>";
                                echo "<td data-title='Status'>$active</td>";
                                echo "<td data-title='Action'>";
                                echo "<a href='edit-Product.php?id=$productId' class='ed'>Edit</a> ";
                                echo "<a href='delete-product.php?id=$productId' class='ed' onclick='return confirmDelete()'>Delete</a> ";
                                // Add "View Reviews" link
                                echo "<a href='review.php?product_id=$productId' class='ed'>View Reviews</a>";
                                echo "<a href='active.php?id=$productId' class='ed'>Active Products</a>";
                                echo "<a href='deactive.php?id=$productId' class='ed'>Deactive Products</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No products found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>
