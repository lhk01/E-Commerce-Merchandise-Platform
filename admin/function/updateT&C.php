<?php
// Database connection
// Database credentials
include("../database/database.php");

// Handle PDF upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    $fileName = $_FILES['pdf_file']['name'];
    $fileTmpName = $_FILES['pdf_file']['tmp_name'];
    $fileData = file_get_contents($fileTmpName); // Get the binary data from the file

    // Prepare the SQL query directly without bind_param, as bind_param doesn't handle large BLOBs well
    $stmt = $mysqli->prepare("INSERT INTO terms_and_conditions (file_name, file_data) VALUES (?, ?)");
    $null = NULL; // Used to bind the blob data
    
    // Bind parameters and send the BLOB data
    $stmt->bind_param("sb", $fileName, $null);
    $stmt->send_long_data(1, $fileData); // Manually send the blob data
    
    // Execute the query
    if ($stmt->execute()) {
        echo "Terms and Conditions uploaded successfully.";
    } else {
        echo "Error uploading file: " . $stmt->error;
    }


}

// Handle PDF retrieval for display
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetchPDF') {
    // Retrieve the latest PDF blob from the database
    $stmt = $mysqli->prepare("SELECT file_data FROM terms_and_conditions ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($pdfData);
    $stmt->fetch();

    if ($pdfData) {
        // Output headers for PDF display
        header("Content-Type: application/pdf");
        header("Content-Disposition: inline; filename=\"terms_and_conditions.pdf\"");
        echo $pdfData;
    } else {
        http_response_code(404);
        echo "No PDF available.";
    }

    // Close the statement and exit to prevent further processing
    $stmt->close();
    exit;
}


?>
