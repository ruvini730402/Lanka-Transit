<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback Submitted</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="Feedback_success.css">
</head>
<body>
     <?php
include 'Header.php';
?>
  <div class="popup-container">
    <div class="popup-box">
      <h2>✅ Thank You!</h2>
      <p>Your feedback has been submitted successfully.</p>
      <a href="UserFeedbackForm.php" class="btn">Return to Feedback Form</a>
    </div>
  </div>
     <?php
include 'Footer.php';
?>
</body>
</html>
