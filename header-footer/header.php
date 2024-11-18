<?php 
    include("../database/database.php");
    require_once("../function/function.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href = '../css/header.css'>
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="../javascript/add-to-bag.js" defer></script>
</head>
<body>
    <header>
        <!-- Logo -->
        <div class="logo">
            <a href = "../pages/homepage.php">
                <img src="../picture/logo.png" alt="Logo">
            </a>
        </div>
        <!-- Centered desktop-view container -->
        <div class="center-wrapper">
            <div class="desktop-view">
                <nav class="nav">
                    <ul>
                        <li><a href="../pages/shop-all.php">Shop All</a></li>
                        <li><a href="../pages/about-us.php">About Us</a></li>
                        <li><a href="../pages/contact-us.php">Contact Us</a></li>
                    </ul>
                </nav>
                <div class="right-side">
                    <form class="desktop-search-form" action="../pages/search.php" method="post">
                      <input type="text" name="search" class="search-box" placeholder="Search...">
                    </form>

                    <?php
                        if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])){
                          $user_id = $_SESSION["user_id"];
                          $sql = "SELECT username from users WHERE id = ?";
                          $stmt = $mysqli->prepare($sql);
                          $stmt->bind_param("i", $user_id);
                          $stmt->execute();
                          $stmt->store_result();
                          if ($stmt->num_rows > 0){
                              $stmt->bind_result($username);
                              $stmt->fetch();
                              $username = (strlen($username) > 10) ? substr($username, 0, 10) . '...' : $username;
                          }

                          echo "<a class='account'>$username</a>";
                          echo '<ul class="account-dropdown">';
                          echo '<li><a href="../pages/my-account.php">My Account</a></li>';
                          echo '<li><a href="../pages/my-order.php">My Orders</a></li>';
                          echo '<li><a href="../pages/return-page.php">My Returns</a></li>';
                          echo '<li><a href="../function/logout.php">Logout</a></li>';
                          echo '</ul>';

                      } else {
                          echo '<a href="../login/login.php" class="account">Sign In</a>';
                      }
                    ?>
                    <a href="../pages/bag.php" class="cart">
                      <i class="fa fa-shopping-bag" style="font-size:24px"></i>
                      <div class="cart-quantity">0</div>
                  </a>
                </div>
            </div>
        </div>
        <!-- Mobile View: search-icon, cart, hamburger bar -->
        <div class="mobile-view">
            <div class="search-icon" id="search-icon" style = "font-size: 20px"><i class="fa fa-search"></i></div>
            <a href="../pages/bag.php" class="cart"><i class="fa fa-shopping-bag" style="font-size:22px"></i></a>
            <div class="cart-quantity-mobile">0</div>
            
           
           <?php

              if (isset($_SESSION["user_id"]) && !empty($_SESSION["user_id"])){
                
                echo "<a class='account' id='mobile-account'>$username</a>"; 
                echo '<ul class="account-dropdown" id="mobile-account-dropdown">';
                echo '<li><a href="../pages/my-account.php">My Account</a></li>';
                echo '<li><a href="../pages/my-order.php">My Orders</a></li>';
                echo '<li><a href="../pages/return-page.php">My Returns</a></li>';
                echo '<li><a href="../function/logout.php">Logout</a></li>';
                echo '</ul>';
              }else{
                echo '<a href="../login/login.php" class="account">Sign In</a>';
              }

            ?>
            <div class="hamburger-menu" id="hamburger-menu">&#9776;</div>
        </div>
        <!-- Mobile Dropdown Search (Initially hidden) -->
        <div class="search-dropdown" id="search-dropdown">
            <form class="desktop-search-form" action="../pages/search.php" method="post">
              <input type="text" name="query" class="search-box" placeholder="Search...">
            </form>
        </div>
    
        <!-- Mobile Dropdown Menu (Initially hidden) -->
        <nav class="mobile-nav" id="mobile-nav">
            <ul>
                <li><a href="../pages/shop-all.php">Shop All</a></li>
                <li><a href="../pages/about-us.php">About Us</a></li>
                <li><a href="../pages/contact-us.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>
    <script src="../javascript/header.js"></script>

</body>
</html>
