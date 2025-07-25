

<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login-form.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login-form.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, email, password, role FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session data for logged-in user
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            $_SESSION['success'] = 'Login successful! Redirecting...';

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                header('Location: ../pages/dashboard.php');
            }
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Database error occurred.';
        error_log("Login error: " . $e->getMessage());
    }
    $database->closeConnection();
} else {
    $_SESSION['error'] = 'Database connection failed.';
}

header('Location: ../pages/login-form.php');
exit;


