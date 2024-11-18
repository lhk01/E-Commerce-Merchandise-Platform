<?php
  session_start();
  require_once("../function/function.php");
  include("../database/database.php");

  if (!isset($_SESSION["user_id"])) {
        redirect("../login/login.php");
  }
 

  if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $order_id = $_GET['order_id'];
    
    $payment_method = isset($_POST['payment-method'])?$_POST['payment-method']:'';

      $sql = "UPDATE orders SET payment_method = ? WHERE order_id = ?";
      $stmt = $mysqli->prepare($sql);
      $stmt ->bind_param('si',$payment_method,$order_id);

      if(!$stmt->execute()){
        redirect("error.php");
      }

    if($payment_method === 'Paypal' || $payment_method === 'Credit Card'){
        echo "<script>
            var newWindow = window.open('https://www.paypal.com/ncp/payment/Z2XA6E59AMJY4', '_blank');
            window.location.href = 'upload-prooft-image.php?order_id=$order_id';
        </script>";
        
    }   

    if($payment_method === 'Cash on Delivery'){
        redirect("success.php");
    }

    if ($payment_method === 'Touch and Go') {
        echo "<script>
                var newWindow = window.open('https://payment.tngdigital.com.my/sc/bDLnY21sCS', '_blank');
                window.location.href = 'upload-prooft-image.php?order_id=$order_id';
            </script>";
    }
  }


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
  <div class = "container">
    <form action = "" method = "post">
      <div class = "payment-container">
        <h3>Payment</h3>
        <p>All transactions are secure and encrypted.</p>
        
        <div class = "payment-method-top">
            <input class = "checkbox" type="checkbox" id="touch-n-go" name="payment-method" value="Touch and Go" onclick="handleCheckboxClick(this)">
            <label class = "payment-method-name" for="touch-n-go">Touch'n Go</label><br>
        </div>



        <div class = "payment-method-mid">
            <input class = "checkbox" type="checkbox" id="credit-card" name="payment-method" value="Credit card" onclick="handleCheckboxClick(this)">
            <label class = "payment-method-name" for="credit-card">Credit card</label><br>
        </div>

        <div class = "payment-method-mid">
            <input class = "checkbox" type="checkbox" id="paypal" name="payment-method" value="Paypal" onclick="handleCheckboxClick(this)">
            <label class = "payment-method-name" for="paypal">PayPal</label><br>
        </div>

        <div class = "payment-method-bottom">
            <input class = "checkbox" type="checkbox" id="cash_on_delivery" name="payment-method" value="Cash on Delivery" onclick="handleCheckboxClick(this)">
            <label class = "payment-method-name" for="'cash_on_delivery">Cash on Delivery</label><br>
        </div>
      </div>
      <button class = 'pay-now'>PAY NOW</button>
    </form>
  </div>
  <script src = "../javascript/handle-checkbox.js"></script>
</body>
</html> 