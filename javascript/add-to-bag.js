document.addEventListener('DOMContentLoaded', () => {
  const productId = new URLSearchParams(window.location.search).get('id');
  let cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
 



  // Increase the red dot quantity
  function addToCart() {
    let total_quantity = parseInt(document.getElementById('quantity').value);
    
    // Retrieve the current cart quantity from localStorage or initialize it to 0
    let cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
    
    // Update the cart quantity
    cartQuantity += total_quantity;
    
    // Store the updated quantity in localStorage
    localStorage.setItem('cartQuantity', cartQuantity);
    
    // Log the updated quantity for debugging
    console.log(cartQuantity);
    
    // Update the cart display (you can implement this function separately)
    updateCartDisplay();
}

  

  // Update cart display quantity
  function updateCartDisplay() {
    const quantityDisplay = document.querySelector('.cart-quantity');
    const mobileQuantityDisplay = document.querySelector('.cart-quantity-mobile');
    const cartQuantity = parseInt(localStorage.getItem('cartQuantity')) || 0;
    
    if (cartQuantity > 0) {
      quantityDisplay.style.display = 'block'; // show the quantity
      mobileQuantityDisplay.style.display = 'block';
      quantityDisplay.textContent = cartQuantity > 9 ? '9+' : cartQuantity; // If more than 9 it will show 9+
      mobileQuantityDisplay.textContent = cartQuantity > 9 ? '9+' : cartQuantity;
    } else {
      quantityDisplay.style.display = 'none'; // Hide the red dot
      mobileQuantityDisplay.style.display = 'none';
    }
  }

  // Call updateCartDisplay on page load to show the correct quantity
  updateCartDisplay();
  
  // Function to fetch product details only
  const fetchProductDetailsOnly = () => {
    fetch(`../function/get-product-details.php?id=${productId}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          console.error(data.error);
          return;
        }
      })
  };

  // Fetch product details immediately
  fetchProductDetailsOnly();

  // Set interval to fetch product details every 10 seconds (10000 ms)
  setInterval(fetchProductDetailsOnly, 10000);

  function showNotification(message) {
    const notification = document.getElementById('notification'); // Get the notification element
    if (notification) {
      notification.innerText = message;
      notification.classList.remove('hidden'); // Show the notification
      notification.classList.add('show'); // Add show class to make it visible
      setTimeout(() => {
        notification.classList.remove('show');  // After 2 seconds, remove show class
        notification.classList.add('hidden'); // Hide the notification
      }, 2000);
    }
  }

  function showErrorNotification(message) {
    const notification = document.getElementById('error_notification'); // Get the notification element
    if (notification) {
      notification.innerText = message;
      notification.classList.remove('hidden'); // Show the notification
      notification.classList.add('show'); // Add show class to make it visible
      setTimeout(() => {
        notification.classList.remove('show');  // After 2 seconds, remove show class
        notification.classList.add('hidden'); // Hide the notification
      }, 2000);
    }
  }

  // Handle form submission and validation
  document.querySelector('.add-to-bag').addEventListener('click', () => {
    let valid = true;
    const quantity = parseInt(document.getElementById('quantityInput').value, 10);
     const userId = document.getElementById('user_id').getAttribute('value').trim();
console.log(userId);

    let sizes = [];
    if (Number(userId) === 0) {
        memoryPage = `../pages/product-page.php?id=${productId}`;
       localStorage.setItem('memoryPage', memoryPage);
       window.location.href = "../login/login.php";
    }

    // Check if product is in the 'Apparel' category and handle sizes
    fetch(`../function/get-product-details.php?id=${productId}`)
      .then(response => response.json())
      .then(data => {
        if (data.categories === 'Apparel') {
          const sizeInputs = document.querySelectorAll('input[name="size[]"]:checked');
          sizeInputs.forEach(input => sizes.push(input.value));

          if (sizes.length === 0) {
            alert('Please select a size.');
            valid = false;
          }

          // Stock validation for sizes
          if (sizes.includes('M') && data.sizeM < (quantity + data.bagsizeM)) {
            showErrorNotification("Selected quantity for size M exceeds available stock");
            valid = false;
          }
          if (sizes.includes('L') && data.sizeL < quantity + data.bagsizeL) {
            showErrorNotification('Selected quantity for size L exceeds available stock.');
            valid = false;
          }
          if (sizes.includes('XL') && data.sizeXL < quantity + data.bagsizeXL) {
            showErrorNotification('Selected quantity for size XL exceeds available stock.');
            valid = false;
          }
        } else {
          // Stock validation for non-apparel items
          if (data.stock < quantity + data.bagQuantity) {
            showErrorNotification('Selected quantity exceeds available stock.');
            valid = false;
          }
        }

        // If all validations pass, submit the form data
        if (valid) {
          const formData = new FormData();
          formData.append('quantity', quantity);
          sizes.forEach(size => formData.append('size[]', size));
          showNotification("Added successfully");
          addToCart();

          
          fetch(`../function/process-add-to-bag.php?id=${productId}`, {
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(data => {
            document.querySelector('.msg').innerHTML = `<p>${data}</p>`;
          })
          .catch(error => console.error('Error:', error));
        }
      });
  });
});

