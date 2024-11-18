<?php
  session_start();

  include("../database/database.php");
  require_once("../function/function.php");


  try{
    if(isset($_GET["id"]) && !empty($_GET["id"])){
    
    $product_id = $_GET["id"];
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    
    $_SESSION['message'] = "Delete Successfully";
    redirect("adminProduct.php");

    }
  }catch (Exception $e){
    $_SESSION['message'] = "Delete Failed";
    redirect("adminProduct.php");
  }
