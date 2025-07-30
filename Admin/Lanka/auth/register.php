
<?php
require_once __DIR__ . '/../classes/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = new User();
    $success = $user->register($name, $email, $password);

    if ($success) {
        $_SESSION['success'] = 'Registered successfully! Please log in.';
        header('Location: ../pages/login-form.php');
        exit();
    }

    $_SESSION['error'] = 'Registration failed. Please try again.';
    header('Location: ../pages/register-form.php');
    exit();
}




