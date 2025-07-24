<?php
// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "<script>
        alert('❌ Invalid request method.');
        window.location.href = 'incident_page.php';
    </script>";
    exit;
}

// Include database configuration
require_once '../config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: Unable to connect to database");
}

// 2. Sanitize and receive POST input
$phoneNumber = trim($_POST['phone_number'] ?? '');
$description = trim($_POST['description'] ?? '');

// 3. Validate input
$errors = [];

if (empty($phoneNumber) || !preg_match('/^[0-9]{10}$/', $phoneNumber)) {
    $errors[] = "Invalid phone number. Must be exactly 10 digits.";
}

if (empty($description) || strlen($description) < 10) {
    $errors[] = "Description must be at least 10 characters.";
}

// 4. Prepare result
$response = "";
$statusClass = "";

if (!empty($errors)) {
    $response = implode("<br>", $errors);
    $statusClass = "danger";
} else {
    // 5. Prepare incident data
    $bookingId = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);  // e.g. "0003", "1223"

    // Fixed initial status
    $status = 'submitted';

    date_default_timezone_set('Asia/Colombo');
    $reportedDate = date('Y-m-d H:i:s');
    $resolvedDate = NULL;

    // 6. Insert into database
    $stmt = $conn->prepare("INSERT INTO Incident (BookingId, Description, Status, ReportedDate, ResolvedDate) VALUES (?, ?, ?, ?, ?)");

    try {
        $stmt->execute([$bookingId, $description, $status, $reportedDate, $resolvedDate]);
        $response = "✅ Incident reported successfully. Your Booking ID is: <strong>$bookingId</strong><br>Status: <strong>$status</strong>";
        $statusClass = "success";
    } catch (PDOException $e) {
        $response = "❌ Failed to submit incident. Please try again.";
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
<div id="flashMessage" class="flash-message alert alert-<?php echo $statusClass; ?>">
    <?php echo $response; ?>
</div>

<script>
    const msgBox = document.getElementById('flashMessage');
    msgBox.style.display = 'block';

    // Auto-hide the popup after 3 seconds
    setTimeout(() => {
        msgBox.style.opacity = '0';
        setTimeout(() => msgBox.remove(), 500);
    }, 2000);

    // Redirect to Report_Incidents.php after 3.5 seconds
    setTimeout(() => {
        window.location.href = 'UserDashboard.php';
    }, 3500);
</script>

</body>
</html>
