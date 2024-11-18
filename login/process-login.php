<?php
// Include the database connection file
include("../database/database.php");
session_start();

// Initialize the response array
$response = array('status' => 'error', 'message' => 'Something went wrong');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required fields are set
    if (isset($_POST['email_or_username']) && isset($_POST['password']) && isset($_POST['g-recaptcha-response'])) {

        // Verify reCAPTCHA
        $secretKey = "6LfAl0UqAAAAAAN-xWgbIAtqSRJ00VZ1Bq6_Ik55"; // Your secret key
        $recaptchaResponse = $_POST['g-recaptcha-response'];

        // Make a request to the Google reCAPTCHA API
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' .
            $secretKey . '&response=' . $recaptchaResponse);

        $responseData = json_decode($verifyResponse);

        if ($responseData->success) {
            // Sanitize the input values
            $emailOrUsername = filter_input(INPUT_POST, 'email_or_username', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            // Prepare the SQL query to find the user by email or username
            $sql = "SELECT * FROM users WHERE (email_address = ? OR username = ?) AND is_verified = 1";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ss", $emailOrUsername, $emailOrUsername);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Check if a user was found
            if ($user) {
                $password_hash = $user['password_hash'];

                // Verify the password
                if (password_verify($password, $password_hash)) {
                    // Regenerate session ID to prevent session fixation
                    session_regenerate_id(true);

                    // Store the user's ID in the session
                    $_SESSION['user_id'] = $user['id'];

                     $response = array('status' => 'success', 'redirect' => '../pages/homepage.php');
                    
                } else {
                    $response['message'] = "Your username or password may be incorrect.";
                }
            } else {
                $response['message'] = "Your username or password may be incorrect.";
            }
        } else {
            $response['message'] = "reCAPTCHA verification failed. Please try again.";
        }
    } else {
        $response['message'] = "Please fill out all required fields.";
    }
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

?>
