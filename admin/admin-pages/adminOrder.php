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
    <title>Orders - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/adminOrder.css">
    <script src="../javascript/adminOrder.js" defer></script>
</head>
<body>

        <div class="main-content">
            <section class="order-list">
                <h2>Order List</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">Loading orders...</td> <!-- Placeholder text before the dynamic content is fetched -->
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
    
</body>
</html>
