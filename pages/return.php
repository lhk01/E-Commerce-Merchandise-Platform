<?php
    session_start();
     include("../header-footer/header.php");
    include("../database/database.php");
    require_once("../function/function.php");

    // Check if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: ../login/login.php");
        exit;
    }

    if(isset($_POST['product_id']) && !empty($_POST['product_id'])){
        $_SESSION['product_id'] = $_POST['product_id'];
        $_SESSION['order_id'] = $_POST['order_id'];
    }

    $product_id = $_SESSION['product_id'];
    $user_id = $_SESSION['user_id'];
    $order_id = $_SESSION['order_id'];
    // Fetch order details
    try {
        // Fetch products in the order
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        $images = explode(",", $result['product_Image']);

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/return.css">
</head>
<body>
    <div class = "container">
        <a href = "order-details.php?order_id=<?php echo $order_id;?>">
            <button>Back</button>
        </a>
        <div class="product-item">
        <?php
            echo '<div class="product-details">';
            echo "<img src = ../upload/product_image/".$images[0].">";
            echo "<p>".$result["productName"]."</p>";
            echo '</div>';
          ?>
    
     <form action="process_return.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="return_items" value="<?php echo $product_id; ?>">
        <div class="reason">
            <label for="refund_reason">Select Reason for Return/Refund:</label>
            <select name="refund_reason" required>
                <option value="" disabled selected>Select a reason</option>
                <option value="Did not receive the full order">Did not receive the full order (all items in the order)</option>
                <option value="Did not receive part of the order">Did not receive part of the order (e.g., missing parts)</option>
                <option value="Received the wrong product">Received the wrong product(s) (seller sent wrong product/variation)</option>
                <option value="Received product with physical damage">Received a product with physical damage (e.g., dented, scratched, shattered)</option>
            </select>
        </div>

        <div class="upload-container">
            <label for="proof_image">Upload proof of issue (image):</label>
            <input type="file" name="proof_image[]" accept="image/*" id="input-file"  required multiple>
        </div>

        <div class="submit-container">
            <input type="submit" value="Submit Request">
        </div>
     </form>
     </div>
     </div>
</body>
</html>
<?php include("../header-footer/footer.php");?> 



