<?php
require_once __DIR__ . '/../classes/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo =  Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setResetToken($email, $token, $expiry) {
        $stmt = $this->pdo->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
       // return $stmt->execute([$token, $expiry, $email]);
        $update = $stmt->execute([$token, $expiry, $email]);

if (!$update) {
    echo '❌ Failed to update token in database.';
    exit;
}
    }

    public function findByToken($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function register($name, $email, $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        return ['success' => true, 'message' => 'User registered.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

    public function login($email, $password) {
    $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return [
            'success' => false,
            'message' => 'Email not found.'
        ];
    }

    if (!password_verify($password, $user['password'])) {
        return [
            'success' => false,
            'message' => 'Incorrect password.'
        ];
    }

    // Determine redirect page by role or email (adjust as needed)
    $redirect = 'user_dashboard.php';
    if (isset($user['role']) && $user['role'] === 'admin') {
        $redirect = 'admin_dashboard.php';
    } elseif ($user['email'] === 'admin@admin.com') { // fallback admin email check
        $redirect = 'admin_dashboard.php';
    }

    return [
        'success' => true,
        'user' => $user,
        'redirect' => $redirect
    ];
}

}
