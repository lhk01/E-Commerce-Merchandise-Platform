<?php
session_start();
include ("../database/database.php");
include ("../phpMailer/mailer.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);  // Enable error reporting

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form data
    $send_at = date('Y-m-d H:i:s');
    $title = $_POST['subject'];
    $admin = 'merchadm1n@gmail.com';
    $name = $_POST['name'];
    $message = $_POST['message'];
    $type = "form";
    $time = "The email sent on " . $send_at;
    $email = $_POST['email']; // Assuming you're getting the email from the form

    // Start a transaction
    $mysqli->begin_transaction();

    try {
        // // Insert a new record into the notifications table
        // $sql_insert_notification = "INSERT INTO notifications (message) VALUES (?)";
        // $stmt_insert_notification = $mysqli->prepare($sql_insert_notification);
        // $stmt_insert_notification->bind_param("s", $message);

        // // Execute the query to insert into notifications table
        // if (!$stmt_insert_notification->execute()) {
        //     echo "Error inserting into notifications: " . $stmt_insert_notification->error;
        //     exit;
        // }

        // // Get the last inserted notification_id (useful for contact_form)
        $notification_id = 1;

        // Now, insert into the contact_form table and reference the notification_id
        $sql_insert_contact_form = "INSERT INTO contact_form (name, message, created_at, email, type, notification_id) 
                                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert_contact_form = $mysqli->prepare($sql_insert_contact_form);
        $stmt_insert_contact_form->bind_param("sssssi", $name, $message, $send_at, $email, $type, $notification_id);

        // Check for errors after execution
        if (!$stmt_insert_contact_form->execute()) {
            echo "Error inserting into contact_form: " . $stmt_insert_contact_form->error;
            exit;
        }

        // Commit the transaction if both queries succeed
        $mysqli->commit();

        // Send the email via mailer function (if required)
        mailer($admin , $title, "Contact By ".$name , $message, $time);

        $_SESSION['msg'] =  "Message has been sent and record updated successfully.";
        redirect("../pages/contact-us.php");
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $mysqli->rollback();
        $_SESSION['msg'] =  "There was an error processing your request: " . $e->getMessage();
         redirect("../pages/contact-us.php");
    }
}
?>