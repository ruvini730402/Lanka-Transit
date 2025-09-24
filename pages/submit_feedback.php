<?php
require_once __DIR__ . '/../includes/session_config.php';
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to share your feedback']);
    exit();
}

require_once '../classes/Database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Unable to connect to our system. Please try again later.']);
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
        error_log("Feedback validation failed - BookingId: $bookingId, BusId: $busId, Rating: $rating");
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields and select a rating from 1-5 stars']);
        exit();
    }
    
    // Get user's phone number from database
    $stmt = $conn->prepare("SELECT PhoneNumber FROM User WHERE ID = ?");
    $stmt->execute([$userId]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userInfo || !$userInfo['PhoneNumber']) {
        error_log("User phone number not found for UserId: $userId");
        echo json_encode(['success' => false, 'message' => 'Your account information is incomplete. Please contact support.']);
        exit();
    }
    
    $userPhoneNumber = $userInfo['PhoneNumber'];
    error_log("Feedback submission attempt - UserId: $userId, Phone: $userPhoneNumber, BookingId: $bookingId, BusId: $busId");
    
    // Verify the booking belongs to the user using phone number (consistent with dashboard)
    $stmt = $conn->prepare("SELECT ID FROM Booking WHERE ID = ? AND PhoneNumber = ? AND BusID = ?");
    $stmt->execute([$bookingId, $userPhoneNumber, $busId]);
    if (!$stmt->fetch()) {
        error_log("Booking verification failed - BookingId: $bookingId, Phone: $userPhoneNumber, BusId: $busId");
        echo json_encode(['success' => false, 'message' => 'We cannot verify this booking. Please ensure you select one of your trips.']);
        exit();
    }
    
    // Check if feedback already exists for this specific booking
    $stmt = $conn->prepare("SELECT ID FROM Feedback WHERE BookingId = ?");
    $stmt->execute([$bookingId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'You have already shared feedback for this trip. Thank you!']);
        exit();
    }
    
    // Insert feedback
    $stmt = $conn->prepare("
        INSERT INTO Feedback (UserId, BusId, BookingId, Rating, Comment, CreatedDate) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$userId, $busId, $bookingId, $rating, $comment])) {
        echo json_encode(['success' => true, 'message' => 'Thank you for sharing your feedback! Your input helps us improve our service.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'We encountered an issue saving your feedback. Please try again.']);
    }
    
} catch (Exception $e) {
    error_log("Feedback submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong while saving your feedback. Please try again later.']);
}
?>