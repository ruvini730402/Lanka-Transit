<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// DB connection (update with your credentials)
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "lankatrasit";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Please provide a valid name and email.";
    header("Location: Setting.php");
    exit();
}

// Handle profile picture upload if exists
$profile_picture_path = null;

if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_type = mime_content_type($file_tmp);
    
    if (!in_array($file_type, $allowed_types)) {
        $_SESSION['error'] = "Only JPG, PNG, GIF images are allowed for profile picture.";
        header("Location: Setting.php");
        exit();
    }

    // Limit file size to 2MB
    if ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) {
        $_SESSION['error'] = "Profile picture must be smaller than 2MB.";
        header("Location: Setting.php");
        exit();
    }

    // Generate a unique file name
    $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
    $new_filename = $username . "_profile." . $ext;
    $upload_dir = __DIR__ . "/uploads/";
    $destination = $upload_dir . $new_filename;

    if (!move_uploaded_file($file_tmp, $destination)) {
        $_SESSION['error'] = "Failed to upload profile picture.";
        header("Location: Setting.php");
        exit();
    }

    // Save relative path to DB
    $profile_picture_path = "uploads/" . $new_filename;
}

// Update user data in DB
if ($profile_picture_path) {
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE username = ?");
    $stmt->bind_param("ssss", $name, $email, $profile_picture_path, $username);
} else {
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE username = ?");
    $stmt->bind_param("sss", $name, $email, $username);
}

if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully.";
} else {
    $_SESSION['error'] = "Database error: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: Setting.php");
exit();
