<?php

function passwordValidate($password, $confirmPassword) {
    $isValid = true;

    // Check if the password is empty
    if (empty($password)) {
        $isValid = false;
        return $isValid;
    } else {
        // Check if the password length is at least 8 characters
        if (strlen($password) < 8) {
            $isValid = false;
            return $isValid;
        }

        // Check for at least one number in the password
        if (!preg_match('/\d/', $password)) {
            $isValid = false;
            return $isValid;
        }

        // Check for at least one lowercase letter in the password
        if (!preg_match("/[a-z]/", $password)) {
            $isValid = false;
            return $isValid;
        }

        // Check for at least one uppercase letter in the password
        if (!preg_match("/[A-Z]/", $password)) {
            $isValid = false;
            return $isValid;
        }

        // Check for at least one special character in the password
        if (!preg_match("/[\W_]/", $password)) {
            $isValid = false;
            return $isValid;
        }
    }

    // Check if the confirmation password is empty
    if (empty($confirmPassword)) {
        $isValid = false;
        return $isValid;
    }

    // Check if the password and confirmation password do not match
    if ($password !== $confirmPassword) {
        $isValid = false;
        return $isValid;
    }

    return $isValid;    

}

function checkIFEmpty($productName, $productPrice, $description, $categories, $stock, $sizeM,
$sizeX, $sizeXL) {
    
    $error = [];

    if(empty($productName)){
      $error[] = 'Warning: Please Enter Product Name.';   
    }
    
    if(empty($productPrice)){

      $error[] = 'Warning: Please Enter Product Price.';   
    }

    if(empty($description)){

      $error[] = 'Warning: Please Enter Product Description.'; 
    }
    
    if(empty($categories)){

      $error[] = 'Warning: Please Select Product Categories.'; 
    }

    // Check category and handle stock or size based on category
    if ($categories !== 'Apparel') { 
      if(empty($stock)){
         $error[] = 'Warning: Please Enter Stock Quantity.';
      }

    } else {   
      if(empty($sizeM)){

        $error[] = 'Warning: Please Enter Stock Quantity (Size M).';
      }
      
      if(empty($sizeX)){

        $error[] = 'Warning: Please Enter Stock Quantity (Size X).';
      }
      
      if(empty($sizeXL)){

        $error[] = 'Warning: Please Enter Stock Quantity (Size XL).';
      }
    }
    
    return $error;
}

