// Function to fetch customer data using AJAX
async function fetchCustomers() {
    try {
        const response = await fetch('../function/fetchCustomers.php');

        // Check if the response is okay (status in the range 200-299)
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        const tableBody = document.querySelector('#customerTable tbody');
        tableBody.innerHTML = ''; // Clear loading text

        if (data.length > 0) {
            data.forEach(customer => {
                let veriyfy = 'No';
                if(customer.is_verified === '1'){
                    veriyfy = 'Yes';
                }
                
                const row = `<tr>
                    <td data-title="ID">${customer.id}</td>
                    <td data-title="Customer Name">${customer.username}</td>
                    <td data-title="Email Address">${customer.email}</td>
                    
                <td data-title="Verify">${veriyfy}</td>

                


                    <td data-title="Action">
                        <a href="#" class="delete-btn" onclick="deleteCustomer(${customer.id})">Delete</a>
                    </td>
                </tr>`;
                tableBody.innerHTML += row; // Add customer rows
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="5">No customers found.</td></tr>';
        }
    } catch (error) {
        console.error('Error fetching customer data:', error);
        document.querySelector('#customerTable tbody').innerHTML = '<tr><td colspan="5">Error loading customers.</td></tr>';
    }
}

// Function to delete a customer
async function deleteCustomer(customerId) {
    if (confirm('Are you sure you want to delete this customer?')) {
        try {
            const response = await fetch('../function/fetchCustomers.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded', 
                },
                body: `id=${customerId}`,
            });

            // Check if the response is okay
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            // Optionally, you could fetch the updated customer list again
            fetchCustomers(); // Refresh the customer list
        } catch (error) {
            console.error('Error deleting customer:', error);
            alert('Error deleting customer. Please try again.');
        }
    }
}

// Call the function to fetch customers on page load
document.addEventListener('DOMContentLoaded', fetchCustomers);
