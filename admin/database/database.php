<?php

  require_once("../function/function.php");

  $host = 'localhost';
  $username = 'root';
  $password = '';
  $dbname = 'merchsystem';

  try{
    // Create a database connection
    $mysqli = new mysqli($host, $username, $password, $dbname);
    // echo"hi";
  }catch(mysqli_sql_exception $e) {
    // If the connection fails, log the error information to the log file
    error_log("Connection failed: " . $e->getMessage(). "\n", 3, "../var/log/app_errors.log");
    redirect("../errorpage/error.html");
    exit();
  }