const chevronButton = document.getElementById('chevron-down-button');
const categoryContainer = document.getElementById('category-container2');

chevronButton.addEventListener('click', () => {
  categoryContainer.classList.toggle('show'); // Toggle the 'show' class
});
