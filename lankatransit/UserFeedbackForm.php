<?php
session_start();

// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// Try to get UserId from session (optional)
$userId = $_SESSION['UserID'] ?? null;

// Fetch buses — Make sure 'Bus' table exists
$stmt = $conn->prepare("SELECT ID, BusNumber FROM Bus");
$stmt->execute();
$busResult = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feedback Form - LankaTransit</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="UserFeedbackForm.css" />
</head>
<body>
<?php
$showBackIcon = true;
include 'Header.php';
?>

<div class="feedback-wrapper">
  <div class="feedback-container">
    <h4 class="mb-3 text-center">Feedback Form</h4>
    <p class="text-muted mb-4 text-center">
      We value your opinion! Help us improve the LankaTransit experience by sharing your thoughts.
    </p>

    <form action="submit_feedback.php" method="POST">
      <?php if ($userId): ?>
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($userId) ?>">
      <?php endif; ?>

      <div class="mb-3">
        <label for="bus_id" class="form-label">Select Bus</label>
        <select name="bus_id" id="bus_id" class="form-select" required>
          <option value="">-- Select Bus --</option>
          <?php foreach ($busResult as $bus): ?>
            <option value="<?= $bus['ID'] ?>"><?= htmlspecialchars($bus['BusNumber']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label for="rating" class="form-label">Rating</label>
        <select name="rating" id="rating" class="form-select" required>
          <option value="">-- Select Rating --</option>
          <option value="5">Excellent (5)</option>
          <option value="4">Very Good (4)</option>
          <option value="3">Good (3)</option>
          <option value="2">Fair (2)</option>
          <option value="1">Poor (1)</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="comment" class="form-label">Comments (Optional)</label>
        <textarea name="comment" id="comment" rows="5" class="form-control" placeholder="Write your comments here..."></textarea>
      </div>

      <div class="text-end">
        <button type="submit" class="btn-lanka">Submit Feedback</button>
      </div>
    </form>
  </div>
</div>

<?php include 'Footer.php'; ?>
</body>
</html>
