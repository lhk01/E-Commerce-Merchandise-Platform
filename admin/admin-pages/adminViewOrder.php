<?php
    session_start();
    include ("header.php");

    include("../database/database.php");

    if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }

    $order_id = $_GET['order_id'];
    $sql = 'SELECT proof_Image FROM orders WHERE order_id =?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $proof_images= "../../picture/image_unavailable.jpg";
    
    if ($row && $row['proof_Image'] !== null && $row['proof_Image'] !== 'empty') {
        $proof_images = $row['proof_Image'];
    }


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Preparation</title>
    <link rel="stylesheet" href="../css/adminViewOrder.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../javascript/adminViewOrder.js" defer></script>
</head>
<body>


    <div class="main-content">

        <button onclick="history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i>
        </button>

        <div class="main-content-box">
            <h1>Customer Order</h1>

            <div class="order-header">
                <div class="order-details">
                    <h2>Order Details</h2>
                    <strong>Order ID:</strong> <p id="order-id"></p>
                    <strong>Customer ID:</strong> <p id="customer-id"></p>
                    <strong>Customer Name:</strong> <p id="customer-name"></p>
                    <strong>Shipping Address:</strong> <p id="shipping-address"></p>
                    <strong>Contact Number:</strong> <p id="contact-number"></p>
                </div>

                <div class="shipping-status">
                    <h2>Current Status</h2>
                    <p id="shipping-status">Pending</p>
                </div>
            </div>

            <h2>Other Details</h2>
            <strong>Order Date:</strong> <p id="order-date"></p>
            <strong>Payment Method:</strong> <p id="payment-method"></p>
            <a href="javascript:void(0)" onclick="openModal()">
                <p>View Proof Image</p>
                <input hidden id = "prooft-image" value = '<?php echo htmlspecialchars($proof_images) ?>'>
            </a>

            <div id="myModal" class="modal">
                <span class="close" onclick="closeModal()">&times;</span>
                
                <img class="modal-content" id="modalImage">
            </div>

            <h2>Product Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Size</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">Loading...</td> <!-- Placeholder text before the dynamic content is fetched -->
                    </tr>
                </tbody>
            </table>

            <div class="status-update">
                <select id="order-status">
                    <option value="Pending Payment">Pending Payment</option>
                    <option value="Pending">Pending</option>
                    <option value="Shipping">Shipping</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <button class="button" id="submit-status">Submit</button>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            var modal = document.getElementById("myModal");
            var modalImage = document.getElementById("modalImage");
            const prooft_image= document.getElementById('prooft-image').value;
            
            // You can set the source of the image here, e.g.:
            modal.style.display = "block";
            modalImage.src = `../../upload/prooft/${prooft_image}`; // Set the image URL for the modal
            console.log(prooft_image);
        }

        // Close the modal
        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
</body>
</html>