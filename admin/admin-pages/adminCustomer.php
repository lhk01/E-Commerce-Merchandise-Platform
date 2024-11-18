<?php
    session_start();
    include ("header.php");
    if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Customer</title>
    <link rel="stylesheet" href="../css/adminCustomer.css">
    <script src="../javascript/adminCustomer.js" defer></script>
</head>
<body>

        <div class="main-content">
            <section class="customer-list">
                <h2>Customer List</h2>

                <table id="customerTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email Address</th>
                            <th>Verify</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5">Loading users...</td> <!-- Placeholder text before the dynamic content is fetched -->
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    
</body>
</html>