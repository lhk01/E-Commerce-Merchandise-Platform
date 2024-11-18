<?php 
    include("../database/database.php");
    require_once("../function/function.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito:wght@200;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
</head>
<body>
    <header>
        <!-- Logo -->
        <div class="logo">
            <a href = "../admin-pages/adminHome.php">
                <img src="../picture/logo.png" alt="Logo">
            </a>
        </div>
        
        <!-- Centered desktop-view container -->
        <div class="center-wrapper">
            <div class="desktop-view">
                <nav class="nav">
                    <ul>
                        <li><a href="../admin-pages/adminHome.php">Dashboard</a></li>
                        <li><a href="../admin-pages/adminProduct.php">Product</a></li>
                        <li><a href="../admin-pages/adminOrder.php">Orders</a></li>
                        <li><a href="../admin-pages/admin-refund.php">Refund</a></li>
                        <li><a href="../admin-pages/admin-voucher.php">Voucher</a></li>
                        <li><a href="../admin-pages/admin-upload-website-item.php">Ads</a></li>
                        <li><a href="../admin-pages/notification.php">Notifications</a></li>
                        <li><a href="../admin-pages/adminCustomer.php">Customer</a></li>
                        <li><a href="../admin-pages/adminSettings.php">Setting</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Mobile View: search-icon, cart, hamburger bar -->
        <div class="mobile-view">
            <div class="hamburger-menu" id="hamburger-menu">&#9776;</div>
        </div>
        
        <!-- Mobile Dropdown Menu (Initially hidden) -->
        <nav class="mobile-nav" id="mobile-nav">
            <ul>
              <li><a href="../admin-pages/adminHome.php">Dashboard</a></li>
              <li><a href="../admin-pages/adminProduct.php">Product</a></li>
              <li><a href="../admin-pages/adminOrder.php">Orders</a></li>
              <li><a href="../admin-pages/admin-refund.php">Refund</a></li>
              <li><a href="../admin-pages/admin-voucher.php">Voucher</a></li>
              <li><a href="../admin-pages/admin-upload-website-item.php">ads</a></li>
              <li><a href="../admin-pages/notification.php">Contact</a></li>
              <li><a href="../admin-pages/adminCustomer.php">Customer</a></li>
              <li><a href="../admin-pages/adminSettings.php">Setting</a></li>
              <li><a href="#">Logout</a></li>
            </ul>
        </nav>
    </header>
    <script src="../javascript/header.js"></script>
</body>
</html>
