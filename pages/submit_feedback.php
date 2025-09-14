<?php
session_start();
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to submit feedback']);
    exit();
}

require_once '../classes/Database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Get form data
    $userId = (int)$_SESSION['user_id'];
    $bookingId = filter_var($_POST['bookingId'] ?? null, FILTER_VALIDATE_INT);
    $busId = filter_var($_POST['busId'] ?? null, FILTER_VALIDATE_INT);
    $rating = filter_var($_POST['rating'] ?? null, FILTER_VALIDATE_INT);
    $comment = trim($_POST['comment'] ?? '');
    
    // Validation
    if (!$bookingId || !$busId || !$rating || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
        exit();
    }
    
    // Verify the booking belongs to the user
    $stmt = $conn->prepare("SELECT ID FROM Booking WHERE ID = ? AND UserId = ? AND BusID = ?");
    $stmt->execute([$bookingId, $userId, $busId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Booking not found or access denied']);
        exit();
    }
    
    // Check if feedback already exists for this bus and user on the booking date
    $stmt = $conn->prepare("
        SELECT f.ID 
        FROM Feedback f 
        JOIN Booking b ON f.BusId = b.BusID AND f.UserId = b.UserId
        WHERE b.ID = ? AND f.UserId = ? AND f.BusId = ? 
        AND DATE(f.CreatedDate) = DATE(b.BookingTime)
    ");
    $stmt->execute([$bookingId, $userId, $busId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already submitted feedback for this trip']);
        exit();
    }
    
    // Insert feedback
    $stmt = $conn->prepare("
        INSERT INTO Feedback (UserId, BusId, Rating, Comment, CreatedDate) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$userId, $busId, $rating, $comment])) {
        echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
    }
    
} catch (Exception $e) {
    error_log("Feedback submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while submitting feedback']);
}
?>
