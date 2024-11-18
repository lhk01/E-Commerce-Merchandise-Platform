<?php
  session_start();
   include("../header-footer/header.php");
  include("../database/database.php");
  require_once("../function/function.php");

  
  // Get all product information based on product id
  if(isset($_GET['id'])){

    try{
      $sql = "SELECT * FROM products WHERE active = 1";

      $stmt = $mysqli->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result();
      

      // Fetch all the rows into an array first
      $products = [];
      while ($row = $result->fetch_assoc()) {
          $products[] = $row;
      }
      // get product id 
      $product_id = $_GET['id'];

      // prepare sql statement
      $sql = "SELECT * FROM products WHERE product_id = ?";
      $stmt = $mysqli->prepare($sql);
      $stmt->bind_param("s", $product_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $product = $result->fetch_assoc();

      $stock = null;
      $selection = false;
      $sizeM = $sizeL = $sizeXL = $quantity = $sizes = null;

      // Get all the product details
      $productName = $product['productName'];
      $description = $product['product_description'];
      $categories = $product['categories'];
      $price = $product['price'];

      $images = explode(",", $product['product_Image']);

      if($categories === 'Apparel'){
        $sizeM = $product['size_M'];
        $sizeL = $product['size_L'];
        $sizeXL = $product['size_XL'];
      }else{
        $stock = $product['stock'];
      }

    }catch (Exception $e) {
      // Handle any exception that occurs and display error message
      echo "Error: " . $e->getMessage();
    }

  }

  // user submit the form (add to cart)
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Page</title>

  <link rel="stylesheet" href="../css/product-page.css">
  <script src="../javascript/increament-btn.js" defer></script>
  <script src="../javascript/size-box-selection.js" defer></script>
  <script src="../javascript/select-image.js" defer></script>
</head>
<body>
  
  <!-- container -->
  <div class = "container">
    <!-- product-container -->
    <div class="product-container">
      <div class = "picture">
        <?php
            // Display the first image as the main image
            echo "<img id='main-image' src='../upload/product_image/$images[0]' alt='$productName' class='main-pic'>";
            echo "<div class='product-views'>";

            // Loop through the rest of the images if they exist
            for ($i = 0; $i < count($images); $i++) {
                echo "<img class='thumbnail' src='../upload/product_image/{$images[$i]}' alt='$productName' data-index='$i'>";
            }
            echo "</div>";
        ?>
      </div>
      
      <!-- product details container -->
      <div class="details">
        <!-- Product name and price -->
        <div class="name-price">
          <h2><?php echo $productName; ?></h2>
          <p class="price">RM<?php echo $price; ?></p>
        </div>
        
          <?php 
              $sql = "
                  SELECT AVG(review_score) as average_rating, COUNT(*) as total_reviews 
                  FROM reviews 
                  WHERE product_id = ?
              ";

              // Prepare the statement
              if ($stmt = $mysqli->prepare($sql)) {
                  // Bind parameters (s = string type for product_id)
                  $stmt->bind_param("s", $product_id);

                  // Execute the statement
                  $stmt->execute();

                  // Get the result
                  $result = $stmt->get_result();
                  if ($row = $result->fetch_assoc()) {
                      // Get the average rating and total reviews
                      $average_rating = round($row['average_rating'], 1);  // Rounded to 1 decimal
                      $total_reviews = $row['total_reviews'];

                      // Display the average rating using stars
                      echo '<div class="review">';
                      echo '<p>';
                      // Display full and half stars based on average rating
                      for ($i = 0; $i < 5; $i++) {
                          if ($i < floor($average_rating)) {
                              // Full star
                              echo '<img class="star" src="../picture/star-full.png">';
                          } elseif ($i < ceil($average_rating)) {
                              // Half star
                              echo '<img class="star" src="../picture/halfstar.webp">';
                          } else {
                              // Empty star
                              echo '<img class="star" src="../picture/star.png">';
                          }
                      }
                      // Display the average rating value and the total reviews count
                      echo " ({$total_reviews} reviews)";
                      echo '</p>';
                      echo '</div>';
                  }

                  // Close statement
                  $stmt->close();
              } 
          ?>


        
        <!-- Size selection container -->
        <form action="" method="POST" id="quantityForm">
          <div class="sizes">
            <?php
            if($categories === 'Apparel'){
              echo "<h3>Size</h3>";
              echo "<div class='checkbox-container'>";
              echo "<label class='checkbox-label' for='size-m'>
                    <input type='checkbox' id='size-m' name='size[]' value='M'> M</label>";
              echo "<label class='checkbox-label' for='size-l'>
                    <input type='checkbox' id='size-l' name='size[]' value='L'> L</label>";
              echo "<label class='checkbox-label' for='size-xl'>
                    <input type='checkbox' id='size-xl' name='size[]' value='XL'> XL</label>";
              echo "</div>";
            }
            ?>
          </div> 

          <!-- Quantity control -->
          <div class="increament-btn">
            <span class="minus">-</span>
            <input id = "quantity" type="number" class="num" value="1" readonly>
            <span class="plus">+</span>
            <input type="hidden" name="quantity" id="quantityInput" value="1">
          </div>

          <!-- Low stock warning -->
          <div class="stock">
            <?php
              if(($stock > 0 && $stock <= 5) || (($sizeM > 0  && $sizeM <= 5) && ($sizeL > 0  && $sizeL <= 5) && ($sizeXL > 0  && $sizeXL <= 5))){
                echo "<p style='color: red;'> Low Stock! </p>";
              }
            ?>
          </div>

        </form>
        <!-- Submit button for Add to Cart -->
          <div class="submit-btn">
            <?php
              if(($stock === 0) || ($sizeM === 0  && $sizeL === 0 && $sizeXL ===0)){
                echo "<button class='add-to-bag' disabled>Out of stock</button>";
              } else {
                echo "<button type='button' class='add-to-bag'>Add to Bag</button>";
              }
            ?>
          </div>

        <!-- Message container for feedback -->
        <div id="notification" class="notification hidden"></div>
        <div id="error_notification" class="error_notification hidden"></div>
        
        
        <!-- Description container -->
        <div class="description">
          <h3>Description</h3>
          <p><?php echo $description; ?></p>
        </div>
      </div>

    </div>
     <!-- user review container -->
    <div class = "product-Rating">
        <?php 
          
            $sql = "
                  SELECT r.*, u.username 
                  FROM reviews r
                  JOIN users u ON r.user_id = u.id
                  WHERE r.product_id = ?
              ";

            // Prepare the statement
            if ($stmt = $mysqli->prepare($sql)) {
                // Bind parameters (s = string type for product_id)
                $stmt->bind_param("s", $product_id);

                // Execute the statement
                $stmt->execute();

                // Get the result
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<h2>Product Reviews</h2>";
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class = "review-container">';
                        echo '<div class = "review">';
                         echo '<span class="username">'.$row['username'].'</span>';

                        for($i = 0; $i < 5; $i++){
                          if($i < $row['review_score']){
                            echo '<img class="star" src = "../picture/star-full.png">';
                          }else{
                            echo '<img class="star" src = "../picture/star.png">';
                          }
                        }
                        echo '<span class="review-date">'.$row['review_date'].'</span>';
                        echo '</div>';
                        echo '<p class = "comment">'.$row['comment'].'</p>';
                        echo '</div>';

                    }
                }
                // Close statement
                $stmt->close();
            } 

        ?>
    

            <div id = "user_id" value = "
            <?php 
            
            if(!isset($_SESSION['user_id'])){
              echo 0;
            }else{
              echo 1;
            }
            ?>" 
            hidden></div>

        </div>
        <div class = "related-title">
            <h2>Related Product</h2>
        </div>
        <div class = "related-product">
          
              <?php
                shuffle($products);

                // Initialize an index to control how many products you want to display (e.g., 3)
                $index = 0;

                foreach ($products as $row) {
                    if ($index === 3) {
                        break; // Stop after displaying 3 products
                    }

                    if ($row['categories'] === $categories) {
                        $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
                        $price = number_format($row['price'], 2);
                        $productImages = $row['product_Image'];
                        $imageArray = explode(",", $productImages); // Assume images are comma-separated
                        $productId = $row['product_id'];

                        // Start of product container 
                        echo '<div class="r-product">';

                        // Create the image slider container
                        echo "<div class='slider-container'>";
                        echo "<a href='product-page.php?id=$productId' class='product-link'>";

                        // Create the images in the slider
                        foreach ($imageArray as $i => $image) {
                            $image = htmlspecialchars(trim($image), ENT_QUOTES, 'UTF-8');
                            echo "<img src='../upload/product_image/$image' alt='$productName' class='slider-image' data-index='$i' style='" . ($i == 0 ? "display: block;" : "display: none;") . "'>";
                        }

                        echo "</a>";
                        // Add the navigation buttons for the slider
                        echo "<button class='prev' onclick='prevImage(this)'>&#10094;</button>";
                        echo "<button class='next' onclick='nextImage(this)'>&#10095;</button>";

                        echo "</div>"; // End of slider container
                        echo "<a href='product-page.php?id=$productId' class='product-link'>";

                        // Product details
                        echo "<p>$productName</p>";
                        echo "<p>RM $price</p>";

                        echo "</div>"; // End of product item
                        echo "</a>";
                        $index++;
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
<?php include("../header-footer/footer.php");?>

