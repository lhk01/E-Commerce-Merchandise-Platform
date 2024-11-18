<?php
  session_start();
  include ("header.php");
  include("../database/database.php");
  require_once("../function/function.php");

  if (!isset($_SESSION['admin_id'])) {
      redirect("../login/admin-login.php");
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get position from POST data
    $position = $_POST['position'];
    $targetDir = "";

   $sql = "UPDATE ads SET sources = ?, title = ?, product_description = ? ,product_id = ? WHERE position = ?";
   $stmt = $mysqli->prepare($sql);


    switch ($position) {

      case "homepage-video":
        // Target directory for uploads
        $targetDir = "../../upload/homepage-video/";

        // Ensure the upload directory exists
        if (!is_dir($targetDir)) {
          mkdir($targetDir, 0777, true);
        }

        // Access uploaded file information
        $file = $_FILES['video'];

        if ($file['error'] === UPLOAD_ERR_OK) {
          $fileName = basename($file['name']);
          $targetFile = $targetDir . $fileName;
          $null = null;

          // Move the uploaded file to the target directory
          if (move_uploaded_file($file['tmp_name'], $targetFile)) {
              $_SESSION['error'] = "Video uploaded successfully ";
              $stmt->bind_param("sssss", $fileName, $null, $null, $null,$position);
              $stmt->execute();
              redirect("admin-upload-website-item.php");
          } else {
              $_SESSION['error'] = "Failed to move the uploaded video.";
              redirect("admin-upload-website-item.php");
          }
        } else {
            $_SESSION['error'] = "File upload error: " . $file['error'];
            redirect("admin-upload-website-item.php");
        }
        break;

      case "homepage-image":

        $targetDir = "../../upload/homepage-image/";

        if (isset($_FILES['imageUpload'])) {
            $fileTmpPath = $_FILES['imageUpload']['tmp_name'];
            $fileName = basename($_FILES['imageUpload']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                // Generate a unique file name
                $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                $targetFilePath = $targetDir . $newFileName;

                // Move the file to the target directory
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    $title = htmlspecialchars($_POST['title']);
                    $description = htmlspecialchars($_POST['description']);
                    $product_id = htmlspecialchars($_POST['product_id']);

                    $stmt->bind_param("sssss", $newFileName, $title, $description, $product_id, $position);
                    $stmt->execute();
                    $_SESSION['error'] =  "Image uploaded successfully!<br>";
                    redirect("admin-upload-website-item.php");
                } else {
                    $_SESSION['error'] =  "Error moving the uploaded file.";
                    redirect("admin-upload-website-item.php");
                }
            } else {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
                redirect("admin-upload-website-item.php");
            }
        } else {
            $_SESSION['error'] =  "No file uploaded.";
            redirect("admin-upload-website-item.php");
        }

        break;
      
      case "homepage-slider":

        $targetDir = "../../upload/homepage-slider/";

        if (!empty($_FILES['imageSlider']['name'][0])) {
          $errors = []; // Array to collect errors for all files
          $imageArray = [];
          $success = true;

          foreach ($_FILES['imageSlider']['name'] as $key => $fileName) {
              $fileTmpPath = $_FILES['imageSlider']['tmp_name'][$key];
              $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
              $allowedExtensions = ['jpg', 'jpeg', 'png','avif'];

              if (in_array($fileExtension, $allowedExtensions)) {
                  // Generate a unique file name
                  $newFileName = uniqid('img_', true) . '.' . $fileExtension;
                  $targetFilePath = $targetDir . $newFileName;

                  // Move the file to the target directory
                  if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                      $imageArray[] = $newFileName;
                      $errors[] = "Uploaded: $fileName as $newFileName";
                  } else {
                      $errors[] = "Error uploading: $fileName";
                      $success = false;
                  }
              } else {
                  $errors[] = "Invalid file type for: $fileName. Only JPG, JPEG, PNG, and GIF are allowed.";
                  $success = false;
              }
          }

          if($success){
            $null = null;
            $imageArrayStr = implode(",", $imageArray);
            $stmt->bind_param("sssss", $imageArrayStr, $null, $productnull_description, $null,$position);
            $stmt->execute();
          }

          // Combine all errors into a single session message
          $_SESSION['error'] = implode("<br>", $errors);
          redirect("admin-upload-website-item.php");
      } else {
          $_SESSION['error'] = "No files selected for upload.";
          redirect("admin-upload-website-item.php");
      }

        break;


      case "shop-all-latest-product":


        $targetDir = "../../upload/shop-all/";

        if (isset($_FILES['shopAllImageUpload'])) { // Changed name
          $fileTmpPath = $_FILES['shopAllImageUpload']['tmp_name'];
          $fileName = basename($_FILES['shopAllImageUpload']['name']);
          $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

          if (in_array($fileExtension, $allowedExtensions)) {
              // Generate a unique file name
              $newFileName = uniqid('img_', true) . '.' . $fileExtension;
              $targetFilePath = $targetDir . $newFileName;

              // Move the file to the target directory
              if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                  $title = htmlspecialchars($_POST['shopAllTitle']); // Changed name
                  $description = htmlspecialchars($_POST['shopAllDescription']); // Changed name
                  $null = null;
                  
                  $stmt->bind_param("sssss", $newFileName, $title, $description, $null ,$position);
                  $stmt->execute();
                  $_SESSION['error'] =  "Image uploaded successfully!<br>";
                  redirect("admin-upload-website-item.php");
              } else {
                  $_SESSION['error'] =  "Error moving the uploaded file.";
                  redirect("admin-upload-website-item.php");
              }
          } else {
              $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
              redirect("admin-upload-website-item.php");
          }
        } else {
            $_SESSION['error'] =  "No file uploaded.";
            redirect("admin-upload-website-item.php");
        }


        break;

      default:
          $_SESSION['error'] = "Something went wrong please try again";
          redirect("admin-upload-website-item.php");
          break;
  }

} 

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel = "stylesheet" href = "../css/admin-upload-website-item.css">
</head>
<body>
  <div class = "container">
    <div class = "upload-container">
      <div class = "selection">
         <form action = "" method = "post" enctype="multipart/form-data" enctype="multipart/form-data">
        <select name = "position" id = 'position'>
          <option value="" disabled selected>Select</option>
          <option value = "homepage-video">Homepage Video</option>
          <option value = "homepage-image">Homepage Image</option>
          <option value = "homepage-slider">Homepage Image Slider</option>
          <option value = "shop-all-latest-product">Shop All Latest Product</option>
        </select>

        <div class = "video-container" id = 'video-container'>
          <div>
            <label for="videoUpload">Choose a video file:</label>
            <input type="file" id="videoUpload" name="video" accept="video/*">
          </div>

          <div>
            <video id="videoPreview" class="video-preview" controls hidden autoplay></video>
          </div>
        </div>

        <div class="homepage-image" id="homepage-image">
          <input type="file" name="imageUpload" id="imageUpload" accept="image/*"/>
          <img id="imagePreview" src="" alt="Preview">
          <div class="details">
              <label>Product id</label>
              <input type="text" name="product_id">
              <label>Title</label>
              <input type="text" name="title">
              <label>Description</label>
              <input type="text" name="description">
          </div>
        </div>

        <div class = "slider-container" id = "slider-container">
          <input type="file" name="imageSlider[]" id="image-slider" accept="image/*" multiple />
          <div id="image-slider-container">
          </div>
        </div>

        <div class = "shopall-image" id = 'shopall-image'>
          <input type="file" id="shopAllImageUpload" name="shopAllImageUpload" accept="image/*" /> 
          <img id="shopAllImagePreview" src="" alt="Preview">
          <div class="details">
              <label>Title</label>
              <input type="text" name="shopAllTitle">
              <label>Description</label>
              <input type="text" name="shopAllDescription">
          </div>
        </div>
      </div>
      
      <div class="homepage" id="homepage">
        <div class="home-video" id="homepage-video"></div>
        <div class="home-empty"> </div>
        <div class="home-slider" id = "home_slider"></div>
      </div>
     
      <div class="shop-all" id="shop-all">
        <div class="category">
          <div class="plush"></div>
          <div class="apparel"></div>
          <div class="accessory"></div>
        </div>
        <div class="shop-all-item" id="shop-all-item"></div>
      </div>

    </div>


  </div>
    <div class = "error-message">
      <?php
        if(isset($_SESSION['error']) && !empty($_SESSION['error'])){
          echo "<p>".$_SESSION['error']."</p>";
          unset($_SESSION['error']);
        }
      ?>
    </div>
    <div id = 'upload' class = "upload">
      <button >Upload</button>
    </div>
  </form>
  <script>

    const position = document.getElementById('position');
    const display_button = document.getElementById('upload');

    position.addEventListener('change', function(event) {
      const selected_option = event.target.value;
      const home = document.getElementById('homepage');
      const home_video = document.getElementById('homepage-video');
      const home_slider = document.getElementById('home_slider');
      const shop_all = document.getElementById('shop-all');
      const shop_all_item = document.getElementById('shop-all-item');
      const display_video = document.getElementById('video-container');
      const display_image = document.getElementById('homepage-image');
      const image_slider_container = document.getElementById('slider-container');
      const shop_all_image = document.getElementById('shopall-image');
      switch (selected_option) {
        case 'homepage-video':

          home.style.display = "block";
          shop_all.style.display = "none";
          home_video.style.borderColor = "blue";
          home_slider.style.borderColor = "black";
          display_video.style.display ="flex";
          display_image.style.display ="none";
          image_slider_container.style.display = "none";
          shop_all_image.style.display = "none";  
          break;
        

        case 'homepage-image':
           home.style.display = "block";
           shop_all.style.display = "none";
          home_video.style.borderColor = "blue";
          home_slider.style.borderColor = "black";
          display_video.style.display ="none";
          display_image.style.display ="flex";
          image_slider_container.style.display = "none";
          shop_all_image.style.display = "none";
          break;

        case 'homepage-slider':
          home.style.display = "block";
          shop_all.style.display = "none";
          home_slider.style.borderColor = "blue";
          home_video.style.borderColor = "black";
          display_video.style.display ="none";
          display_image.style.display ="none";
          image_slider_container.style.display = "flex";
          shop_all_image.style.display = "none";
          break;



        case 'shop-all-latest-product':
          home.style.display = "none";
          shop_all.style.display = "flex";
          shop_all_item.style.borderColor = "blue";
          display_video.style.display ="none";
          display_image.style.display ="none";
          image_slider_container.style.display = "none";
          shop_all_image.style.display = "flex";

          break;

        default:
          console.log('Unknown selection');
      }


    });

     const videoInput = document.getElementById('videoUpload');
    const videoPreview = document.getElementById('videoPreview');

    videoInput.addEventListener('change', function () {
      const file = this.files[0];

      if (file) {
        const videoURL = URL.createObjectURL(file);
        videoPreview.src = videoURL;
        videoPreview.hidden = false;
        display_button.style.display = 'flex';
      } else {
        videoPreview.hidden = true;
      }
    });

    const imageInput = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');

    imageInput.addEventListener('change', function () {
      const file = this.files[0]; // Get the uploaded file

      if (file) {
        const imageURL = URL.createObjectURL(file); // Create a URL for the uploaded file
        imagePreview.src = imageURL; // Set the image preview source
        imagePreview.style.display = 'block'; // Show the image
        display_button.style.display = 'flex';
      } else {
        imagePreview.style.display = 'none'; // Hide the image if no file is selected
      }
    });

    const image_slider = document.getElementById('image-slider');
    const imagePreviewContainer = document.getElementById('image-slider-container');

    image_slider.addEventListener('change', function () {
      const files = Array.from(this.files); // Get the uploaded files as an array

      if (files.length > 4) {
        alert('You can only upload up to 4 images. Please select 4 or fewer images.');
        this.value = ''; // Clear the input so the user can reselect
        imagePreviewContainer.innerHTML = ''; // Clear any existing previews
        return; // Stop further execution
      }

      imagePreviewContainer.innerHTML = ''; // Clear any existing previews

      files.slice(0, 4).forEach((file) => { // Limit to 4 files
        if (file) {
          const imageURL = URL.createObjectURL(file); // Create a URL for each file

          // Create an img element for the preview
          const imgElement = document.createElement('img');
          imgElement.src = imageURL;
          imgElement.alt = 'Preview';
          imgElement.style.maxWidth = '100px';
          imgElement.style.maxHeight = '100px';
          imgElement.style.objectFit = 'cover';
          imgElement.style.border = '1px solid #ccc';
          imgElement.style.borderRadius = '4px';
          display_button.style.display = 'flex';
          // Append the img to the container
          imagePreviewContainer.appendChild(imgElement);
        }
      });
    });

    const shopAllImageInput = document.getElementById('shopAllImageUpload');
const shopAllImagePreview = document.getElementById('shopAllImagePreview');

shopAllImageInput.addEventListener('change', function () {
  const file = this.files[0]; // Get the uploaded file

  if (file) {
    const imageURL = URL.createObjectURL(file); // Create a URL for the uploaded file
    shopAllImagePreview.src = imageURL; // Set the image preview source
    shopAllImagePreview.style.display = 'block'; // Show the image
    display_button.style.display = 'flex'; // Show the upload button
  } else {
    shopAllImagePreview.style.display = 'none'; // Hide the image if no file is selected
  }
});



  </script>
</body>
</html>