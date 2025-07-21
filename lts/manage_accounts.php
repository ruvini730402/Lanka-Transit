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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_POST['account_action'] ?? '';

if ($action === 'deactivate') {
    // Deactivate account (e.g., set an 'active' flag to 0)
    $stmt = $conn->prepare("UPDATE users SET active = 0 WHERE username = ?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        session_destroy(); // Log out user
        header("Location: login.php?message=account_deactivated");
        exit();
    } else {
        $_SESSION['error'] = "Failed to deactivate account.";
        header("Location: Setting.php");
        exit();
    }

} elseif ($action === 'delete') {
    // Delete account permanently
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        session_destroy(); // Log out user
        header("Location: login.php?message=account_deleted");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete account.";
        header("Location: Setting.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Invalid account action.";
    header("Location: Setting.php");
    exit();
}

$stmt->close();
$conn->close();
?>
