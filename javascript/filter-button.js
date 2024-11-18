document.addEventListener('DOMContentLoaded', function () {
  const filterBtn = document.getElementById('filterBtn');
  const filterPanel = document.getElementById('filterPanel');
  const closeBtn = document.getElementById('closeBtn');
  const filterContainer = document.getElementById('filter-container');

  // Toggle the 'active' class when the filter button is clicked
  filterBtn.addEventListener('click', function () {
    filterPanel.classList.toggle('active');
    filterContainer.style.display = "none";
  });

  // Hide the filter panel when the close button is clicked
  closeBtn.addEventListener('click', function () {
    filterPanel.classList.remove('active');
    
     setTimeout(() => {
      filterContainer.style.display = "inline";
    }, 250);
  });
});


document.addEventListener("DOMContentLoaded", function() {
    const highToLowCheckbox = document.getElementById("sort-high-to-low");
    const lowToHighCheckbox = document.getElementById("sort-low-to-high");

    // Event listener for 'High to Low' checkbox
    highToLowCheckbox.addEventListener("change", function() {
        if (highToLowCheckbox.checked) {
            // Uncheck 'Low to High' when 'High to Low' is checked
            lowToHighCheckbox.checked = false;
        }
    });

    // Event listener for 'Low to High' checkbox
    lowToHighCheckbox.addEventListener("change", function() {
        if (lowToHighCheckbox.checked) {
            // Uncheck 'High to Low' when 'Low to High' is checked
            highToLowCheckbox.checked = false;
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const inStock = document.getElementById("availability-in-stock");
    const outStock = document.getElementById("availability-out-of-stock");

    // Event listener for 'in-stock' checkbox
    inStock.addEventListener("change", function() {
        if (inStock.checked) {
            // Uncheck 'outstock' when 'in-stock' is checked
            outStock.checked = false;
        }
    });

    // Event listener for 'Low to High' checkbox
    outStock.addEventListener("change", function() {
        if (outStock.checked) {
            // Uncheck 'in-stock' when 'outstock' is checked
            inStock.checked = false;
        }
    });
});

