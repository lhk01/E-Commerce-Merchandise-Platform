// Function to preview the profile picture
function previewProfilePicture(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('profilePicturePreview');
        output.src = reader.result; // Update to show the new image
    };
    reader.readAsDataURL(event.target.files[0]); // Read the uploaded file
}

// Handle form submission
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Prevent the default form submission

    const formData = new FormData(this);

    try {
        const response = await fetch('../function/updateProfile.php', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.success) {
            alert('Profile updated successfully!');
            location.reload(); 
        } else {
            alert('Error updating profile: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('There was an error updating the profile: ' + error.message);
    }
});

