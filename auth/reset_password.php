<?php
session_start();
ini_set('display_errors', 0); // Disable error display for production
ini_set('log_errors', 1); // Enable error logging
ini_set('error_log', '/home/skul7candy/Lanka-Transit/logs/php_errors.log'); // Adjust path if needed

try {
    require_once __DIR__ . '/../classes/User.php';

    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$token || !$password || !$confirm_password) {
        $_SESSION['reset_error'] = '⚠️ All fields are required.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['reset_error'] = '❌ Passwords do not match.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    if (strlen($password) < 12 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $_SESSION['reset_error'] = '❌ Password must be at least 12 characters long and include uppercase, lowercase letters, and at least one number.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    $user = new User();
    $userData = $user->findByToken($token);

    if (!$userData) {
        error_log("Token not found: " . $token);
        $_SESSION['reset_error'] = '❌ Invalid token.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    if (!isset($userData['ID'])) {
        error_log("User ID not found in userData for token: " . $token);
        $_SESSION['reset_error'] = '❌ Invalid user data.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    if (strtotime($userData['token_expiry']) < time()) {
        error_log("Token expired for token: " . $token . ", Expiry: " . $userData['token_expiry']);
        $_SESSION['reset_error'] = '❌ Token has expired.';
        header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $user->updatePassword($userData['ID'], $hashedPassword);

    // Clear any existing session
    session_unset();
    session_destroy();

    // Start a new session for success message
    session_start();
    $_SESSION['success'] = '✅ Password has been reset successfully. Please log in.';
    header("Location: http://localhost:8080/pages/login-form.php");
    exit;

} catch (Exception $e) {
    error_log("Error in reset_password.php: " . $e->getMessage() . " | Token: " . $token);
    $_SESSION['reset_error'] = '❌ An unexpected error occurred.';
    header("Location: http://localhost:8080/pages/reset_password_form.php?token=" . urlencode($token));
    exit;
}