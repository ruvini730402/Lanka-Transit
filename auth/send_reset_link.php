<?php
require_once __DIR__ . '/../includes/session_config.php';
require_once __DIR__ . '/../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['forgot_message'] = 'Invalid email address.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

$user = new User();
$userData = $user->findByEmail($email);

if (!$userData) {
    $_SESSION['forgot_message'] = '❌ Email not found.';
    header('Location: ../pages/forgot-password.php');
    exit;
}

// Generate token and expiry
$token = bin2hex(random_bytes(32));
$expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Store token and expiry in DB
$user->setResetToken($email, $token, $expiry);

// Send email
if (Database::sendResetEmail($email, $token)) {
    $_SESSION['success'] = '✅ Reset link has been sent to your email.';
} else {
    $_SESSION['forgot_message'] = '❌ Failed to send email. Please try again.';
}

header('Location: ../pages/forgot-password.php');
exit;
