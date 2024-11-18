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
    <title>Notification List</title>
    <link rel="stylesheet" href="../css/notification.css"> 
</head>
<body>


<?php include '../function/fetchNotification.php'; ?> 

    <div class="main-content">
        <section class="notification-list-container">
            <h2>Notification List</h2>
   
            <table class="notification-list">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $row): ?>
                            <tr>
                                <td data-title="Type"><?php echo htmlspecialchars($row['type']); ?></td>
                                <td data-title="ID"><?php echo htmlspecialchars($row['id']); ?></td>
                                <td data-title="Message"><?php echo htmlspecialchars($row['message']); ?></td>
                                <td data-title="Date"><?php echo htmlspecialchars($row['date']); ?></td>
                                <td data-title="Action">
                                    <?php
                                    // Generate the appropriate link based on the type of notification
                                    if ($row['type'] == 'Order') {
                                        echo "<a href='adminViewOrder.php?order_id=" . $row['id'] . "' class='view-order'>View Order</a>";
                                    } elseif ($row['type'] == 'Refund') {
                                        echo "<a href='admin-refund.php?refund_id=" . $row['id'] . "' class='view-refund'>View Refund</a>";
                                    } elseif ($row['type'] == 'Review') {
                                        $productId = $row['message']; // Adjust extraction logic if needed
                                        echo "<a href='review.php?product_id=" . $row['product_id'] . "' class='view-review'>View Reviews</a>";
                                    } elseif ($row['type'] == 'Contact') {
                                        echo "<a href='contact.php?form_id=" . $row['id'] . "' class='view-contact'>View Contact</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No notifications found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
