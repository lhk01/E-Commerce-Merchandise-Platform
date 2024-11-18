// Function to fetch order list data from the server
async function fetchOrderListData() {
    try {
        const response = await fetch('fetchOrderListData.php');
        const data = await response.json();

        // Check if 'ordersList' exists in the response
        if (data.ordersList) {
            populateOrdersList(data.ordersList);
        } else {
            populateOrdersList([]); // Pass an empty array if no orders
        }
    } catch (error) {
        console.error('Error fetching order data:', error);
        populateOrdersList([]); // Handle errors by showing no data
    }
}


// Function to populate recent orders table
function populateOrdersList(orders) {
    const ordersTableBody = document.querySelector('.order-list tbody');
    ordersTableBody.innerHTML = ''; // Clear the table body before populating

    if (orders.length === 0) {
        // If there are no recent orders, display a message
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = '<td colspan="6">No orders available.</td>';
        ordersTableBody.appendChild(emptyRow);
    } else {
        // Loop through orders and add them to the table
        orders.forEach(order => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${order.id}</td>
                <td>${order.customer}</td>
                <td>${order.date}</td>
                <td>RM${order.total}</td>
                <td>${order.status}</td>
                <td><a href="adminViewOrder.php?order_id=${order.id}" class="view-order">View</a></td>
            `;
            ordersTableBody.appendChild(row);
        });
    }
}

// Call the function to fetch data when the page loads
window.onload = fetchOrderListData;
