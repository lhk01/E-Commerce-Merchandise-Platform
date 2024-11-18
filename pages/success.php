<?php 
session_start();
 include("../header-footer/header.php"); 

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link rel="stylesheet" href="../css/success.css">
</head>
<body>
    <div class="container">
        <div class="success-message">
           <img src = "../picture/success.png" width="50px">
            <h1>Payment Successful!</h1>
            <p>Thank you for your purchase. Your payment has been processed successfully.</p>
            <a href="my-order.php" class="order-button">Track My Order</a>
        </div>
    </div>
</body>
</html>
<?php include("../header-footer/footer.php");?> 
