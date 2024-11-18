<?php
session_start();
include ("header.php");
include("../database/database.php");

if (!isset($_SESSION['admin_id'])) {
    redirect("../login/admin-login.php");
}

// Fetch all pending refund requests
try {
    $sql = "SELECT refund_requests.*, orders.user_id 
            FROM refund_requests 
            JOIN orders ON refund_requests.order_id = orders.order_id 
            ";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Manage Refund Requests</title>
    <link rel="stylesheet" href="../css/adminRefund.css">
    
</head>
<body>

    <div class="main-content">
        <section class="refund-list-container">
            <h2>Manage Refund Requests</h2>

            <?php
                if ($result->num_rows > 0) {
                    echo '<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
                    echo '<tr>';
                    echo '<th>Order ID</th>';
                    echo '<th>Reason</th>';
                    echo '<th>Status</th>';
                    echo '<th>Proof Images</th>';
                    echo '<th>Actions</th>';
                    echo '</tr>';

                    while ($row = $result->fetch_assoc()) {
                        $proofImages = explode(",", $row['proof_images']); // Get images as an array

                        echo '<tr>';
                        echo '<td data-title="Order ID" class = "orderID">' . $row['order_id'] . '</td>';
                        echo '<td data-title="Reason" class = "reason">' . htmlspecialchars($row['refund_reason']) . '</td>';
                        echo '<td data-title="Status" class = "status">' . ucfirst(htmlspecialchars($row['status'])) . '</td>';
                        
                        // Display the uploaded proof images in a single cell
                        echo '<td data-title="Proof Image">';
                        foreach ($proofImages as $image) {
                            echo '<img src="../../upload/refund_proofs/' . trim($image) . '" alt="Proof Image" style="width: 200px; height: auto; margin-left: 50px;">';
                        }
                        echo '</td>';

                        // Actions for approving or rejecting
                        echo '<td data-title="Actions">';
                        echo '<form action="../function/process_refund_request.php" method="POST" style="display:inline;">';
                        echo '<input type="hidden" name="refund_request_id" value="' . $row['refund_request_id'] . '">';
                        echo '<button type="submit" name="action" value="approve">Approve</button>';
                        echo '<button type="submit" name="action" value="reject">Reject</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }

                    echo '</table>';
                } else {
                    echo "<p>No pending refund requests.</p>";
                }
            ?>

        </section>
    </div>
</body>
</html>
