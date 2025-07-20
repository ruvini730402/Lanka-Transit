<?php
require_once __DIR__ . '/User.php';

class Auth {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }

    public function login($email, $password) {
    session_start();

    // Admin credentials check
    if ($email === 'admin@gmail.com' && $password === 'adminlanka1234') {
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        $_SESSION['name'] = 'System Administrator';
        $_SESSION['success'] = 'Admin login successful';
        return ['success' => true, 'redirect' => 'admin_dashboard.php'];
    }

    // Normal user login
    $res = $this->user->login($email, $password);

    if (!$res['success']) {
        $errorMessage = isset($res['message']) ? trim($res['message']) : '';
        $_SESSION['error'] = $errorMessage !== '' ? $errorMessage : 'Login failed. Please try again.';
        return ['success' => false];
    }

    $_SESSION['email'] = $email;
    $_SESSION['name'] = $res['user']['name'];
    $_SESSION['role'] = 'user';
    $_SESSION['success'] = 'Login successful';
    return ['success' => true, 'redirect' => 'user_dashboard.php'];
}


 

}
