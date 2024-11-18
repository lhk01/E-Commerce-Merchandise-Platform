<?php

  session_start();
  require_once("../function/function.php");
  include("../database/database.php");

  if (!isset($_SESSION["user_id"])) {
        redirect("../login/login.php");
  }

  $order_id = $_GET['order_id'];

  $sql = "UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?";
  $stmt = $mysqli->prepare($sql);
  $stmt ->bind_param("i",$order_id);

  if(!$stmt->execute()){
        redirect("error.php");
  }else{
    redirect("order-details.php?order_id=$order_id");
  }