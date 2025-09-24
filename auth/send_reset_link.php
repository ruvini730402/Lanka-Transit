<?php
session_start();
ini_set('display_errors', 0); // Disable error display for production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '/home/skul7candy/Lanka-Transit/logs/php_errors.log'); // Adjust path if needed

try {
    require_once __DIR__ . '/../classes/Database.php';
    require_once __DIR__ . '/../classes/User.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        $_SESSION['forgot_message'] = '❌ Method Not Allowed.';
        header('Location: http://localhost:8080/pages/forgot-password.php');
        exit;
    }

    $email = $_POST['email'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['forgot_message'] = '❌ Invalid email address.';
        header('Location: http://localhost:8080/pages/forgot-password.php');
        exit;
    }

    $user = new User();
    $userData = $user->findByEmail($email);

    if (!$userData) {
        $_SESSION['forgot_message'] = '❌ Email not found.';
        header('Location: http://localhost:8080/pages/forgot-password.php');
        exit;
    }

    // Generate token and expiry
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token and expiry in DB
    $user->setResetToken($email, $token, $expiry);

    // Construct reset link
    $resetLink = "http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token);
    error_log("Generated reset link: " . $resetLink);

    // Send email
    if (Database::sendResetEmail($email, $resetLink)) {
        $_SESSION['success'] = '✅ Reset link has been sent to your email.';
    } else {
        $_SESSION['forgot_message'] = '❌ Failed to send email. Please try again.';
        error_log("Failed to send reset email for: " . $email);
    }

    header('Location: http://localhost:8080/pages/forgot-password.php');
    exit;

} catch (Exception $e) {
    error_log("Error in send_reset_link.php: " . $e->getMessage());
    $_SESSION['forgot_message'] = '❌ An unexpected error occurred. Please try again.';
    header('Location: http://localhost:8080/pages/forgot-password.php');
    exit;
}