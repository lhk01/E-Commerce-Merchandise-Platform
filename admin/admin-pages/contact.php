<?php
session_start();
include("../admin-pages/header.php");

if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact</title>
  <link rel="stylesheet" href="../css/adminContact.css"> 
</head>
<body>

    <div class="main-content">
        <div class="product-list">
            <h1>Contact List</h1>
          
            <div class="product-table">
                <?php
                // Include PHP logic to fetch contact data
                include("../function/fetchContactData.php");
                ?>
            </div>
        </div>
    </div>

</body>
</html>
