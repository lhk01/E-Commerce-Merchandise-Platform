<?php
  session_start();
  include("../header-footer/header.php");
  include("../database/database.php"); 

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
    <link rel="stylesheet" href="../css/homepage.css">
    <script src = "../javascript/fetch_top_products.js"></script>
    <title>Document</title>
</head>
<body>

  <div class="container">
      <div class="videoContainer">
          <video id="myVideo" autoplay muted>

              <?php
                if (!empty($adsData)){
                  foreach ($adsData as $ad) {
                    if ($ad['position'] === "homepage-video") {
                        echo '<source src="../upload/homepage-video/' . $ad['sources'] . '" type="video/mp4">';
                      }
                  }
                }
              ?>
          </video>
          <?php
            if (!empty($adsData)){
              foreach ($adsData as $ad) {
                if ($ad['position'] === "homepage-image") {
                   $title =  $ad['title'];
                   $description =  $ad['product_description'];
                   $id =  $ad['product_id'];
                    echo '<img id="image" src="../upload/homepage-image/' . $ad['sources'] . '" alt="End Image">';
                  }
              }
            }
          ?>
        
          
          <div class="text-overlay">
              <h2 class="title"><?php echo $title ?></h2>
              <h3 class="description"><?php echo $description ?></h3>
              <a href="product-page.php?id=<?php echo $id; ?>">
              <button class="btn">SHOP NOW</button>
              </a>
          </div>
      </div>
  </div>

  <div class="container-2">
      <div class="monthly-pick-container">
          <div class="monthly-pick-title">
              <h2>This Month's Pick</h2>
          </div>
      </div>

      <div class="product-container" id="product-container"></div>
        
      <div class="slider-container">
    <div class="slider-images">
      <?php
        if (!empty($adsData)){
          foreach ($adsData as $ad) {
            if ($ad['position'] === "homepage-slider") {
                // Split the sources string into an array
                $imageSources = explode(',', $ad['sources']);
                
                // Loop through the array and print images with incremented names
                for ($i = 0; $i < count($imageSources); $i++) {
                    $imageName = 'Image ' . ($i + 1); // Increment image number
                    echo '<img  src="../upload/homepage-slider/' . $imageSources[$i] . '" alt="' . $imageName . '">';
                }
            }
        }
      }
      ?>

      <!-- Add more images as needed -->
    </div>
    <div class="dots-container">
      <!-- Dots will be dynamically added here -->
    </div>
    <button class="prev-slide">&#10094;</button>
    <button class="next-slide">&#10095;</button>
  </div>
      </div>


  </div>

<script>
    const images = document.querySelectorAll('.slider-images img');
const dotsContainer = document.querySelector('.dots-container');
const prevButton = document.querySelector('.prev-slide');
const nextButton = document.querySelector('.next-slide');

let currentIndex = 0;

// Create dots dynamically based on the number of images
images.forEach((image, index) => {
  const dot = document.createElement('span');
  dot.addEventListener('click', () => goToImage(index));
  dotsContainer.appendChild(dot);
});

// Update the active dot
function updateDots() {
  const dots = document.querySelectorAll('.dots-container span');
  dots.forEach((dot, index) => {
    if (index === currentIndex) {
      dot.classList.add('active');
    } else {
      dot.classList.remove('active');
    }
  });
}

// Go to a specific image (based on clicked dot)
function goToImage(index) {
  currentIndex = index;
  updateSlider();
}

// Update the slider to show the current image
function updateSlider() {
  const newTransformValue = `translateX(-${currentIndex * 100}%)`;
  document.querySelector('.slider-images').style.transform = newTransformValue;
  updateDots();
}

// Initialize the first dot as active
updateDots();

// Next Image
nextButton.addEventListener('click', () => {
  currentIndex = (currentIndex + 1) % images.length;
  updateSlider();
});

// Previous Image
prevButton.addEventListener('click', () => {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  updateSlider();
});

// Initialize the first image
updateSlider();


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
const video = document.getElementById('myVideo');
    const image = document.getElementById('image');
    const overlay = document.querySelector('.text-overlay');

    // Event listener for video end
    video.onended = function() {
        video.style.opacity = '0'; // Fade out the video
        image.style.opacity = '1'; // Fade in the image
        overlay.style.opacity = '1'; // Fade in the overlay content
    };
</script>

</body>
</html>
<?php include("../header-footer/footer.php");?>
