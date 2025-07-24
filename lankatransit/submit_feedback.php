<?php
// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// Get form data
$userId   = $_POST['user_id'] ?? null;
$busId    = $_POST['bus_id'] ?? null;
$comment  = trim($_POST['comment'] ?? '');
$rating   = $_POST['rating'] ?? null;
$createdDate = date("Y-m-d");

// Validation
if (empty($busId) || empty($rating)) {
    $response = "❌ Missing required fields. Please fill out the form completely.";
    $statusClass = "danger";
} else {
    // Prepare SQL
    $stmt = $conn->prepare("INSERT INTO Feedback (UserId, BusId, Comment, Rating, CreatedDate) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        $response = "❌ SQL error: " . $conn->errorInfo()[2];
        $statusClass = "danger";
    } else {
        // Handle nullable UserId
        try {
            if ($userId === '' || $userId === null) {
                $stmt->execute([null, $busId, $comment, $rating, $createdDate]);
            } else {
                $stmt->execute([$userId, $busId, $comment, $rating, $createdDate]);
            }
            $response = "✅ Your feedback was successfully submitted!";
            $statusClass = "success";
        } catch (PDOException $e) {
            $response = "❌ Failed to submit feedback: " . $e->getMessage();
            $statusClass = "danger";
        }
    }
}

// Close database connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submitting Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .flash-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 25px;
            font-size: 16px;
            z-index: 9999;
            border-radius: 8px;
            transition: opacity 0.5s ease;
        }
    </style>
</head>
<body>

<!-- Flash Message Output -->
<div id="flashMessage" class="flash-message alert alert-<?php echo $statusClass; ?>">
    <?php echo $response; ?>
</div>

<script>
    const msgBox = document.getElementById('flashMessage');
    msgBox.style.display = 'block';

    // Auto-hide after 2 seconds
    setTimeout(() => {
        msgBox.style.opacity = '0';
        setTimeout(() => msgBox.remove(), 500);
    }, 2000);

    // Redirect after 3.5 seconds
    setTimeout(() => {
        window.location.href = 'UserDashboard.php';
    }, 2000);
</script>

</body>
</html>
