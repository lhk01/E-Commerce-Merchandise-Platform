<?php
  session_start();
  include("../database/database.php");
  require_once("../function/function.php");

  // Check if the user is logged in
  if (!isset($_SESSION["user_id"])) {
        redirect("../login/login.php");
        exit;
  }

  if(isset($_POST['product_id']) && !empty($_POST['product_id'])){
    $_SESSION['product_id'] = $_POST['product_id'];
    $_SESSION['order_id'] = $_POST['order_id'];
  }

  
  

   // Fetch order details
  try {
      // Fetch products in the order
      $product_id = $_SESSION['product_id'];
      $user_id = $_SESSION['user_id'];
      $sql = "SELECT * FROM products WHERE product_id = ?";
      $stmt = $mysqli->prepare($sql);
      $stmt->bind_param("s", $product_id);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();

      $images = explode(",", $result['product_Image']);

  } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle review submission
    if (isset($_POST['rating']) && isset($_POST['comment'])) {
        $review_score = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

        if ($review_score < 1 || $review_score > 5) {
            $_SESSION['message'] = "Review score must be between 1 and 5.";
            header("Location: review.php");
            exit;
        }

        if (empty($comment)) {
            $_SESSION['message'] = "Please write a comment.";
            header("Location: review.php");
            exit;
        }

        $user_id = $_SESSION['user_id']; // Replace with your session user ID
        $product_id = $_SESSION['product_id'];// Replace with your session product ID

        $stmt = $mysqli->prepare("INSERT INTO reviews (user_id, product_id, review_score, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $user_id, $product_id, $review_score, $comment);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Review submitted successfully!";
            unset($_SESSION['product_id'], $_SESSION['order_id']);
            header("Location: product-page.php?id=$product_id");
        } else {
            $_SESSION['message'] = "Review submitted unsuccessfully!";
            header("Location: review.php");
        }
        exit;
    }

    // Handle return button
    if (isset($_POST['return'])) {
        unset($_SESSION['product_id']);
        $order_id = $_SESSION['order_id'] ?? 0;
        unset($_SESSION['order_id']);
        header("Location: order-details.php?order_id=$order_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review</title>
  <link rel="stylesheet" href="../css/review.css">
  <style>
    /* Add the styles for the star rating here */
  
  </style>
</head>
<body>
    <div class="container">
        <div>
          <form action="" method="post">
            <button class="back-btn" name="return">Return</button>
          </form>
        </div>

        <div class="container-2">
          <div>
            <?php
              // Display product details
              echo '<div class="product-details">';
              echo "<img src='../upload/product_image/" . $images[0] . "'>";
              echo "<p>" . $result["productName"] . "</p>";
              echo '</div>';
            ?>
          </div>

          <form action="" method="POST">
              <div class="rating-container">
                <!-- Star rating -->
                <span onclick="selectRating(1)" class="star" data-value="1">★</span>
                <span onclick="selectRating(2)" class="star" data-value="2">★</span>
                <span onclick="selectRating(3)" class="star" data-value="3">★</span>
                <span onclick="selectRating(4)" class="star" data-value="4">★</span>
                <span onclick="selectRating(5)" class="star" data-value="5">★</span>
              </div>
              <input type="hidden" id="rating" name="rating" value="">
              <label for="comment">Comment:</label><br>
              <textarea id="comment" name="comment" rows="4" cols="50" placeholder="Write your review here..." required></textarea><br><br>
              <input type="submit" value="Submit Review">
          </form>
        </div>
    </div>

    <script>
        let stars = document.querySelectorAll(".star");
        let ratingInput = document.getElementById("rating");

        // Function to handle star click
        function selectRating(rating) {
            // Reset active class
            stars.forEach(star => star.classList.remove("active"));
            // Highlight stars up to the selected one
            for (let i = 0; i < rating; i++) {
                stars[i].classList.add("active");
            }
            // Update hidden input with rating
            ratingInput.value = rating;
        }
    </script>
</body>
</html>

<?php include("../header-footer/footer.php");?>