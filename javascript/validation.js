const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('password_confirmation');
const content = document.getElementById('password-requirements');
const content2 = document.getElementById('password-requirements-2');
const requirementList = document.querySelectorAll(".requirement-list li");
const submitButton = document.getElementById("submit_btn");
const imageInput = document.getElementById('profile_image');
let isRecaptchaVerified = false; 

// Show or hide password requirement list
passwordInput.addEventListener('input', function() {
    content.style.display = passwordInput.value.length > 0 ? 'block' : 'none';
    validateForm(); // Validate the form after every input
});

// Show or hide password confirmation check
confirmPasswordInput.addEventListener('input', function() {
    content2.style.display = confirmPasswordInput.value.length > 0 ? 'block' : 'none';
    checkPasswordMatch(); // Check password match whenever confirmPasswordInput changes
    validateForm(); // Validate the form after every input
});

// Password validation requirements
const requirements = [
    { req: /.{8,}/, index: 0 },  // Minimum of 8 characters
    { req: /[0-9]/, index: 1 },  // At least one number
    { req: /[a-z]/, index: 2 },  // At least one lowercase letter
    { req: /[^A-Za-z0-9]/, index: 3 },  // At least one special character
    { req: /[A-Z]/, index: 4 },  // At least one uppercase letter
];

passwordInput.addEventListener('keyup', (e) => {
    requirements.forEach(item => {
        const isValid = item.req.test(e.target.value);
        const requirementItem = requirementList[item.index];

        // Update icon and color based on validation
        requirementItem.firstElementChild.className = isValid ? "fa-solid fa-check" : "fa-solid fa-circle";
        requirementItem.firstElementChild.style.color = isValid ? "#007bff" : "#777";
    });
    validateForm(); // Validate the form after every keyup
});

// Check if passwords match
function checkPasswordMatch() {
    const errorMessageElement = document.getElementById("error");
    const iconElement = document.querySelector('.requirement-list-2 i.fa-solid');

    if ((passwordInput.value === confirmPasswordInput.value) && passwordInput.value !== "") {
        errorMessageElement.innerHTML = "Passwords match.";
        iconElement.className = 'fa-solid fa-check';
        iconElement.style.color = "#007bff"; // Change color to blue when passwords match
    } else {
        errorMessageElement.innerHTML = "Passwords do not match.";
        iconElement.className = 'fa-solid fa-circle';
        iconElement.style.color = "#777"; // Revert to circle if they don't match
    }
    validateForm(); // Validate the form after checking password match
}

// Enable or disable the submit button based on form validation
function validateForm() {
    // Check if the password meets all validation requirements
    const isPasswordValid = requirements.every(item => item.req.test(passwordInput.value));
    // Check if the password and confirm password fields match and are not empty
    const isPasswordMatching = passwordInput.value === confirmPasswordInput.value && passwordInput.value !== "";

    // Check if an image file is selected and is valid
    const isImageValid = imageInput.files.length > 0 && imageInput.files[0].type.startsWith('image/');

    // Enable or disable the submit button based on password, image, and reCAPTCHA validation
    if (isPasswordValid && isPasswordMatching && isRecaptchaVerified && isImageValid) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
}

// reCAPTCHA callback function
function enableSubmitbtn() {
    isRecaptchaVerified = true;
    validateForm(); // Revalidate the form when reCAPTCHA is complete
}

// reCAPTCHA expires callback
function recaptchaExpired() {
    isRecaptchaVerified = false;
    validateForm(); // Disable submit button if recaptcha expires
}

// Image validation and enabling submit button when an image is selected
imageInput.addEventListener('change', function() {
    validateImageFile();
    validateForm(); // Validate the form after image input
});

// Function to validate image file type
function validateImageFile() {
    const file = imageInput.files[0];
    const fileType = file ? file.type : null;
    
    // Check if the selected file is an image (image/*)
    if (fileType && fileType.startsWith('image/')) {
        submitButton.disabled = false; // Enable submit button if it's an image
    } else {
        submitButton.disabled = true; // Disable submit button if not an image
    }
}
