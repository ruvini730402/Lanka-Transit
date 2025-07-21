

<?php
session_start();
require_once __DIR__ . '/../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login-form.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$auth = new Auth();
$res = $auth->login($email, $password);

if (!empty($res['success']) && $res['success'] === true) {
    // Set session data for logged-in user
    $_SESSION['user_id'] = $res['user']['id'];
    $_SESSION['user_email'] = $res['user']['email'];
    $_SESSION['role'] = $res['user']['role'] ?? 'user';  // set role if present or default 'user'
    $_SESSION['success'] = 'Login successful! Redirecting...';

    // Redirect to provided page (e.g., dashboard)
    header("Location: ../pages/{$res['redirect']}");
    exit;
} else {
    // On failure, set error message and redirect back to login form
    $_SESSION['error'] = $res['message'] ?? 'Login failed. Please try again.';
    header('Location: ../pages/login-form.php');
    exit;
}


