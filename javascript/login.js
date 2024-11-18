function togglePassword() {
  const passwordInput = document.getElementById('password');
  const toggleButton = document.querySelector('.toggle-password i');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    toggleButton.classList.remove('fa-eye');
    toggleButton.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    toggleButton.classList.remove('fa-eye-slash');
    toggleButton.classList.add('fa-eye');
  }
}

// Handle form submission with AJAX
document.getElementById('submit_btn').addEventListener('click', function() {
  const emailOrUsername = document.getElementById('email_or_username').value;
  const password = document.getElementById('password').value;

  const verifyResponse = grecaptcha.getResponse();

  if (emailOrUsername === "" || password === "") {
    displayErrorMessage("Please enter both email/username and password.");
    return;
  }

  if (verifyResponse === "") {
    displayErrorMessage("Please complete the reCAPTCHA.");
    return;
  }

  // Show loading spinner
  const loadingSpinner = document.getElementById('loading');
  const loginForm = document.querySelector('.login-form');
  loginForm.style.display = 'none';
  loadingSpinner.style.display = 'block';

  // AJAX request to process-login.php
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'process-login.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  
  // Prepare the data to send in POST request
  const params = `email_or_username=${encodeURIComponent(emailOrUsername)}&password=${encodeURIComponent(password)}&g-recaptcha-response=${verifyResponse}`;
  
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      // Hide loading spinner
      loadingSpinner.style.display = 'none';
        console.log(xhr.responseText); 
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);


        if (response.status === 'success') {
          const memoryPage = localStorage.getItem('memoryPage');

          if (memoryPage){
            pages = memoryPage;
            localStorage.removeItem('memoryPage');
              window.location.href = pages;
          }else{
            window.location.href = response.redirect;
          }
          
        } else {
          // Show form again and display error message
          loginForm.style.display = 'flex';
          grecaptcha.reset();
          displayErrorMessage(response.message);
        }
      } else {
        // Handle errors with the request
        displayErrorMessage("An error occurred. Please try again.");
        grecaptcha.reset();
      }
    }
  };

  // Send the request with the form data
  xhr.send(params);
});

// Function to display error message
function displayErrorMessage(message) {
  const errorMessageDiv = document.getElementById('error-message');
  errorMessageDiv.style.display = 'block';
  errorMessageDiv.innerText = message;
}