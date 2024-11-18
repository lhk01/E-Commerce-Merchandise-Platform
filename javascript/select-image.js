const thumbnails = document.querySelectorAll('.thumbnail');
const mainImage = document.getElementById('main-image');

// Function to remove active class from all thumbnails
function clearActiveClass() {
    thumbnails.forEach(thumbnail => {
        thumbnail.classList.remove('active');
    });
}

thumbnails.forEach(thumbnail => {
    thumbnail.addEventListener('click', function() {
        // Swap the src of the main image with the clicked thumbnail
        const newSrc = this.src;
        mainImage.src = newSrc;

        // Remove 'active' class from all thumbnails and add it to the clicked one
        clearActiveClass();
        this.classList.add('active');
    });
});
