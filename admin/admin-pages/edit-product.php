<?php 
session_start();
include("../database/database.php");
require_once("../function/function.php");
include ("header.php");


if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

if(isset($_GET['id']) && !empty($_GET['id'])){
   $_SESSION['id'] = $_GET['id'];
}

if (!empty($_SESSION['id'])) {
    $get_product_Id = $_SESSION['id'];
    
    // Initialize variables
    $product_id = $productName = $categories = $price = $product_Image = $stock = 
    $size_M = $size_L = $size_XL = $product_description = 0;
    $categoryArray = ["Plush", "Apparel", "Accessory"];

    // Prepare the SQL query
    $sql = "SELECT product_id, productName, categories, price, product_image, stock, size_M, size_L, size_XL, product_description FROM products WHERE product_id = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $get_product_Id);  // Bind the product ID
        $stmt->execute();
        
        // Bind the result columns to PHP variables
        $stmt->bind_result($product_id, $productName, $categories, $price, $product_Image, $stock, $size_M, $size_L, $size_XL, $product_description);
        
        // Fetch the data and handle cases where the product is not found
        if ($stmt->fetch()) {
            // Convert product_image to array if there are multiple images
            $imageArray = (!empty($product_Image)) ? explode(",", $product_Image) : [];
        } else {
            // Redirect to error page if product is not found
            redirect("../errorpage/error.html");
            exit;
        }
        
        // Close the statement
        $stmt->close();
    } else {
        // Error handling if the SQL statement preparation fails
        redirect("../errorpage/error.html");
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../css/admin-upload.css">
  
</head>
<body>
  <div class="container">
    <div>
      <div class="title">
        <h2>Edit</h2>
      </div>
      
      <form action="../function/upload-and-edit.php" method="post" enctype="multipart/form-data">
        <div>
          <!-- product Id container -->
          <div class="product-id">
              <label>Product ID</label><br>
              <input type="text" class = "item-details" name="product_id" value="<?php echo htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8'); ?> ">
          </div>

          <div>
            <label>Product Name</label><br>
            <input type="text" name="product-name" class="item-details" value="<?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>"><br>
          </div>

          <div>
            <label>Price</label><br>
            <input type="text" name="product-price" class="item-details" value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"><br>
          </div>

          <div class="product-category">
                <label for="dropdown" id ="categories">Categories</label><br>
                <select id="dropdown" name="dropdown" class="categories">
                    <option value="<?php echo $categories; ?>"selected><?php echo htmlspecialchars($categories, ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php
                    // Loop through each category and create an <option> tag
                    foreach ($categoryArray as $category) {
                        $safeCategory = htmlspecialchars($category, ENT_QUOTES, 'UTF-8');

                        if($safeCategory != $categories){
                          echo "<option value='$safeCategory'>$safeCategory</option>";
                        }
                    }
                    ?>
                </select><br>
          </div>
            
          

          <div class="hidden" style="display: none;">
            <div>
              <label for="numberInputM">Stock Size-M</label><br>
              <input type="number" id="numberInputM" name="size-m" min="0" class="item-details" value="<?php echo htmlspecialchars($size_M, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div>
              <label for="numberInputX">Stock Size-X</label><br>
              <input type="number" id="numberInputX" name="size-l" min="0" class="item-details"  value="<?php echo htmlspecialchars($size_L, ENT_QUOTES, 'UTF-8'); ?>" >
            </div>

            <div>
              <label for="numberInputXL">Stock Size-XL</label><br>
              <input type="number" id="numberInputXL" name="size-xl" min="0" class="item-details" value="<?php echo htmlspecialchars($size_XL, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
          </div>

          <div class="stock" style="display: none;">
            <div>
              <label for="numberInput">Stock</label><br>
              <input type="number" id="numberInput" name="stock" min="0" class="item-details" 
              value="<?php echo (int)($categories !== "Apparel" ? $stock : 0); ?>">

            </div>
          </div>


          <div class="product-description">
            <label>Description</label><br>
            <textarea class="description" name="product-description" rows="10" cols="100">
              <?php echo htmlspecialchars($product_description, ENT_QUOTES, 'UTF-8'); ?>
            </textarea>
          </div>
          
        

          <div class="image-container">
            <label for="input-file" id="drop-area">
              <input type="file" accept="image/*" id="input-file" name="image[]" hidden multiple>
              <div id="img-view">
                <img src="../picture/upload.png" alt="Upload Icon" class="upload-pic">
                <p>Drag and Drop or click here<br>to upload images</p>
                <span>Upload multiple images</span>
              </div>
            </label>
          </div>

          <div>
            <p>Note: First Image Will be the Product Cover Photo</p>
          </div>

          <div>
            <?php
              if(!empty($_SESSION['message'])){
                foreach($_SESSION['message'] as $message){
                  echo "<p>{$message}</p>";
                }
                unset($_SESSION['message']);
              }
            ?>
          </div>

          <div class = "submit-container">
            <input type="submit" name="update" class="submit-btn" value="update">
          </div>

        </div>
      </form>
    </div>
  </div>
  <script src="../javascript/upload-image.js"></script>
  <script src="../javascript/selectproduct.js"></script>            
  
</body>
</html>