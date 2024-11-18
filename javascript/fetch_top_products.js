async function fetchAndDisplayProducts() {
  try {
    const response = await fetch('../function/fetch_top_products.php'); // Adjust path as needed
    const products = await response.json();

    const productContainer = document.getElementById('product-container');
    let htmlContent = '';

    // Check if there are products in the response
    if (products.length > 0) {
      // Display the featured product (first product in the list)
      const featuredProduct = products[0];
      const featuredImages = featuredProduct.product_Image.split(','); // Get the array of images

      htmlContent += `
        <div class="featured-product">
          <div class="slider-container">
            <a href="product-page.php?id=${featuredProduct.product_id}" class="product-link-1">
              ${featuredImages.map((image, index) => `
                <img src="../upload//product_image/${image.trim()}" class="featured-product-image slider-image" data-index="${index}" style="display: ${index === 0 ? 'block' : 'none'};">
              `).join('')}
            </a>
            <button class="prev" onclick="prevImage(this)">&#10094;</button>
            <button class="next" onclick="nextImage(this)">&#10095;</button>
          </div>
          <div class="product-description">
            <p>${featuredProduct.productName}</p>
            <p>RM ${featuredProduct.price}</p>
          </div>
        </div>
      `;

      // Display the remaining products in a grid
      htmlContent += '<div class="product-grid">';
      products.slice(1, 5).forEach((product, index) => {
        const productImages = product.product_Image.split(','); // Get the array of images

        // Start a new row for every 2 products
        if (index % 2 === 0) {
          htmlContent += '<div class="product-row">';
        }

        // Product box with image slider
        htmlContent += `
          <a href="product-page.php?id=${product.product_id}" class="product-link">
            <div class="product-box">
              <div class="slider-container">
                ${productImages.map((image, imageIndex) => `
                  <img src="../upload/product_image/${image.trim()}" class="product-image slider-image" data-index="${imageIndex}" style="display: ${imageIndex === 0 ? 'block' : 'none'};">
                `).join('')}
                </a>
                <button class="prev" onclick="prevImage(this)">&#10094;</button>
                <button class="next" onclick="nextImage(this)">&#10095;</button>
                <a href="product-page.php?id=${product.product_id}" class="product-link">
              </div>
              <div class="product-description-2">
                <p>${product.productName}</p>
                <p>RM ${product.price}</p>
              </div>
            </div>
          </a>
        `;

        // Close the row after every 2 products
        if (index % 2 === 1) {
          htmlContent += '</div>';
        }
      });

      // Ensure the last row is closed if it has an odd number of products
      if (products.length > 1 && (products.length - 1) % 2 === 1) {
        htmlContent += '</div>';
      }

      htmlContent += '</div>'; // Close the product grid
    } else {
      // If no products, display a message
      htmlContent = '<p>No top-selling products available.</p>';
    }

    // Insert the generated HTML into the container
    productContainer.innerHTML = htmlContent;
  } catch (error) {
    console.error('Error fetching products:', error);
    document.getElementById('product-container').innerHTML = '<p>Error loading products.</p>';
  }
}

// Call the async function to execute the fetch and display
fetchAndDisplayProducts();
