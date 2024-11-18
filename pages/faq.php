<?php 
session_start();
include("../database/database.php"); 
include("../header-footer/header.php"); 
$query = "SELECT question, answer FROM faq";
$result = $mysqli->query($query);

$faqs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $faqs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQ</title>
  <link rel = "stylesheet" href = "../css/faq.css">
  
</head>
<body>
  <div class = "container">
   <div class="faq-container">
    <div class="faq-header">
      <h1>Frequently Asked Questions</h1>
    </div>
    <?php 
    if (!empty($faqs)) {
        foreach ($faqs as $faq) {
            echo '<div class="faq-item">';
            echo '<div class="faq-question" onclick="toggleAnswer(this)">' . htmlspecialchars($faq['question']) . '</div>';
            echo '<div class="faq-answer">' . htmlspecialchars($faq['answer']) . '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No FAQs available at the moment.</p>';
    }
    ?>
  </div>
  </div>

  <script>
    function toggleAnswer(element) {
      const answer = element.nextElementSibling;
      if (answer.style.display === "block") {
        answer.style.display = "none";
      } else {
        answer.style.display = "block";
      }
    }
  </script>
</body>
</html>
<?php include("../header-footer/footer.php");?>

