<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Database.php';

class Auth {
    private $user;
    private $pdo;
    
    public function __construct() {
        $this->user = new User();
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function login($email, $password) {
        // Don't start session here since login.php already starts it
        
        // Check Admin table first
        $adminStmt = $this->pdo->prepare("SELECT * FROM Admin WHERE Email = ?");
        $adminStmt->execute([$email]);
        $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['PasswordHash'])) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $admin['Name'];
            $_SESSION['admin_id'] = $admin['ID'];
            $_SESSION['success'] = 'Admin login successful';
            return ['success' => true, 'redirect' => 'admin_dashboard.php'];
        }

        // Fallback hardcoded admin check (remove this in production)
        if ($email === 'admin@lankatransit.com' && $password === 'adminlanka1234') {
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
            return ['success' => false, 'message' => $res['message'] ?? 'Login failed'];
        }

        $_SESSION['email'] = $email;
        $_SESSION['name'] = $res['user']['Name'];
        $_SESSION['user_id'] = $res['user']['ID'];
        $_SESSION['role'] = $res['user']['Role'] === 'administrator' ? 'admin' : 'user';
        $_SESSION['success'] = 'Login successful';
        
        $redirect = $res['user']['Role'] === 'administrator' ? 'admin_dashboard.php' : 'dashboard.php';
        return ['success' => true, 'redirect' => $redirect];
    }


 

}
