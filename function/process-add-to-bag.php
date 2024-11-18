<?php
session_start();
include("../database/database.php");
require_once("../function/function.php");



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = $_SESSION["user_id"];
    $product_id = $_GET['id'];
    $quantity = null;
    $sizeM = $sizeL = $sizeXL = null;

    try {
        // Fetch product details
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not found");
        }

        $categories = $product['categories'];
        $isApparel = ($categories === "Apparel");

        // Process sizes for apparel or quantity for non-apparel
        if ($isApparel && isset($_POST['size'])) {
            $sizes = (array)$_POST['size']; // Ensure $sizes is always an array
            $quantity = (int)$_POST['quantity']; // Get the overall quantity for each selected size

            foreach ($sizes as $size) {
                if ($size === 'M') {
                    $sizeM = $quantity;
                } elseif ($size === 'L') {
                    $sizeL = $quantity;
                } elseif ($size === 'XL') {
                    $sizeXL = $quantity;
                }
            }
        } else {
            $quantity = (int)$_POST['quantity'];
        }

        // Check if product is already in bag
        $bag_sql = "SELECT * FROM bag WHERE user_id = ? AND product_id = ?";
        $bag_stmt = $mysqli->prepare($bag_sql);
        $bag_stmt->bind_param("is", $user_id, $product_id);
        $bag_stmt->execute();
        $existing_bag = $bag_stmt->get_result()->fetch_assoc();

        if ($existing_bag) {
            // Update existing bag entry
            if ($isApparel) {
                $total_sizeM = $existing_bag['size_M'] + $sizeM;
                $total_sizeL = $existing_bag['size_L'] + $sizeL;
                $total_sizeXL = $existing_bag['size_XL'] + $sizeXL;

                $update_sql = "UPDATE bag SET size_M = ?, size_L = ?, size_XL = ? WHERE user_id = ? AND product_id = ?";
                $update_stmt = $mysqli->prepare($update_sql);
                $update_stmt->bind_param("iiiis", $total_sizeM, $total_sizeL, $total_sizeXL, $user_id, $product_id);
            } else {
                $new_quantity = $existing_bag['quantity'] + $quantity;

                $update_sql = "UPDATE bag SET quantity = ? WHERE user_id = ? AND product_id = ?";
                $update_stmt = $mysqli->prepare($update_sql);
                $update_stmt->bind_param("iis", $new_quantity, $user_id, $product_id);
            }
            $update_stmt->execute();
        } else {
            // Insert new entry in bag
            $insert_sql = "INSERT INTO bag (user_id, product_id, categories, price, quantity, size_M, size_L, size_XL) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $mysqli->prepare($insert_sql);
            $insert_stmt->bind_param("issdiiii", $user_id, $product_id, $categories, $product['price'], $quantity, $sizeM, $sizeL, $sizeXL);
            $insert_stmt->execute();
        }

    } catch (Exception $e) {
        header("Location: ../pages/product-page.php?id=$product_id");
        exit();
    }
}
?>
