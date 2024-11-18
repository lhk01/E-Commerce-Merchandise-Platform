<?php

    include("../database/database.php");
    require_once("../function/function.php");
    try {

    if (!isset($mysqli)) {
        throw new Exception("Database connection object not found.");
    }

    $limit = 12;

    // Get the offset, categories, sortBy, and availability from POST request
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    $categories = isset($_POST['categories']) ? $_POST['categories'] : null;
    $sortBy = isset($_POST['sortBy']) ? $_POST['sortBy'] : '';
    $availability = isset($_POST['availability']) ? $_POST['availability'] : null;

    // Prepare the base SQL query
    $sql = "SELECT * FROM products";

    // Initialize an array for WHERE conditions
    $whereConditions = array();

    $whereConditions[] = "active = 1";

    // Apply category filtering
    if ($categories != null) {
        $categoriesArray = explode(',', $categories);
        $categoriesArray = array_map(function($category) use ($mysqli) {
            return "'" . $mysqli->real_escape_string($category) . "'";
        }, $categoriesArray);
        $categoriesString = implode(',', $categoriesArray);
        $whereConditions[] = "categories IN ($categoriesString)";
    }

    // Apply availability filtering
    if ($availability != null) {
        $availOptions = explode(',', $availability);
        $inStock = in_array('in-stock', $availOptions);
        $outOfStock = in_array('out-of-stock', $availOptions);

        if ($inStock && !$outOfStock) {
            // Only in-stock
            $availabilityCondition = "((categories = 'Apparel' AND (size_M > 0 OR size_L > 0 OR size_XL > 0)) OR (categories IN ('Accessory', 'Plush') AND stock > 0))";
            $whereConditions[] = $availabilityCondition;
        } else if (!$inStock && $outOfStock) {
            // Only out-of-stock
            $availabilityCondition = "((categories = 'Apparel' AND size_M = 0 AND size_L = 0 AND size_XL = 0) OR (categories IN ('Accessory', 'Plush') AND stock = 0))";
            $whereConditions[] = $availabilityCondition;
        }
        // If both in-stock and out-of-stock are selected, do not apply any availability filter
    }

    // Build the WHERE clause
    if (count($whereConditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }

    // Apply sorting based on the selected sort option
    if ($sortBy == "high-to-low") {
        $sql .= " ORDER BY price DESC";
    } else if ($sortBy == "low-to-high") {
        $sql .= " ORDER BY price ASC";
    }

    // Append limit and offset
    $sql .= " LIMIT $limit OFFSET $offset";

    $result = $mysqli->query($sql);

    if (!$result) {
        throw new Exception("SQL query failed: " . $mysqli->error);
    }

    if ($result->num_rows > 0) {
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        // Shuffle the array to randomize order
        shuffle($rows);
        
    foreach ($rows as $row) {

        $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
        $price = number_format($row['price'], 2);
        $productImages = $row['product_Image'];
        $imageArray = explode(",", $productImages); // Assume images are comma-separated
        $productId = $row['product_id'];

        // Start of product container
        
        echo "<div class='product-item'>";

        // Create the image slider container
        echo "<div class='slider-container'>";
        echo "<a href='product-page.php?id=$productId' class='product-link'>";
        // Create the images in the slider
        foreach ($imageArray as $index => $image) {
            $image = htmlspecialchars(trim($image), ENT_QUOTES, 'UTF-8');
            echo "<img src='../upload/product_image/$image' alt='$productName' class='slider-image' data-index='$index' style='" . ($index == 0 ? "display: block;" : "display: none;") . "'>";
        }
        echo "</a>";
        // Add the navigation buttons for the slider
        echo "<button class='prev' onclick='prevImage(this)'>&#10094;</button>";
        echo "<button class='next' onclick='nextImage(this)'>&#10095;</button>";

        echo "</div>"; // End of slider container
        echo "<a href='product-page.php?id=$productId' class='product-link'>";
        // Product details
        echo "<h2>$productName</h2>";
        echo "<p>RM $price</p>";

        echo "</div>"; // End of product item
        echo "</a>";
    }
} else {
    echo ""; // No more products
}

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage() . "\n", 3, "../var/log/app_debug.log");
    header("Location: ../errorpage/error.html");
    exit();
}


