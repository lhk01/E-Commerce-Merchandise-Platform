<?php 
  session_start();
   include("../header-footer/header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/product.css">
  <script src="../javascript/filter-button.js"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <title>Apparel</title>
</head>
<body>
  

  <div class = "container">
    <!-- Filter container -->
    <div >
      <div class = "filter-container" id = "filter-container">
        <button id="filterBtn" class = "filter-btn"><img src = "../picture/filter.png" style = "width: 25px;"></button>
      </div>

      <div id="filterPanel" class="filter-panel">
        <div>
          <h2>Filter</h2>
          <button id="closeBtn"><i class="material-icons">&#xe5cd;</i></button>
        </div>

        <!-- Category Container -->
        <div class="checkbox-container">
          <div class="category">
              <h4>Category</h4>
              <div>
                  <!-- <label class="custom-checkbox">
                      <input type="checkbox" id="category-all"><p>All</p>
                      <span class="checkmark"></span>
                  </label> -->

                  <label class="custom-checkbox">
                      <input type="checkbox" id="category-apparel"><p>Apparel</p>
                      <span class="checkmark"></span>
                  </label>

                  <label class="custom-checkbox">
                      <input type="checkbox" id="category-plush"><p>Plush</p>
                      <span class="checkmark"></span>
                  </label>

                  <label class="custom-checkbox">
                      <input type="checkbox" id="category-accessory"><p>Accessory</p>
                      <span class="checkmark"></span>
                  </label>
              </div>
          </div>

          <!-- Sort by container -->
          <div class="sort-by">
              <h4>Sort by</h4>
              <div>
                  <label class="custom-checkbox">
                      <input type="checkbox" id="sort-high-to-low"><p>Price: High to Low</p>
                      <span class="checkmark"></span>
                  </label>

                  <label class="custom-checkbox">
                      <input type="checkbox" id="sort-low-to-high"><p>Price: Low to High</p>
                      <span class="checkmark"></span>
                  </label>
              </div>
          </div>

          <!-- Availability Container -->
          <div class="availability">
              <h4>Availability</h4>
              <div>
                  <label class="custom-checkbox">
                    <input type="checkbox" id="availability-in-stock"><p>In Stock</p>
                    <span class="checkmark"></span>
                  </label>

                  <label class="custom-checkbox">
                    <input type="checkbox" id="availability-out-of-stock"><p>Out of Stock</p>
                    <span class="checkmark"></span>
                  </label>
              </div>
          </div>
      </div>
    </div>
    </div>
    
    <div>
      <div class = "product-container">
        <div class= "product-grid" id = "product-container">
            
        </div>
        
        <button id = "load-more-btn" class = "load-more-btn" >Load More</button>
      </div>
    </div>
  </div>
  
  <script src="../javascript/load_more.js"></script>
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
</html>
<?php include("../header-footer/footer.php");?> 

