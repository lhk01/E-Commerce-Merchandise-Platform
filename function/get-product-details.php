<?php
session_start();
include("../database/database.php");

if (!isset($_SESSION["user_id"])) {
    $product_id = $_GET['id'];
    $_SESSION['memory-page'] = "../pages/product-page.php?id=$product_id";
    redirect("../login/login.php");
}

if(isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION["user_id"];
    try {
        // Fetch product details from the database
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        $bag_sql = "SELECT * FROM bag WHERE user_id = ? AND product_id = ?";
        $bag_stmt = $mysqli->prepare($bag_sql);
        $bag_stmt->bind_param("is", $user_id, $product_id);
        $bag_stmt->execute();
        $existing_bag = $bag_stmt->get_result()->fetch_assoc();

        $bagsizeM = 0;
        $bagsizeL = 0;
        $bagsizeXL = 0;
        $bag_quantity = 0;

        if($existing_bag){
            if($existing_bag['categories'] === 'Apparel'){
                $bagsizeM =$existing_bag['size_M'];
                $bagsizeL = $existing_bag['size_L'] ;
                $bagsizeXL = $existing_bag['size_XL'];
            }else{
                $bag_quantity = $existing_bag['quantity'];
            }
        }

        // Prepare product data
        $productData = [
            'product_id' => $product['product_id'],
            'categories' => $product['categories'],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'sizeM' => $product['size_M'] ?? 0,
            'sizeL' => $product['size_L'] ?? 0,
            'sizeXL' => $product['size_XL'] ?? 0,
            'bagQuantity'=> $bag_quantity,
            'bagsizeM' => $bagsizeM,
            'bagsizeL' => $bagsizeL,
            'bagsizeXL' => $bagsizeXL

        ];




        // Return product data as JSON
        echo json_encode($productData);

    } catch (Exception $e) {
        // Return an error message if something goes wrong
        echo json_encode(['error' => "Error fetching product details: " . $e->getMessage()]);
    }
}
?>
