<?php

  include("../database/database.php");

  $sql = "SELECT 
        voucher_name,
        voucher_code,
        discount
        FROM vouchers
        WHERE expiration_date >= CURDATE()";

  $result = $mysqli->query($sql);

  $vocher = [];

  if($result->num_rows > 0){
    while($row = $result -> fetch_assoc()){
      $vocher[] = $row;
    }
  }

  header('Content-Type: application/json');
  echo json_encode($vocher);

