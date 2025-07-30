<?php
session_start();
require_once __DIR__ . '/../classes/User.php';

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!$token || !$password || !$confirm_password) {
    $_SESSION['reset_error'] = '⚠️ All fields are required.';
    header("Location: ../pages/reset_password_form.php?token=" . urlencode($token));
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['reset_error'] = '❌ Passwords do not match.';
    header("Location: ../pages/reset_password_form.php?token=" . urlencode($token));
    exit;
}

try {
    $user = new User();
    $userData = $user->findByToken($token);

    if (!$userData || strtotime($userData['token_expiry']) < time()) {
        $_SESSION['reset_error'] = '❌ Invalid or expired token.';
        header("Location: ../pages/reset_password_form.php?token=" . urlencode($token));
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $user->updatePassword($userData['id'], $hashedPassword);

    // ✅ Clear any existing session (including old login state)
    session_unset();
    session_destroy();

// ✅ Start a new session to store the success message
    session_start();
    $_SESSION['success'] = '✅ Password has been reset successfully. Please log in.';
    header("Location: ../pages/login-form.php");
    exit;


} catch (Exception $e) {
    error_log("Password Reset Error: " . $e->getMessage());
    $_SESSION['reset_error'] = '❌ An unexpected error occurred.';
    header("Location: ../pages/reset_password_form.php?token=" . urlencode($token));
    exit;
}

