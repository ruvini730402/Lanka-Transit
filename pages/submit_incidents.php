<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please log in to report incidents.');
        window.location.href = '../auth/login.php';
    </script>";
    exit();
}

$userId = $_SESSION['user_id'];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "<script>
        alert('Invalid request. Please try again.');
        window.location.href = 'incidents.php';
    </script>";
    exit;
}

// Include database configuration
require_once '../classes/Database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Unable to connect to our system. Please try again later.");
}

// Sanitize and receive POST input
$description = trim($_POST['description'] ?? '');
$bookingId = filter_var($_POST['booking_id'] ?? null, FILTER_VALIDATE_INT);

// Validate input
$errors = [];

if (empty($description) || strlen($description) < 10) {
    $errors[] = "Please provide a detailed description (at least 10 characters).";
}

if (!$bookingId || $bookingId === false) {
    $errors[] = "Please select a completed trip to report the incident.";
}

// Verify the booking belongs to the current user using phone number
if ($bookingId && empty($errors)) {
    // Get user's phone number
    $stmt = $conn->prepare("SELECT PhoneNumber FROM User WHERE ID = ?");
    $stmt->execute([$userId]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userInfo || !$userInfo['PhoneNumber']) {
        $errors[] = "Your account information is incomplete. Please contact support.";
    } else {
        // Verify booking belongs to user AND is a past booking (incidents can only be reported for completed trips)
        $stmt = $conn->prepare("SELECT ID, BookingTime FROM Booking WHERE ID = ? AND PhoneNumber = ? AND Status IN ('confirmed', 'completed') AND BookingTime < NOW()");
        $stmt->execute([$bookingId, $userInfo['PhoneNumber']]);
        $bookingData = $stmt->fetch();
        if (!$bookingData) {
            $errors[] = "Please select one of your completed trips. Incidents can only be reported for past journeys.";
            error_log("Security: User $userId attempted to report incident for unauthorized/future booking $bookingId");
        }
    }
}

// Prepare result
$response = "";
$statusClass = "";

if (!empty($errors)) {
    $response = implode("<br>", $errors);
    $statusClass = "danger";
} else {
    // Prepare incident data
    $status = 'submitted';
    date_default_timezone_set('Asia/Colombo');
    $reportedDate = date('Y-m-d');

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO Incident (BookingId, Description, Status, ReportedDate) VALUES (?, ?, ?, ?)");

    try {
        $stmt->execute([$bookingId, $description, $status, $reportedDate]);
        
        // Get the inserted incident ID for reference
        $incidentId = $conn->lastInsertId();

        // Log successful incident creation
        error_log("Incident created successfully - ID: $incidentId, UserID: $userId, BookingID: $bookingId");
        
        $response = "Your incident has been reported successfully. Reference ID: INC-$incidentId. We will investigate and get back to you soon.";
        $statusClass = "success";
    } catch (PDOException $e) {
        // Log the actual error for debugging
        error_log("Incident submission error: " . $e->getMessage());
        $response = "We encountered an issue while reporting your incident. Please try again later.";
        $statusClass = "danger";
    }
}

// Close database connection
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Submitted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .flash-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            display: none;
            padding: 15px 25px;
            border-radius: 8px;
            opacity: 0.95;
        }
    </style>
</head>
<body>

<!-- Flash message container -->
<div id="flashMessage" class="flash-message alert alert-<?php echo htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8'); ?>">
    <?php echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8'); ?>
</div>

<script>
    const msgBox = document.getElementById('flashMessage');
    msgBox.style.display = 'block';

    // Auto-hide the popup after 3 seconds
    setTimeout(() => {
        msgBox.style.opacity = '0';
        setTimeout(() => msgBox.remove(), 500);
    }, 2000);

    // Redirect to incidents.php after 3.5 seconds
    setTimeout(() => {
        window.location.href = 'incidents.php';
    }, 3500);
</script>

</body>
</html>