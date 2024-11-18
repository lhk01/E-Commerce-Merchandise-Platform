<?php
session_start();

include("../database/database.php");
require_once("../function/function.php");

try {
    if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $product_id = $_GET["id"];

        // Correct SQL query
        $sql = "UPDATE products SET active = 0 WHERE product_id = ?";
        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $product_id);

            if ($stmt->execute()) {
                $_SESSION['msg'] = "Deactive Successfully";
            }
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare the SQL statement.");
        }
    } else {
        throw new Exception("Invalid product ID.");
    }
} catch (Exception $e) {
    $_SESSION['msg'] = "Delete Failed: " . $e->getMessage();
} finally {
    redirect("adminProduct.php");
}
