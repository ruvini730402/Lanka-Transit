<?php
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/database[WRONG].php'; // Include Database class
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $mobile = trim($_POST['mobile'] ?? '');

    // Basic validation for mobile number
    if (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $_SESSION['error'] = 'Please enter a valid 10-digit mobile number.';
        header('Location: ../pages/register-form.php');
        exit();
    }

    $user = new User();
    $result = $user->register($name, $email, $password, $mobile);

    if ($result['success']) {
        // Send registration confirmation email
        $emailSent = Database::sendRegistrationEmail($email, $name);
        if ($emailSent) {
            $_SESSION['success'] = 'Registered successfully! A confirmation email has been sent to your email address. Please log in.';
        } else {
            $_SESSION['success'] = 'Registered successfully! However, we couldn’t send a confirmation email. Please log in.';
        }
        header('Location: ../pages/login-form.php');
        exit();
    }

    $_SESSION['error'] = $result['message'];
    header('Location: ../pages/register-form.php');
    exit();
}