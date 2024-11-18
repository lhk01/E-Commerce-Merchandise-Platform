<?php
session_start();
include("../database/database.php");



// Get refund request ID and action (approve or reject)
$refund_request_id = $_POST['refund_request_id'];
$action = $_POST['action'];

$status = '';
if ($action === 'approve') {
    $status = 'approved';
} elseif ($action === 'reject') {
    $status = 'rejected';
} else {
    $_SESSION['message'] = "Invalid action.";
    header("Location: admin_refund_requests.php");
    exit;
}

// Update refund request status
try {
    $sql = "UPDATE refund_requests SET status = ? WHERE refund_request_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $status, $refund_request_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Refund request has been " . ucfirst($status) . ".";
    } else {
        $_SESSION['message'] = "No changes made.";
    }

    header("Location: ../admin-pages/admin-refund.php");
    exit;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
