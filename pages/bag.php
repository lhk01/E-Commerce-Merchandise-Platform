<?php
    session_start();
    include("../header-footer/header.php");
    include("../database/database.php");
    require_once("../function/function.php");

    // Check again if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        $_SESSION['memory-page'] = "../pages/bag.php";
        redirect("../login/login.php");
    }

    // Store Session user_id in variables user_id
    $user_id = $_SESSION["user_id"];
    $total = 0;

    try{
        // Prepare SQL query to fetch bag contents along with product details
        $sql = "SELECT bag.*, products.product_image, products.productName 
                FROM bag 
                JOIN products ON bag.product_id = products.product_id 
                WHERE bag.user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Get the bag id
            $bag_id = $_POST['bag_id'];
                
            // Fetch the product_id from the bag table
            $fetch_product_sql = "SELECT product_id FROM bag WHERE bag_id = ?";
            $fetch_product_stmt = $mysqli->prepare($fetch_product_sql);
            $fetch_product_stmt->bind_param("i", $bag_id);
            $fetch_product_stmt->execute();
            $fetch_product_result = $fetch_product_stmt->get_result();
            $product_row = $fetch_product_result->fetch_assoc();
            $product_id = $product_row['product_id'];

            if (isset($_POST['delete_size'])) {
                // Handle deletion for Apparel sizes
                $size = $_POST['size'];

                // Prepare update statement to set the specific size to NULL
                $update_sql = "UPDATE bag SET size_" . $size . " = NULL WHERE bag_id = ?";
                $update_stmt = $mysqli->prepare($update_sql);
                $update_stmt->bind_param("i", $bag_id);
                $update_stmt->execute();

                // Check if all sizes are NULL to delete the product
                $check_sql = "SELECT size_M, size_L, size_XL FROM bag WHERE bag_id = ?";
                $check_stmt = $mysqli->prepare($check_sql);
                $check_stmt->bind_param("i", $bag_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $check_row = $check_result->fetch_assoc();

                // If all sizes are NULL, delete the product from the bag
                if (empty($check_row['size_M']) && empty($check_row['size_L']) && empty($check_row['size_XL'])) {
                    $delete_sql = "DELETE FROM bag WHERE bag_id = ?";
                    $delete_stmt = $mysqli->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $bag_id);
                    $delete_stmt->execute();
                    
                }
                redirect('bag.php');
            }
            
             // Handle deletion for non-Apparel products
            if (isset($_POST['delete_product'])) {
                $delete_sql = "DELETE FROM bag WHERE bag_id = ?";
                $delete_stmt = $mysqli->prepare($delete_sql);
                $delete_stmt->bind_param("i", $bag_id);
                $delete_stmt->execute();
                
                redirect('bag.php');
            } 

            if (isset($_POST['increment_quantity'])){
                $quantity_change = (int)$_POST['quantity_change'];
                $size = $_POST['size'] ?? null;
                $bag_id = $_POST['bag_id'];

                // Check stock in products table
                if ($size) {
                    $check_stock_sql = "SELECT size_" . $size . " FROM products WHERE product_id = ?";
                } else {
                    $check_stock_sql = "SELECT stock FROM products WHERE product_id = ?";
                }

                $check_stock_stmt = $mysqli->prepare($check_stock_sql);
                $check_stock_stmt->bind_param("s", $product_id);
                $check_stock_stmt->execute();
                $check_stock_result = $check_stock_stmt->get_result();
                $product_stock = $check_stock_result->fetch_assoc();

                // Fetch current quantity from the bag
                if ($size) {
                    $fetch_bag_sql = "SELECT size_" . $size . " FROM bag WHERE bag_id = ?";
                } else {
                    $fetch_bag_sql = "SELECT quantity FROM bag WHERE bag_id = ?";
                }

                $fetch_bag_stmt = $mysqli->prepare($fetch_bag_sql);
                $fetch_bag_stmt->bind_param("i", $bag_id);
                $fetch_bag_stmt->execute();
                $bag_result = $fetch_bag_stmt->get_result();
                $bag_item = $bag_result->fetch_assoc();

                // Check if there's enough stock to increment the quantity
                if (($size && $bag_item["size_" . $size] + $quantity_change <= $product_stock["size_" . $size]) ||
                    (!$size && $bag_item['quantity'] + $quantity_change <= $product_stock['stock'])) {
                    
                    // Update the bag table
                    if ($size) {
                        $update_quantity_sql = "UPDATE bag SET size_" . $size . " = size_" . $size . " + ? WHERE bag_id = ?";
                    } else {
                        $update_quantity_sql = "UPDATE bag SET quantity = quantity + ? WHERE bag_id = ?";
                    }

                    $update_quantity_stmt = $mysqli->prepare($update_quantity_sql);
                    $update_quantity_stmt->bind_param("ii", $quantity_change, $bag_id);
                    $update_quantity_stmt->execute();
                    redirect('bag.php');
                } else {
                    $_SESSION['msg'] = "Cannot add more, stock is insufficient.";
                    $_SESSION['bag-id'] = $bag_id;
                    $_SESSION['size'] = $size;
                    redirect('bag.php');
                }
            }

             if (isset($_POST['decrement_quantity'])) {
                $quantity_change = (int)$_POST['quantity_change'];
                $size = $_POST['size'] ?? null;
                $bag_id = $_POST['bag_id'];
                
                // Update the quantity in the bag table
                if ($size) {
                    $update_quantity_sql = "UPDATE bag SET size_" . $size . " = GREATEST(size_" . $size . " - ?, 0) WHERE bag_id = ?";
                } else {
                    $update_quantity_sql = "UPDATE bag SET quantity = GREATEST(quantity - ?, 0) WHERE bag_id = ?";
                }

                // Prepare and execute the update statement
                $update_quantity_stmt = $mysqli->prepare($update_quantity_sql);
                $update_quantity_stmt->bind_param("ii", $quantity_change, $bag_id);
                $update_quantity_stmt->execute();

                // Check if the quantity is now zero and delete the item if it is
                if ($size) {
                    // Check the specific size column
                    $check_quantity_sql = "SELECT size_" . $size . " FROM bag WHERE bag_id = ?";
                } else {
                    $check_quantity_sql = "SELECT quantity FROM bag WHERE bag_id = ?";
                }
                
                $check_quantity_stmt = $mysqli->prepare($check_quantity_sql);
                $check_quantity_stmt->bind_param("i", $bag_id);
                $check_quantity_stmt->execute();
                $check_quantity_stmt->bind_result($remaining_quantity);
                $check_quantity_stmt->fetch();
                $check_quantity_stmt->close();

                if ($remaining_quantity == 0) {
                    $delete_item_sql = "DELETE FROM bag WHERE bag_id = ?";
                    $delete_item_stmt = $mysqli->prepare($delete_item_sql);
                    $delete_item_stmt->bind_param("i", $bag_id);
                    $delete_item_stmt->execute();
                    $delete_item_stmt->close();
                }

                redirect('bag.php');
            }


        }


    }catch (Exception $e) {
        // Handle any exception that occurs and display error message
        echo "Error: " . $e->getMessage();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bag</title>
    <link rel="stylesheet" href="../css/bag.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
</head>
<body>
     <div class = "container">
        <div class="bag">
            <?php

                $sql = "SELECT * FROM bag WHERE user_id = ?";
                $stmt = $mysqli->prepare($sql);

                // Assuming you have the user ID stored in $user_id
                $stmt->bind_param("s", $user_id); // Use the correct variable here
                $stmt->execute();
                $item = $stmt->get_result();

                $index = 0;

                if ($item->num_rows > 0) {
                    while ($row = $item->fetch_assoc()) { // Change $result to $item
                        
                        if($row['categories'] === "Apparel"){
                            $index += $row['size_M'];
                            $index += $row['size_L'];
                            $index += $row['size_XL'];
                        }else{
                            $index += $row['quantity'];
                        }
                    }
                }
            ?>

            
            <h3>Your bag <span class = "total-item"><?php echo "(".$index.")"; ?></span></h3>

        </div>
        
        <div>
            <?php
                if ($result->num_rows > 0){
                    while ($row = $result->fetch_assoc()){
                        // Assuming product_image is a comma-separated string of image filenames
                        $images = explode(",", $row['product_image']);
                        $firstImage = trim($images[0]); // Get the first image

                        if ($row['categories'] === "Apparel") {
                            // Display sizes in individual blocks
                            $sizes = [
                                'M' => $row["size_M"],
                                'L' => $row["size_L"],
                                'XL' => $row["size_XL"]
                            ];

                            foreach ($sizes as $size => $quantity){
                                if (!empty($quantity)){
                                    // container
                                    echo '<div class="container-2">';

                                    // image container
                                    echo '<div class = "img">';
                                    echo '<img src="../upload/product_image/' . $firstImage . '" alt="Product Image" style="width:150px;height:auto;"><br>';
                                    echo '</div>';

                                    // product details container
                                    echo '<div class = "product-details">';
                                    echo '<p>' . $row["productName"] . '</p>';
                                    echo "Size: " . $size . "<br>";
                                    echo '<div class="quantity-container">';

                                    // Decrement button form
                                    echo '<form method="POST" style="display:inline;">';
                                    echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                                    echo '<input type="hidden" name="quantity_change" value="1">';
                                    echo '<input type="hidden" name="size" value="' . $size . '">';
                                    echo '<button onclick = "decreaseCartItem()" class="d-quantity-button" type="submit" name="decrement_quantity">-</button>';
                                    echo '</form>';

                                    // Display the quantity
                                    echo '<div class="quantity-display">'; 
                                    echo '<p id ="num1">'.$quantity.'</p>';
                                    echo '</div>';

                                    // Increment button form
                                    echo '<form method="POST" style="display:inline;">';
                                    echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                                    echo '<input type="hidden" name="quantity_change" value="1">';
                                    echo '<input type="hidden" name="size" value="' . $size . '">';
                                    echo '<button onclick ="increaseCartItem()" class="i-quantity-button" type="submit" name="increment_quantity">+</button>';
                                    echo '</form>';

                                    echo '</div>';
                                    
                                    // message container
                                    if(!empty($_SESSION['msg'])){
                                        if($row["bag_id"] == $_SESSION['bag-id'] && $size == $_SESSION['size']){
                                            echo '<div>';
                                            echo "<p style='font-size: 16px; font-weight: 300;'>".
                                                    $_SESSION['msg']."</p>";
                                            echo '</div>';
                                            unset($_SESSION['bag-id']);
                                            unset($_SESSION['msg']);
                                        }
                                    }

                                    echo '</div>';
                                    //end product container
                                    
                                    echo '<div class = "container-3">';
                                    echo '<div class="price">';
                                    echo "RM".$row["price"];
                                    echo '</div>';
                                    
                                    echo '<div class="delete">';
                                    echo '<form method="POST" style="display:inline;">';
                                    echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                                    echo '<button onclick = "deleteCartItem()" class = "delete-btn" type="submit" name="delete_product"><i class="fa fa-trash-o" style="font-size:26px"></i></button>';
                                    echo '</form>';
                                    echo '</div>';

                                    echo '</div>';

                                

                                    echo '</div>';
                                    $total += $row["price"] * $quantity;
                                    
                                }
                            }
                        }else{

                            echo '<div class="container-2">';

                            // image container
                            echo '<div class = "img">';
                            echo '<img src="../upload/product_image/' . $firstImage . '" alt="Product Image" style="width:150px;height:auto;"><br>';
                            echo '</div>';

                            // product details container
                            echo '<div class = "product-details">';
                            echo '<p>' . $row["productName"] . '</p>';
                            echo '<div class="quantity-container">';
                            
                            // Decrement button form
                            echo '<form method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                            echo '<input type="hidden" name="quantity_change" value="1">';
                            echo '<button onclick = "decreaseCartItem()" class="d-quantity-button" type="submit" name="decrement_quantity">-</button>';
                            echo '</form>';

                            // Display the quantity
                            echo '<div class="quantity-display">';
                            echo '<p  id ="num2">'.$row["quantity"].'</p>';
                            echo '</div>';

                            // Increment button form
                            echo '<form method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                            echo '<input type="hidden" name="quantity_change" value="1">';
                            echo '<button onclick ="increaseCartItem()" class="i-quantity-button" type="submit" name="increment_quantity">+</button>';
                            echo '</form>';
                            echo '</div>';

                            // message container
                            if(!empty($_SESSION['msg'])){
                                if($row["bag_id"] === $_SESSION['bag-id']){
                                    echo '<div>';
                                    echo "<p style='font-size: 16px; font-weight: 300;'>".
                                            $_SESSION['mssg']."</p>";
                                    echo '</div>';
                                    unset($_SESSION['bag-id']);
                                    unset($_SESSION['msg']);
                                }
                            }

                            echo '</div>';
                            //end product container
                            
                            echo '<div class = "container-3">';
                            echo '<div class="price">';
                            echo "RM".$row["price"];
                            echo '</div>';

                            echo '<div class="delete">';
                            echo '<form method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="bag_id" value="' . $row["bag_id"] . '">';
                            echo '<button onclick = "deleteCartItem()" class = "delete-btn" type="submit" name="delete_product"><i class="fa fa-trash-o" style="font-size:26px"></i></button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
        
                            echo '</div>';
                            $total += $row["price"] * $row["quantity"];

                        }
                    }
                     echo '<div class="total-container">
                            <div class="sub-total"> 
                            <h3>Subtotal</h3>
                            <span>RM ' . $total . '</span>
                            </div>
                            <div class="shiping">
                            <h3>Shipping</h3>
                            <span>RM 5</span>
                            </div>
                            <div class="total">
                            <h3>Total</h3>
                            <span>RM ' . ($total + 5) . '</span>
                            </div>
                            </div>';

                    echo '<div class="checkout">
                            <form method="POST" action="checkout.php">
                            <button type="submit" class="checkout-button">Proceed to Checkout</button>
                            </form>
                            </div>';
                    
                }else{
                    echo "<div class = 'empty-bag'>";
                    echo "<p>Your Bag is empty</p>";
                    echo "</div>";
                }
            ?>
        </div>
     </div>
     <script>
        let cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
        function increaseCartItem(){
            cartQuantity++;
            localStorage.setItem('cartQuantity', cartQuantity);
            updateCartDisplay();
        }


  // Decrease the red dot quantity
  function decreaseCartItem() {
    if (cartQuantity > 0) {
      cartQuantity--;
      localStorage.setItem('cartQuantity', cartQuantity); // Store the updated quantity in local storage
    }
    updateCartDisplay();
  }

   function deleteCartItem() {
    
    var quantity1 = document.getElementById('num1') ? parseInt(document.getElementById('num1').innerText) : 0;
    var quantity2 = document.getElementById('num2') ? parseInt(document.getElementById('num2').innerText) : 0;
    
    // Get the current cart quantity from localStorage, defaulting to 0
    let cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
    
    // Decrease the cart quantity based on the quantities of the deleted items
    if (quantity1 > 0) {
        cartQuantity -= quantity1;
    } else if (quantity2 > 0) {
        cartQuantity -= quantity2;
    }

    // Ensure cart quantity doesn't go below zero
    if (cartQuantity < 0) {
        cartQuantity = 0;
    }
    
    // Update the localStorage with the new cart quantity
    localStorage.setItem('cartQuantity', cartQuantity); 

    // Update the cart display to reflect the new quantity
    updateCartDisplay();
}


  function updateCartDisplay() {
    const quantityDisplay = document.querySelector('.cart-quantity');
    const cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
    
    if (cartQuantity > 0) {

      quantityDisplay.style.display = 'block'; // show the quantity
      quantityDisplay.textContent = cartQuantity > 9 ? '9+' : cartQuantity; // If more than 9 it will show 9+
    } else {
      quantityDisplay.style.display = 'none'; // Hide the red dot
    }
  }
     </script>
</body>
</html>
<?php include("../header-footer/footer.php");?> 