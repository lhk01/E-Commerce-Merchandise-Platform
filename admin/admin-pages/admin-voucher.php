<?php
session_start();
include ("header.php");
include("../database/database.php");
require_once("../function/function.php");

if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

// Function to generate a random alphanumeric voucher code
function generateVoucherCode($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomCode = '';
    for ($i = 0; $i < $length; $i++) {
        $randomCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomCode;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get discount and expiration period from form
    $discount = $_POST['discount'];
    $expirationPeriod = $_POST['expiration_period'];

    // Calculate the expiration date based on the selected period
    $expirationDate = date('Y-m-d', strtotime("+$expirationPeriod week"));

    // Generate voucher code
    $voucherCode = generateVoucherCode(10);

    $voucher_name = "OFFER" . $discount;

    // Insert into the vouchers table
    $stmt = $mysqli->prepare("INSERT INTO vouchers (voucher_name, voucher_code, discount, expiration_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $voucher_name, $voucherCode, $discount, $expirationDate);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Voucher added successfully! Voucher Code: ";
        redirect("admin-voucher.php");
    } else {
        $_SESSION['msg'] = "Error: " . $stmt->error;
        redirect("admin-voucher.php");
    }
    $stmt->close();
}

// Delete voucher if delete request is made
if (isset($_GET['delete_id'])) {
    $voucherId = $_GET['delete_id'];

    $stmt = $mysqli->prepare("DELETE FROM vouchers WHERE voucher_id = ?");
    $stmt->bind_param("i", $voucherId);

    if ($stmt->execute()) {
        $_SESSION['msg'] = "Voucher deleted successfully!";
    } else {
        $_SESSION['msg'] = "Error deleting voucher: " . $stmt->error;
    }
    $stmt->close();
    redirect("admin-voucher.php");
}

// Fetch current vouchers from the database
$result = $mysqli->query("SELECT * FROM vouchers ORDER BY expiration_date DESC");
?>

<!-- Simple HTML form to add voucher -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Voucher</title>
    <link rel="stylesheet" href="../css/admin-voucher.css">
    <script>
        function confirmDelete(voucherId) {
            var result = confirm("Are you sure you want to delete this voucher?");
            if (result) {
                window.location.href = "admin-voucher.php?delete_id=" + voucherId;
            }
        }
    </script>
</head>
<body>

    <div class = "container">
        <div class="form-container">
            <h2>Add Voucher</h2>
            <form method="POST">
                <label for="discount">Discount Amount:</label>
                <input type="number" step="0.01" name="discount" id="discount" required><br><br>

                <label for="expiration_period">Expiration Period:</label>
                <select name="expiration_period" id="expiration_period" required>
                    <option value="1">1 Week</option>
                    <option value="2">2 Weeks</option>
                    <option value="3">3 Weeks</option>
                    <option value="4">4 Weeks</option>
                </select><br><br>

                <?php
                    if (isset($_SESSION['msg']) && !empty($_SESSION['msg'])) {
                        echo "<p>" . $_SESSION['msg'] . "</p>";
                        unset($_SESSION['msg']);
                    }
                ?>
                <button type="submit">Add Voucher</button>
            </form>
        </div>

        <!-- Table to display current vouchers -->
        <div class="voucher-table">
            <h2>Current Vouchers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Voucher ID</th>
                        <th>Voucher Name</th>
                        <th>Voucher Code</th>
                        <th>Discount</th>
                        <th>Expiration Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row['voucher_id']) . "</td>
                                        <td>" . htmlspecialchars($row['voucher_name']) . "</td>
                                        <td>" . htmlspecialchars($row['voucher_code']) . "</td>
                                        <td>RM " . htmlspecialchars($row['discount']) . "</td>
                                        <td>" . htmlspecialchars($row['expiration_date']) . "</td>
                                        <td><a href=\"javascript:void(0);\" onclick=\"confirmDelete(" . $row['voucher_id'] . ")\">Delete</a></td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No vouchers available</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
