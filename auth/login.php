

<?php
require_once __DIR__ . '/../includes/session_config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login-form.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Please enter both email and password.';
    header('Location: ../pages/login-form.php');
    exit;
}

try {
    $auth = new Auth();
    $res = $auth->login($email, $password);

    if (!empty($res['success']) && $res['success'] === true) {
        // Session variables are already set in Auth class, use the redirect from response
        $redirectPage = $res['redirect'] ?? 'dashboard.php';
        
        if ($redirectPage === 'admin_dashboard.php') {
            header("Location: ../Admin/admin.html");
        } else {
            header("Location: ../pages/dashboard.php");
        }
        exit;
    } else {
        // On failure, set error message and redirect back to login form
        $_SESSION['error'] = $res['message'] ?? 'Login failed. Please try again.';
        header('Location: ../pages/login-form.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Login system error: ' . $e->getMessage();
    header('Location: ../pages/login-form.php');
    exit;
}


