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
    <title>FAQ Settings</title>
    <link rel="stylesheet" href="../css/adminFAQ.css">
</head>
<body>

    <div class="main-content">
        <!-- Box for FAQ Form -->
        <section class="faq-section">
            <h1 class="page-title">Add a New FAQ</h1>

            <form class="faqForm">
                <label for="faqQuestion">Question:</label>
                <input type="text" id="faqQuestion" class="faqQuestion" required>

                <label for="faqAnswer">Answer:</label>
                <textarea id="faqAnswer" class="faqAnswer" rows="4" required></textarea>

                
                <button type="submit">Add FAQ</button>
            </form>
        </section>

        <!-- Box for FAQ List -->
        <section class="faqList-section">
            <h1 class="page-title">FAQ List</h1>

            <div class="faqList">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="faqList">
                        <!-- Existing FAQs will appear here -->
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script src="../javascript/adminFAQ.js"></script>
</body>
</html>
