<?php
  session_start();
  include ("header.php");
  if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../css/adminAddProduct.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  
</head>
<body>

    <div class="main-content">

    

        <div class="main-content-box">
            <div class="title">
                <h2>Upload</h2>
            </div>
            
            <form action="../function/upload-and-edit.php" method="post" enctype="multipart/form-data">
                <div>
                    <div>
                        <label>Product Name</label><br>
                        <input type="text" class="item-details" name="product-name" required>
                    </div>

                    <div>
                        <label>Price</label><br>
                        <input type="text" class="item-details" name="product-price" required>
                    </div>

                    <div>
                        <label for="dropdown" id ="categories">Product Categories</label><br>
                        <select id="dropdown" name="dropdown" class="categories">
                            <option value="" disabled selected>Select categories</option>
                            <option value="Apparel">Apparel</option>
                            <option value="Plush">Plush</option>
                            <option value="Accessory">Accessory</option>
                        </select>
                    </div>  

                    <div class="hidden" style="display: none;">
                        <div>
                            <label for="numberInputM">Stock Size-M</label><br>
                            <input type="number" id="numberInputM" name="size-m" min="0" class="item-details">
                        </div>

                        <div>
                            <label for="numberInputX">Stock Size-L</label><br>
                            <input type="number" id="numberInputX" name="size-l" min="0" class="item-details">
                        </div>

                        <div>
                            <label for="numberInputXL">Stock Size-XL</label><br>
                            <input type="number" id="numberInputXL" name="size-xl" min="0" class="item-details">
                        </div>
                    </div>

                    <div class="stock" style="display: none;">
                        <div>
                            <label for="numberInput">Stock</label><br>
                            <input type="number" id="numberInput" name="stock" min="0" class="item-details">
                        </div>
                    </div>


                    <div>
                        <label>Description</label><br>
                        <textarea class="description" name="product-description" rows="4" cols="50"></textarea>
                    </div>

                    <div class="image-container">
                        <label for="input-file" id="drop-area">
                            <input type="file" accept="image/*" id="input-file" name="image[]" hidden required multiple>
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
                        <input type="submit" name="submit" class="submit-btn" value="Submit">
                    </div>

                </div>
            </form>
        </div>
    </div>      

    <script src="../javascript/upload-image.js"></script>
    <script src="../javascript/selectProduct.js"></script>  

</body>
</html>