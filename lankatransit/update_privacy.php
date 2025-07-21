<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Database connection
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "lankatrasit"; 

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get visibility value from the form
$visibility = $_POST['profile_visibility'] ?? '';

if (!in_array($visibility, ['public', 'private'])) {
    $_SESSION['error'] = "Invalid privacy option selected.";
    header("Location: Setting.php");
    exit();
}

// Convert visibility to boolean for DB (1 = public, 0 = private)
$is_public = $visibility === 'public' ? 1 : 0;

// Update DB
$stmt = $conn->prepare("UPDATE users SET is_public = ? WHERE username = ?");
$stmt->bind_param("is", $is_public, $username);

if ($stmt->execute()) {
    $_SESSION['success'] = "Privacy settings updated successfully.";
} else {
    $_SESSION['error'] = "Database error: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: Setting.php");
exit();
