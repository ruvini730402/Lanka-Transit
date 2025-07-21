<?php
session_start();

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Access Denied'); window.location.href='userDashboard.php';</script>";
    exit();
}

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'lankatrasit';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Sanitize input
function clean($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

// Collect and clean form data
$name          = clean($_POST['name'] ?? '');
$feedback_type = clean($_POST['feedback_type'] ?? '');
$rating        = clean($_POST['rating'] ?? '');
$message       = clean($_POST['message'] ?? '');

// Validate required fields (message is optional now)
if (!$name || !$feedback_type || !$rating) {
    echo "<script>alert('⚠️ Please fill in all required fields.'); window.history.back();</script>";
    exit();
}

// Prepare and insert into DB
$stmt = $conn->prepare("INSERT INTO feedback (name, feedback_type, rating, message, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssis", $name, $feedback_type, $rating, $message);

if ($stmt->execute()) {
    header("Location: feedback_success.php");
    exit();
}
