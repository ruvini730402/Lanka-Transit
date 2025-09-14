<?php
// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "<script>
        alert('❌ Invalid request method.');
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
    die("Connection failed: Unable to connect to database");
}

// Sanitize and receive POST input
$description = trim($_POST['description'] ?? '');

// Validate input
$errors = [];

if (empty($description) || strlen($description) < 10) {
    $errors[] = "Description must be at least 10 characters.";
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
    $stmt = $conn->prepare("INSERT INTO Incident (Description, Status, ReportedDate) VALUES (?, ?, ?)");

    try {
        $stmt->execute([$description, $status, $reportedDate]);
        
        // Get the inserted incident ID for reference
        $incidentId = $conn->lastInsertId();

        $response = "✅ Incident reported successfully. Your Incident ID is: INC-$incidentId";
        $statusClass = "success";
    } catch (PDOException $e) {
        // Log the actual error for debugging
        error_log("Incident submission error: " . $e->getMessage());
        $response = "❌ Failed to submit incident. Database error: " . $e->getMessage();
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