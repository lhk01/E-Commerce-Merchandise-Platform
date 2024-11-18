<?php

    session_start();
    // include("../header-footer/header.php");
    include("../database/database.php");
    include("../phpMailer/receipt.php");

    // Check again if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        redirect("../login/login.php");
    }

    $user_id = $_SESSION["user_id"];
    
    $_SESSION['total'] = 0;
    // Store results in an array for reuse
    
    try{
        // Fetch cart items
        $sql = "SELECT bag.*, products.product_Image, products.productName, products.price, products.categories 
                FROM bag 
                JOIN products ON bag.product_id = products.product_id 
                WHERE bag.user_id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stmt = $mysqli->prepare("SELECT voucher_code, discount FROM vouchers WHERE expiration_date >= CURDATE()");
        $stmt->execute();
        $availableVouchers = $stmt->get_result();

        // Store results in an array for reuse
        $cart_items = [];
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
            
            if ($row['categories'] === 'Apparel') {
                $sizes = [
                    'M' => $row['size_M'], 
                    'L' => $row['size_L'], 
                    'XL' => $row['size_XL']
                ];
                
                foreach ($sizes as $size => $quantity) {
                    if (!empty($quantity)) {
                        $subtotal = $row['price'] * $quantity;
                        $_SESSION['total'] += $subtotal;
                    }
                }

            } else {
                $subtotal = $row['price'] * $row['quantity'];
                $_SESSION['total'] += $subtotal;
            }
        }
    }catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['pay-now'])) {

        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_SPECIAL_CHARS);  
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);  
        $full_name = $first_name . " ". $last_name;
        
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);  
        $state = isset($_POST['state'])?$_POST['state']: '';
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS);  
        $zip_code = filter_input(INPUT_POST, 'zip-code', FILTER_SANITIZE_NUMBER_INT);  
        $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_SPECIAL_CHARS);  
        $full_address = $address . " " . $city. " ". $zip_code. " ".$state;
        
        $payment_method = isset($_POST['payment-method'])?$_POST['payment-method']:'';
        $total = $_SESSION['total'] + 5;

       
        
        if(!empty($first_name) && !empty($last_name)&& !empty($address) && !empty($state) && 
        !empty($city) && !empty($zip_code) && !empty($phone_number)){
            date_default_timezone_set('Asia/Kuala_Lumpur');
            $currentDateTime = date('Y-m-d H:i:s');


            $voucher = $_POST['voucher'] ?? 0;
            $vocher_sql = "SELECT voucher_id FROM vouchers WHERE discount = ?";
            $vocher_stmt = $mysqli->prepare($vocher_sql);
            $vocher_stmt ->bind_param('i',$voucher);
            $vocher_stmt->execute();
            $vocher_stmt->bind_result($voucher_id);
            $vocher_stmt->fetch();

            $total -= $voucher;
            $vocher_stmt->close();
            
            $payment_status = "Payment Pending";
            $proof_images = null;
            if($payment_method === "Cash on Delivery"){
                $payment_status = "Pending";
                $proof_images = "empty";
            }

            $order_sql = "INSERT INTO orders (user_id, fullname, phone_number, order_date, total_price, shipping_address, order_status, payment_method, voucher_id, proof_Image) 
              VALUES (?, ?, ?, ?, ?, ?, '$payment_status', ?, ?,?)";

            $order_stmt = $mysqli->prepare($order_sql);
            $order_stmt->bind_param("isssdssss", $user_id, $full_name, $phone_number, $currentDateTime, $total, $full_address, $payment_method, $voucher_id,$proof_images);
            $order_stmt->execute();
            $order_id = $mysqli->insert_id;
            
            foreach ($cart_items as $row) {
                if ($row['categories'] === 'Apparel') {
                    $sizes = [
                        'M' => $row['size_M'], 
                        'L' => $row['size_L'], 
                        'XL' => $row['size_XL']
                    ];

                    foreach ($sizes as $size => $quantity) {
                        if (!empty($quantity)) {
                            // Initialize size variables
                            $size_M = NULL;
                            $size_L = NULL;
                            $size_XL = NULL;

                            // Assign the correct size
                            if ($size === 'M') {
                                $size_M = $quantity;
                            } elseif ($size === 'L') {
                                $size_L = $quantity;
                            } elseif ($size === 'XL') {
                                $size_XL = $quantity;
                            }

                            $quantity = null;


                            // Insert into order_items
                            $item_sql = "INSERT INTO order_items (order_id, product_id, price, quantity, size_M, size_L, size_XL)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                            $item_stmt = $mysqli->prepare($item_sql);
                            $item_stmt->bind_param("isdiiii", $order_id, $row['product_id'], $row['price'], $quantity, 
                                                $size_M, $size_L, $size_XL);
                            $item_stmt->execute();

                            $size_M = isset($size_M) ? $size_M : 0;
                            $size_L = isset($size_L) ? $size_L : 0;
                            $size_XL = isset($size_XL) ? $size_XL : 0;
                            // Update stock
                            $update_stock_sql = "UPDATE products SET size_M = size_M - ?, size_L = size_L - ?, size_XL = size_XL - ? 
                                                WHERE product_id = ?";
                            $update_stock_stmt = $mysqli->prepare($update_stock_sql);
                            $update_stock_stmt->bind_param("iiis", $size_M, $size_L, $size_XL, $row['product_id']);
                            $update_stock_stmt->execute();

                            $size_M = isset($size_M) && $size_M != 0 ? $size_M : null;
                            $size_L = isset($size_L) && $size_L != 0 ? $size_L : null;
                            $size_XL = isset($size_XL) && $size_XL != 0 ? $size_XL : null;

                            $items[] = [
                                'name' => $row['productName'],
                                'amount' => $row['price'],
                                'quantity' => 0,
                                'category' => $row['categories'],
                                'size_m' => $size_M,
                                'size_l' => $size_L,
                                'size_xl' => $size_XL
                            ];
                        }
                    }

                } else {    
                    $item_sql = "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
                    $item_stmt = $mysqli->prepare($item_sql);
                    $item_stmt->bind_param("isdi", $order_id, $row['product_id'], $row['price'], $row['quantity']);
                    $item_stmt->execute();

                    // Update stock for non-apparel products
                    $update_stock_sql = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                    $update_stock_stmt = $mysqli->prepare($update_stock_sql);
                    $update_stock_stmt->bind_param("is", $row['quantity'], $row['product_id']);
                    $update_stock_stmt->execute();

                    $items[] = [
                        'name' => $row['productName'],
                        'amount' => $row['price'],
                        'quantity' => $row['quantity'],
                        'category' => $row['categories'],
                        'size_m' => $size_M,
                        'size_l' => $size_L,
                        'size_xl' => $size_XL
                    ];
                }
            }

            // Clear bag after order confirmation
            $clear_bag_sql = "DELETE FROM bag WHERE user_id = ?";
            $clear_bag_stmt = $mysqli->prepare($clear_bag_sql);
            $clear_bag_stmt->bind_param("i", $user_id);
            $clear_bag_stmt->execute();

            
            


            $email_sql = "SELECT email_address FROM users WHERE id = ?";
            $email = $mysqli->prepare($email_sql);
            $email->bind_param("i", $user_id);  
            $email->execute();  
            $email->bind_result($email_address);  
            $email->fetch();

            $title = "Receipt";
            $subject = "Product Receipt";
            $date = date("d/m/y");


            mailerReceipt($email_address,$title,$subject,$items,$total,$payment_method,$order_id, $date,$voucher);
            
            unset($_SESSION['total']);

            if($payment_method === 'Paypal' || $payment_method === 'Credit Card'){
                echo "<script>
                    var newWindow = window.open('https://www.paypal.com/ncp/payment/Z2XA6E59AMJY4', '_blank');
                    window.location.href = 'upload-prooft-image.php?order_id=$order_id';
                </script>";
               
            }   

            if($payment_method === 'Cash on Delivery'){
                redirect("success.php");
            }

            if ($payment_method === 'Touch and Go') {
                echo "<script>
                        var newWindow = window.open('https://payment.tngdigital.com.my/sc/bDLnY21sCS', '_blank');
                        window.location.href = 'upload-prooft-image.php?order_id=$order_id';
                    </script>";
            }




            // // Redirect to a success page
            // redirect("upload-prooft-image.php?order_id=$order_id");

        }else{
            echo "ur mom";
        }    
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="stylesheet" href="../css/input-container.css">
    <script src="../javascript/vocher.js" defer></script>
</head>
<body>
    <div class = "container">
        <div class = "product-container">
            <div class="ordered-product">
                <div class = "title">
                    <h3>Order Summary</h3>
                </div>
                <?php
                $total = 0; // Initialize total

                foreach ($cart_items as $row) {
                    $images = explode(",", $row['product_Image']);
                    $productTotal = 0;

                    echo "<div class='product-details'>";
                    
                    // Display product image
                    echo "<div class='product-image'>";
                    echo "<img src='../upload/product_image/".$images[0]."' alt='".$row['productName']."'>";
                    echo "</div>";

                    // Display product name, size, and quantity
                    echo "<div class='product-info'>";

                    if($row['categories'] === 'Apparel') {
                        $sizes = [
                            'M' => $row['size_M'], 
                            'L' => $row['size_L'], 
                            'XL' => $row['size_XL']
                        ];
                      
                        foreach ($sizes as $size => $quantity) {
                            if (!empty($quantity)) {
                                echo "<p class='product-name'>X ".$quantity." ".$row['productName']."</p>";
                                echo "<p class='product-size'>Size: ".$size."</p>";
                                
                                $productTotal += $quantity * $row['price'];
                            }
                        }
                        
                    } else {
                        $productTotal = $row['quantity'] * $row['price'];
                        echo "<p class='product-name'>X ".$row['quantity']." ".$row['productName']."</p>";
                    }

                    echo "</div>";
                    echo "<div class='product-price'>RM ".number_format($productTotal, 2)."</div>";
                    echo "</div>";

                    $total += $productTotal;
                }
                
                // Display subtotal
                echo "<div class='subtotal'>";
                echo "<p>Subtotal</p>";
                echo "<p>RM ".number_format($total, 2)."</p>";
                echo "</div>";

                echo "<div class='subtotal'>";
                echo "<p>Shipping</p>";
                echo "<p>RM 5</p>";
                echo "</div>";

                echo "<div class='discount-price' id = 'price' hidden></div>";
                

            

                echo "<div class='subtotal'>";
                echo "<p>Subtotal</p>";
                echo "<p id = 'total' value = '".($total+5)."'>RM ".number_format(($total + 5), 2)."</p>";
                echo "</div>";
                
                ?>
            </div>

        </div>
        <form action = '' method ='post'>
            <div class = "information">
                <h3>Contact information</h3>
                <div class = "username-container">
                    <div class="input-container">
                        <input type="text" placeholder=" " name="first_name" id="first_name" class="input">
                        <label>First Name</label>
                    </div>
                    <div class="input-container">
                        <input type="text" placeholder=" " name="last_name" id="last_name" class="input">
                        <label>Last Name</label>
                    </div>
                </div>
                <div class = "address-container">
                    <div class="input-container">
                        <input type="text" placeholder=" " name="address" id="address" class="input">
                        <label >Address</label>
                    </div>
                </div>
                <div class = "state">
                    <div class="input-container">
                        <input type="text" placeholder=" " name="city" id="city" class="input">
                        <label >City</label>
                    </div>
                    <div class="input-container">
                        <select name="state" id="state" class="custom">
                            <option value="" disabled selected>State</option>
                            <option value="afghanistan">Afghanistan</option>
                            <option value="armenia">Armenia</option>
                            <option value="azerbaijan">Azerbaijan</option>
                            <option value="bahrain">Bahrain</option>
                            <option value="bangladesh">Bangladesh</option>
                            <option value="bhutan">Bhutan</option>
                            <option value="brunei">Brunei</option>
                            <option value="cambodia">Cambodia</option>
                            <option value="china">China</option>
                            <option value="cyprus">Cyprus</option>
                            <option value="georgia">Georgia</option>
                            <option value="india">India</option>
                            <option value="indonesia">Indonesia</option>
                            <option value="iran">Iran</option>
                            <option value="iraq">Iraq</option>
                            <option value="israel">Israel</option>
                            <option value="japan">Japan</option>
                            <option value="jordan">Jordan</option>
                            <option value="kazakhstan">Kazakhstan</option>
                            <option value="kuwait">Kuwait</option>
                            <option value="kyrgyzstan">Kyrgyzstan</option>
                            <option value="laos">Laos</option>
                            <option value="lebanon">Lebanon</option>
                            <option value="malaysia">Malaysia</option>
                            <option value="maldives">Maldives</option>
                            <option value="mongolia">Mongolia</option>
                            <option value="myanmar">Myanmar</option>
                            <option value="nepal">Nepal</option>
                            <option value="north-korea">North Korea</option>
                            <option value="oman">Oman</option>
                            <option value="pakistan">Pakistan</option>
                            <option value="palestine">Palestine</option>
                            <option value="philippines">Philippines</option>
                            <option value="qatar">Qatar</option>
                            <option value="saudi-arabia">Saudi Arabia</option>
                            <option value="singapore">Singapore</option>
                            <option value="south-korea">South Korea</option>
                            <option value="sri-lanka">Sri Lanka</option>
                            <option value="syria">Syria</option>
                            <option value="taiwan">Taiwan</option>
                            <option value="tajikistan">Tajikistan</option>
                            <option value="thailand">Thailand</option>
                            <option value="timor-leste">Timor-Leste</option>
                            <option value="turkey">Turkey</option>
                            <option value="turkmenistan">Turkmenistan</option>
                            <option value="united-arab-emirates">United Arab Emirates</option>
                            <option value="uzbekistan">Uzbekistan</option>
                            <option value="vietnam">Vietnam</option>
                            <option value="yemen">Yemen</option>
                        </select>
                    </div>

                    <div class="input-container">
                        <input type="text" placeholder=" " name="zip-code" id="zip-code" class="input">
                        <label >Zip-Code</label>
                    </div>
                </div>
                
                <div class = "phone">
                    <div class="input-container">
                        <input type="text" placeholder=" " name="phone_number" id="phone_number" class="input">
                        <label >Phone</label>
                    </div>
                </div>
                
            </div>
            
            <div class = "payment-container">
                <h3>Payment</h3>
                <p>All transactions are secure and encrypted.</p>
                
                <div class = "payment-method-top">
                    <input class = "checkbox" type="checkbox" id="touch-n-go" name="payment-method" value="Touch and Go" onclick="handleCheckboxClick(this)">
                    <label class = "payment-method-name" for="touch-n-go">Touch'n Go</label><br>
                </div>



                <div class = "payment-method-mid">
                    <input class = "checkbox" type="checkbox" id="credit-card" name="payment-method" value="Credit Card" onclick="handleCheckboxClick(this)">
                    <label class = "payment-method-name" for="credit-card">Credit card</label><br>
                </div>

                <div class = "payment-method-mid">
                    <input class = "checkbox" type="checkbox" id="paypal" name="payment-method" value="Paypal" onclick="handleCheckboxClick(this)">
                    <label class = "payment-method-name" for="paypal">PayPal</label><br>
                </div>

                <div class = "payment-method-bottom">
                    <input class = "checkbox" type="checkbox" id="cash_on_delivery" name="payment-method" value="Cash on Delivery" onclick="handleCheckboxClick(this)">
                    <label class = "payment-method-name" for="'cash_on_delivery">Cash on Delivery</label><br>
                </div>

                
            </div>

            <div class = "voucher-container">
                <h3>Voucher</h3>
                <div id = "voucher"></div>
                <div class = "vocher-eror" id = 'vocher-eror'></div>
            </div>

            <button class = "pay-now" name = "pay-now" onclick = "clearstorage()">Pay Now</button>

        </form>
    </div>

    <script src = "../javascript/handle-checkbox.js"></script>
    <script>
        function clearstorage() {
            let cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
            cartQuantity = 0;
            localStorage.setItem('cartQuantity', cartQuantity);
            updateCartDisplay();
        }

        function updateCartDisplay() {
    const quantityDisplay = document.querySelector('.cart-quantity');
    const cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
    
    if (cartQuantity > 0) {

      quantityDisplay.style.display = 'block'; // show the quantity
      quantityDisplay.textContent = cartQuantity > 9 ? '9+' : cartQuantity; // If more than 9 it will show 9+
    } else {
      quantityDisplay.style.display = 'none'; // Hide the red dot
    }
  }
    </script>
</body>
</html>
<?php include("../header-footer/footer.php");?>
