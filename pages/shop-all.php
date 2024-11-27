<?php
  session_start();
  include("../database/database.php");
  include("../header-footer/header.php");
  $sql = "SELECT * FROM products WHERE active = 1";

  $stmt = $mysqli->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  

  // Fetch all the rows into an array first
  $products = [];
  while ($row = $result->fetch_assoc()) {
      $products[] = $row;
  }

  $sql = "SELECT sources, title, product_description, position, product_id FROM ads";
  $stmt = $mysqli->prepare($sql);

  // Execute the query
  $stmt->execute();

  // Get the result
  $result = $stmt->get_result();

  // Initialize an empty array to store the data
  $adsData = [];

  // Loop through all rows and store them in the array
  while ($data = $result->fetch_assoc()) {
      $adsData[] = $data;
  }


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop All</title>
  <link rel="stylesheet" href="../css/shop-all.css">
  <link href="https://fonts.googleapis.com/css2?family=Aladin&display=swap" rel="stylesheet">
</head>
<body>


  <div class = "container"> 
    <div class = "shop-all-container">
      <div class = "title">
        <h2>Shop All</h2>
        <p>Discover More Amazing Merchandise</p>
      </div>

      <div class="category-header">
        <div class="item">
            <a href="#apparel">
                <img src="../picture/apparel.png" alt="Apparel">
                <p>Apparel</p>
            </a>
        </div>
        <div class="item">
            <a href="#plush">
                <img src="../picture/ahri.png" alt="Plush">
                <p>Plush</p>
            </a>
        </div>
        <div class="item">
            <a href="#accessory">
                <img src="../picture/accessory.png" alt="Accessory">
                <p>Accessory</p>
            </a>
        </div>
    </div>


      <div class = "latest">
        <?php
          if (!empty($adsData)){
            foreach ($adsData as $ad) {
              if ($ad['position'] === "shop-all-latest-product") {
                  $title = $ad['title'];
                  $description = $ad['product_description'];
                 echo '<img src="../upload/shop-all/' . $ad['sources'] . '" />';

                }
            }
          }
        ?>
        <div class = "content">

          <h3><?php echo $title?></h3>
          <p>
          <?php echo $description?>
          </p>

        </div>
      </div>

      <span id="apparel"></span>

      <div class="category-p">
        <h2>Apparel</h2>
        <a href="product.php">View More</a>
      </div>

      <div class="product-container">
          <?php
          $index = 0;
          foreach ($products as $row) {
              if ($index === 3) {
                  break;
              }

              if ($row['categories'] === "Apparel") {
                   $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
                  $price = number_format($row['price'], 2);
                  $productImages = $row['product_Image'];
                  $imageArray = explode(",", $productImages); // Assume images are comma-separated
                  $productId = $row['product_id'];

                  // Start of product container
                  
                  echo '<div class="product">';

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
      
      <span id="plush"></span>

      <div class="category-p">
          <h2>Plush</h2>
          <a href="product.php">View More</a>
      </div>

      <div class="product-container">
          <?php
          $index = 0;
          foreach ($products as $row) {
              if ($index === 3) {
                  break;
              }

              if ($row['categories'] === "Plush") {
                   $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
                  $price = number_format($row['price'], 2);
                  $productImages = $row['product_Image'];
                  $imageArray = explode(",", $productImages); // Assume images are comma-separated
                  $productId = $row['product_id'];

                  // Start of product container
                  
                  echo '<div class="product">';

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
      
      <span id="accessory"></span>

      <div class="category-p">
          <h2>Accessory</h2>
          <a href="product.php">View More</a>
      </div>

      <div class="product-container">
          <?php
          $index = 0;
          foreach ($products as $row) {
              if ($index === 3) {
                  break;
              }
              
              if ($row['categories'] === "Accessory") {
                
                  $productName = htmlspecialchars($row['productName'], ENT_QUOTES, 'UTF-8');
                  $price = number_format($row['price'], 2);
                  $productImages = $row['product_Image'];
                  $imageArray = explode(",", $productImages); // Assume images are comma-separated
                  $productId = $row['product_id'];

                  // Start of product container
                  
                  echo '<div class="product">';

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
