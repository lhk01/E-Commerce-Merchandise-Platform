function startCountdown(name, countdownName,filename) {
    const button = document.getElementById('send-email');
    let countdown = 60; // countdown time in seconds

    // Disable the button during countdown
    button.disabled = true;
    
    // Send out the email before countdown
    send(filename);

    // Update the button text to show the countdown
    const countdownInterval = setInterval(() => {
        button.textContent = `${countdownName} (${countdown})`;
        countdown--;

        // When countdown reaches 0, reset the button text
        if (countdown < 0) {
            clearInterval(countdownInterval);
            button.textContent = `${name}`;
            button.disabled = false;
        }
    }, 1000); // 1000ms = 1 second
}

function send(filename) {
    const formData = new FormData();
    formData.append("action", "sended");
    console.log(filename);
    fetch(filename, {
        method: "POST",
        body: formData,
    })
    .then((response) => response.text())
    .then((data) => {
        console.log("OTP sent:", data);
        const messageDiv = document.getElementById('otp-message');
        messageDiv.textContent = "An OTP (one-time password) has been sent to your email.";
    })
    .catch((error) => {
        console.error("Error sending OTP:", error);
    });
}
