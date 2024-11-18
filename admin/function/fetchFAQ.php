<?php
include("../database/database.php");

// Handle different AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $question = $mysqli->real_escape_string($_POST['question']);
        $answer = $mysqli->real_escape_string($_POST['answer']);
        
        $sql = "INSERT INTO faq (question, answer) VALUES ('$question', '$answer')";
        if ($mysqli->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "FAQ added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $mysqli->error]);
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id']);
        $sql = "DELETE FROM faq WHERE id = $id";
        if ($mysqli->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "FAQ deleted successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $mysqli->error]);
        }
    } elseif ($action === 'get') {
        $result = $mysqli->query("SELECT * FROM faq");
        $faqs = [];
        while ($row = $result->fetch_assoc()) {
            $faqs[] = $row;
        }
        echo json_encode(["success" => true, "faqs" => $faqs]);
    } elseif ($action === 'update') {
        $id = intval($_POST['id']);
        $question = $mysqli->real_escape_string($_POST['question']);
        $answer = $mysqli->real_escape_string($_POST['answer']);
        
        $sql = "UPDATE faq SET question = '$question', answer = '$answer' WHERE id = $id";
        if ($mysqli->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "FAQ updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $mysqli->error]);
        }
    }
}


?>
