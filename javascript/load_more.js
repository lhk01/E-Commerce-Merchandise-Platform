document.addEventListener("DOMContentLoaded", function() {
    let offset = 0; // Number of products loaded
    const limit = 12; // Number of products to load at a time
    const loadMoreBtn = document.getElementById("load-more-btn");
    const productContainer = document.getElementById("product-container");
    let categories = []; // Store the selected categories
    let sortBy = ""; // Store the selected sorting option (high-to-low or low-to-high)
    let availability = []; // Store the selected availability filters

    // Set a timeout to fix the issue with the button appearing first
    setTimeout(() => {
        loadMoreBtn.style.display = "inline";
    }, 300);

    // Function to get selected categories
    function getSelectedCategories() {
        categories = []; // Reset categories
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox.id.startsWith('category-') && checkbox.id !== 'category-all') {
                categories.push(checkbox.id.replace('category-', ''));
            }
        });
    }

    // Function to get the selected sorting option
    function getSelectedSort() {
        const highToLow = document.getElementById('sort-high-to-low');
        const lowToHigh = document.getElementById('sort-low-to-high');

        if (highToLow.checked) {
            sortBy = "high-to-low";
        } else if (lowToHigh.checked) {
            sortBy = "low-to-high";
        } else {
            sortBy = ""; // No sorting selected
        }
    }

    // Function to get selected availability
    function getSelectedAvailability() {
        availability = []; // Reset availability
        const availCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        availCheckboxes.forEach(checkbox => {
            if (checkbox.checked && checkbox.id.startsWith('availability-')) {
                availability.push(checkbox.id.replace('availability-', ''));
            }
        });
    }

    // Function to load products
    function loadProducts() {
        const xhr = new XMLHttpRequest(); // To perform an AJAX request.
        xhr.open("POST", "../function/load_more.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText;

                if (response.trim() === "") {  // Check if empty
                    loadMoreBtn.style.display = "none"; // No more products, hide the button
                } else {
                    productContainer.innerHTML += response; // Append the newly loaded products to the container
                    offset += limit; // Update the offset variable
                }
            }
        };

        // Prepare the data to send
        let params = "offset=" + offset + "&limit=" + limit;
        if (categories.length > 0) {
            params += "&categories=" + encodeURIComponent(categories.join(','));
        }
        if (sortBy) {
            params += "&sortBy=" + encodeURIComponent(sortBy);
        }
        if (availability.length > 0) {
            params += "&availability=" + encodeURIComponent(availability.join(','));
        }

        xhr.send(params);
    }

    // Load initial items
    loadProducts();

    // Event listener for the Load More button
    loadMoreBtn.addEventListener("click", loadProducts);

    // Event listener for checkbox changes (category, sorting, and availability)
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            getSelectedCategories();    // Get the updated list of selected categories
            getSelectedSort();          // Get the updated sorting option
            getSelectedAvailability();  // Get the updated availability filters
            offset = 0;                  // Reset offset
            productContainer.innerHTML = ""; // Clear current products
            loadProducts();             // Reload products with the new filters and sorting
        });
    });
});
