// Function to fetch dynamic content for the dashboard
async function fetchDashboardData() {
    try {
        const response = await fetch('../function/fetchDashboardData.php'); // Fetch from the PHP file
        const data = await response.json();

        // Update the dashboard with the fetched data
        document.getElementById('totalSales').innerText = `RM${data.totalSales}`;
        document.getElementById('totalOrders').innerText = data.totalOrders;
        document.getElementById('totalCustomers').innerText = data.totalCustomers;
        document.getElementById('averageOrder').innerText = `RM${data.averageOrder}`;
        
        // Update the recent orders section
        populateRecentOrders(data.recentOrders);
    } catch (error) {
        console.error('Error fetching dashboard data:', error);

        // Display error in the UI
        document.getElementById('totalSales').innerText = 'Error';
        document.getElementById('totalOrders').innerText = 'Error';
        document.getElementById('totalCustomers').innerText = 'Error';
        document.getElementById('averageOrder').innerText = 'Error';
    }
}

// Function to fetch sales data for the chart
async function fetchSalesData() {
    try {
        const response = await fetch('../function/fetchSalesData.php'); // Create this PHP file to return sales data
        const salesData = await response.json();
        
        renderSalesChart(salesData); // Call the function to render the chart
    } catch (error) {
        console.error('Error fetching sales data:', error);
    }
}

// Function to render the sales chart
function renderSalesChart(salesData) {
    const ctx = document.getElementById('salesChart').getContext('2d');

    // Convert values to numbers
    const values = salesData.values.map(value => parseFloat(value));

    try {
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.labels, // Array of labels (e.g., months, days)
                datasets: [{
                    label: 'Total Sales',
                    data: values, // Use the converted values here
                    borderColor: 'rgba(0, 128, 0, 1)', // Green color
                    backgroundColor: 'rgba(0, 255, 0, 0.2)', // Light green color
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow for custom sizing
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            font: {
                                size: 14 // Fixed font size for x-axis title
                            }
                        },
                        ticks: {
                            font: {
                                size: 12 // Fixed font size for x-axis labels
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales (RM)',
                            font: {
                                size: 14 // Fixed font size for y-axis title
                            }
                        },
                        ticks: {
                            font: {
                                size: 12 // Fixed font size for y-axis labels
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error rendering sales chart:', error);
        // Optionally, display a message to the user
        const errorMessage = document.createElement('p');
        errorMessage.innerText = 'Error rendering chart. Please try again later.';
        document.querySelector('.line-chart-section').appendChild(errorMessage);
    }
}



// Function to populate recent orders table
function populateRecentOrders(orders) {
    const ordersTableBody = document.querySelector('.recent-orders tbody');
    ordersTableBody.innerHTML = ''; // Clear the table body before populating

    if (orders.length === 0) {
        // If there are no recent orders, display a message
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = '<td colspan="5">No recent orders available.</td>';
        ordersTableBody.appendChild(emptyRow);
    } else {
        // Loop through orders and add them to the table
        orders.forEach(order => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td data-title="Order ID">${order.order_id}</td>
                <td data-title="Customer">${order.username}</td>
                <td data-title="Date">${order.order_date}</td>
                <td data-title="Total">RM${order.total_price}</td>
                <td data-title="Status">${order.order_status}</td>
            `;
            ordersTableBody.appendChild(row);
        });
    }
}

// Call the function to fetch data when the page loads
window.onload = function() {
    fetchDashboardData();
    fetchSalesData(); 
};

