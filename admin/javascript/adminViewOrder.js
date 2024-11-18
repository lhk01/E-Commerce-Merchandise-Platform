// Function to fetch order details from the server
async function fetchOrderDetails(orderId) {
    try {
        const response = await fetch(`../function/fetchOrderDetails.php?order_id=${orderId}`);
        const data = await response.json();

        // Check if the response has order details
        if (data.orderDetails) {
            populateOrderDetails(data.orderDetails);
        } else {
            console.error('No order details found.');
        }
    } catch (error) {
        console.error('Error fetching order details:', error);
    }
}

// Function to populate order details on the page
function populateOrderDetails(orderDetails) {
    document.getElementById('order-id').textContent = orderDetails.order_id;
    document.getElementById('customer-id').textContent = orderDetails.user_id;
    document.getElementById('customer-name').textContent = orderDetails.customer_name;
    document.getElementById('shipping-address').textContent = orderDetails.shipping_address;
    document.getElementById('contact-number').textContent = orderDetails.contact_number;
    document.getElementById('shipping-status').textContent = orderDetails.order_status;
    document.getElementById('order-date').textContent = orderDetails.order_date;
    document.getElementById('payment-method').textContent = orderDetails.payment_method;

    // Populate product details
    const productTableBody = document.querySelector('table tbody');
    productTableBody.innerHTML = ''; // Clear the table before populating

    orderDetails.products.forEach(product => {
        const imageArray = product.image.split(",");
        const row = document.createElement('tr');
        row.innerHTML = `
            <td data-title="ID">${product.id}</td>
            <td data-title="Name">${product.name}</td>
            <td data-title="Price">RM${product.price}</td>
            <td data-title="Image"><img src="../../upload/product_image/${imageArray[0]}" alt="${product.name}" width="50" /></td>
            <td data-title="Quantity">${product.quantity}</td>
            <td data-title="Size">${product.sizes}</td> 
        `;
        productTableBody.appendChild(row);
    });
    
}

// Function to submit the updated order status to the server
async function submitOrderStatus(orderId, newStatus) {
    try {
        const response = await fetch('../function/updateOrderStatus.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId, order_status: newStatus })
        });

        const result = await response.json();
        
        if (result.success) {
            // Update the order status on the page after successful submission
            document.getElementById('shipping-status').textContent = newStatus;
            // Show a success pop-up
            alert('Order status updated successfully.');
        } else {
            console.error('Failed to update order status:', result.error);
            alert('Error: ' + result.error); // Optional: Display error in an alert
        }
    } catch (error) {
        console.error('Error submitting order status:', error);
        alert('An error occurred. Please try again.');
    }
}



// Call the function to fetch data when the page loads
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id'); // Assuming you pass order_id in the URL

    if (orderId) {
        fetchOrderDetails(orderId);

        // Add an event listener to handle the order status submission
        document.getElementById('submit-status').addEventListener('click', function() {
            const selectedStatus = document.getElementById('order-status').value;

            // Submit the updated order status
            submitOrderStatus(orderId, selectedStatus);
        });
    } else {
        console.error('Order ID not found in the URL');
    }
};
