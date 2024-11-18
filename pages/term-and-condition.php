<?php

session_start();
include("../database/database.php");
include("../header-footer/header.php");


// Prepare the SQL query to fetch the first PDF from the table
$sql = "SELECT file_name, file_data FROM terms_and_conditions LIMIT 1";

// Prepare the statement
if ($stmt = $mysqli->prepare($sql)) {
    // Execute the query
    $stmt->execute();
    
    // Bind the result variables
    $stmt->bind_result($file_name, $file_data);
    
    // Fetch the result
    if ($stmt->fetch()) {
        // Serve the file data to a variable
        $file_data_encoded = base64_encode($file_data);
    } else {
        echo "No PDF found.";
        exit;
    }
    
    // Close the statement
    $stmt->close();
} else {
    echo "Database query failed.";
    exit;
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View PDF</title>
    <link rel="stylesheet" href="../css/term-and-condition.css">

</head>
<body>
    <div class ="container">
      <h1>Term and Condition</h1>
      <div class="pdf-container">
        <iframe src="data:application/pdf;base64,<?php echo $file_data_encoded; ?>"></iframe>
    </div>
    </div>
</body>
</html>
<?php include("../header-footer/footer.php");?>
