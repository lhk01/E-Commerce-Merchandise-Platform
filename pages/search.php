<?php
  session_start();
  include("../database/database.php");
   include("../header-footer/header.php");
  require_once("../function/function.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../css/product.css">
  <style>
    .container{
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    
  </style>
</head>
<body>
  <div class = "container">
 
    
    <?php
  
    if(isset($_POST['search']) && !empty($_POST['search'])){
      $search = filter_input(INPUT_POST, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $search_sql = "SELECT * FROM products WHERE active = 1 AND (productName LIKE '%$search%' OR product_description LIKE '%$search%')";
      $result = mysqli_query($mysqli, $search_sql);

      if ($result->num_rows > 0) {
        
        echo '<div class ="search-title">';
        echo "<h2>Search Results for ".$search." ...</h2>";
        echo "</div>";
        echo '<div class= "product-grid" id = "product-container">';
        while ($row = $result->fetch_assoc()) {

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
          echo '<div class ="search-title">';
          echo "<h2>No Results for ".$search." ...</h2>";
          echo "</div>";
      }

    }

  ?>
  </div>
  </div>
  <script>
    function nextImage(button) {
    let sliderContainer = button.parentNode;
    let images = sliderContainer.querySelectorAll('.slider-image');
    let currentIndex = 0;

    // Find the currently displayed image
    images.forEach((img, index) => {
        if (img.style.display === 'block') {
            currentIndex = index;
            img.style.display = 'none';
        }
    });

    // Show the next image (loop back to first if at the end)
    let nextIndex = (currentIndex + 1) % images.length;
    images[nextIndex].style.display = 'block';
}

function prevImage(button) {
    let sliderContainer = button.parentNode;
    let images = sliderContainer.querySelectorAll('.slider-image');
    let currentIndex = 0;

    // Find the currently displayed image
    images.forEach((img, index) => {
        if (img.style.display === 'block') {
            currentIndex = index;
            img.style.display = 'none';
        }
    });

    // Show the previous image (loop back to last if at the beginning)
    let prevIndex = (currentIndex - 1 + images.length) % images.length;
    images[prevIndex].style.display = 'block';
}

  </script>
</body>
</html>