<?php
// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// Get form data
$userId    = $_POST['user_id'] ?? null;
$bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);
$comment   = trim($_POST['comment'] ?? '');
$rating    = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
$createdDate = date("Y-m-d");

// Get bus ID from booking
$busId = null;
if ($bookingId) {
    $stmt = $conn->prepare("SELECT BusID FROM Booking WHERE ID = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($booking) {
        $busId = $booking['BusID'];
    }
}

// Additional validation
if ($userId !== null && !is_numeric($userId)) {
    $userId = null;
}

if ($rating !== false && ($rating < 1 || $rating > 5)) {
    $rating = null;
}

// Validation
if ($bookingId === false || $bookingId === null || $busId === null || $rating === false || $rating === null) {
    if ($bookingId === false || $bookingId === null) {
        $response = "❌ Please select a trip to review.";
    } elseif ($busId === null) {
        $response = "❌ Invalid booking selection. Please try again.";
    } elseif ($rating === false || $rating === null) {
        $response = "❌ Please provide a rating for your trip.";
    } else {
        $response = "❌ Missing required fields. Please fill out the form completely.";
    }
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
<div id="flashMessage" class="flash-message alert alert-<?php echo htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>">
    <?php echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8'); ?>
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
        window.location.href = 'feedback.php';
    }, 2000);
</script>

</body>
</html>
