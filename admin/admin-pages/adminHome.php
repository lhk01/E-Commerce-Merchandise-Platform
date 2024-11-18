<?php
session_start();
    require_once("../function/function.php");
    include ("header.php");
   include("../database/database.php");

    if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/adminHome.css"> 
    <script src="../javascript/adminDashboard.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
</head>
<body>

    <!-- Main Content Section -->
    <div class="main-content">
        <div class="header">
            <h2 class="sales-overview">Sales Overview</h2>
            <section class="download-report">
                <a href="downloadReport.php" class="download-button">Download Report</a>
            </section>
        </div>

        <!-- Dashboard Overview Section -->
        <section class="dashboard-overview">
            <div class="card">
                <h3>Total Sales</h3>
                <p id="totalSales">RM0.00</p> <!-- Placeholder for dynamic content -->
            </div>
            <div class="card">
                <h3>Total Orders</h3>
                <p id="totalOrders">0</p> <!-- Placeholder for dynamic content -->
            </div>
            <div class="card">
                <h3>Total Customers</h3>
                <p id="totalCustomers">0</p> <!-- Placeholder for dynamic content -->
            </div>
            <div class="card">
                <h3>Average Order</h3>
                <p id="averageOrder">RM0.00</p> <!-- Placeholder for dynamic content -->
            </div>
        </section>

        <!--line chart-->
        <section class="line-chart-section">
            <canvas id="salesChart"></canvas>
        </section>

        <!-- Recent Orders Section -->
        <section class="recent-orders">
            <h2>Recent Orders</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5">Loading recent orders...</td> <!-- Placeholder text before the dynamic content is fetched -->
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
