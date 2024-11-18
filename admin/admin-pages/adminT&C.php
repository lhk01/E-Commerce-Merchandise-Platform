<?php
    session_start();
    include ("header.php");
    
    if (!isset($_SESSION['admin_id'])) {
        redirect("../login/admin-login.php");
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Terms and Conditions</title>
    <link rel="stylesheet" href="../css/adminT&C.css">
    <script src="../javascript/adminT&C.js" defer></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


    <div class="main-content">
        
        <section class="tc-section">
            <h2>Upload Terms and Conditions</h2>

            <!-- Form for uploading PDF -->
            <form id="tcForm" enctype="multipart/form-data" method="POST" action="updateT&C.php">
                <label for="tcUpload">Upload T&C (PDF only):</label>
                <input type="file" id="tcUpload" name="pdf_file" accept="application/pdf" required>
                <button type="submit">Upload T&C</button>
            </form>
            
        </section>

        <!--pdf view-->
        <section class="pdf-view">
            <iframe id="pdfViewer"></iframe>
        </section>
    </div>
</body>
</html>
