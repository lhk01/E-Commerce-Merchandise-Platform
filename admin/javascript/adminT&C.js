// Function to load the PDF from the server and display it in the iframe
async function loadPDF() {
    try {
        // Fetch the PDF as binary data from the server
        const response = await fetch('../function/updateT&C.php?action=fetchPDF');
        
        // Check if the response is okay
        if (!response.ok) {
            throw new Error('Error fetching the PDF. Please try again.');
        }

        // Get the binary data as a Blob
        const blob = await response.blob();

        // Create a URL for the Blob and set it as the source of the iframe
        const pdfViewer = document.getElementById('pdfViewer');
        pdfViewer.src = URL.createObjectURL(blob);
        pdfViewer.style.display = 'block'; // Show the iframe to display the PDF
    } catch (error) {
        alert(error.message);
    }
}

// Event listener for form submission to handle file upload
document.getElementById('tcForm').addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevent the default form submission
    
    const fileInput = document.getElementById('tcUpload');
    const filePath = fileInput.value; // Get the file path
    const allowedExtensions = /(\.pdf)$/i; // Regular expression to check for PDF

    try {
        // Check if the file extension is valid
        if (!allowedExtensions.exec(filePath)) {
            throw new Error('File not supported. Please upload a valid PDF file.');
        }

        const formData = new FormData(this); // Create FormData object from form
        const uploadButton = document.querySelector('button[type="submit"]');

        uploadButton.disabled = true; // Disable the button
        uploadButton.textContent = 'Uploading...'; // Change button text

        // Make AJAX request to upload file
        const response = await fetch('../function/updateT&C.php', {
            method: 'POST',
            body: formData
        });

        // Check if the response is okay
        if (!response.ok) {
            throw new Error('Error uploading file. Please try again.');
        }

        const data = await response.text(); // Get the response text
        alert('Upload successful: ' + data); // Popup alert on successful upload

        // Load the newly uploaded PDF
        loadPDF();
    } catch (error) {
        alert(error.message); // Show the error message in a popup alert
    } finally {
        const uploadButton = document.querySelector('button[type="submit"]'); // Re-select the button
        uploadButton.disabled = false; // Enable the button again
        uploadButton.textContent = 'Upload T&C'; // Reset button text
    }
});

// Load the PDF when the page loads
window.onload = loadPDF;
