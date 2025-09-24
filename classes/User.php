<?php
require_once __DIR__ . '/../classes/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM User WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setResetToken($email, $token, $expiry) {
        // First get the user ID, then update User_2 table for reset tokens
        $userStmt = $this->pdo->prepare("SELECT ID FROM User WHERE Email = ?");
        $userStmt->execute([$email]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo '❌ User not found.';
            exit;
        }
        
        // Update or insert into User_2 table for reset token
        $stmt = $this->pdo->prepare("INSERT INTO User_2 (user_id, reset_token, token_expiry) VALUES (?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE reset_token = ?, token_expiry = ?");
        $update = $stmt->execute([$user['ID'], $token, $expiry, $token, $expiry]);

        if (!$update) {
            echo '❌ Failed to update token in database.';
            exit;
        }
    }

    public function findByToken($token) {
        $stmt = $this->pdo->prepare("SELECT u.*, u2.reset_token, u2.token_expiry 
                                     FROM User u 
                                     JOIN User_2 u2 ON u.ID = u2.user_id 
                                     WHERE u2.reset_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($userId, $hashedPassword) {
        // Update password in User table
        $stmt1 = $this->pdo->prepare("UPDATE User SET PasswordHash = ? WHERE ID = ?");
        $result1 = $stmt1->execute([$hashedPassword, $userId]);
        
        // Clear reset token in User_2 table
        $stmt2 = $this->pdo->prepare("UPDATE User_2 SET reset_token = NULL, token_expiry = NULL WHERE user_id = ?");
        $result2 = $stmt2->execute([$userId]);
        
        return $result1 && $result2;
    }

    public function register($name, $email, $password, $phoneNumber) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            // Check if email already exists
            $checkStmt = $this->pdo->prepare("SELECT ID FROM User WHERE Email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered.'];
            }

            // Check if mobile number already exists
            $checkMobileStmt = $this->pdo->prepare("SELECT ID FROM User WHERE PhoneNumber = ?");
            $checkMobileStmt->execute([$phoneNumber]);
            if ($checkMobileStmt->fetch()) {
                return ['success' => false, 'message' => 'Mobile number already registered.'];
            }
            
            $stmt = $this->pdo->prepare("INSERT INTO User (Name, Email, PasswordHash, PhoneNumber, Role) VALUES (?, ?, ?, ?, 'registered user')");
            $stmt->execute([$name, $email, $hashedPassword, $phoneNumber]);
            return ['success' => true, 'message' => 'User registered successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM User WHERE Email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'No account found with that email address.'
                ];
            }

            if (!password_verify($password, $user['PasswordHash'])) {
                return [
                    'success' => false,
                    'message' => 'Incorrect password. Please try again.'
                ];
            }

            // Determine redirect page by role
            $redirect = 'dashboard.php';
            if (isset($user['Role']) && $user['Role'] === 'administrator') {
                $redirect = 'admin_dashboard.php';
            }

            return [
                'success' => true,
                'user' => $user,
                'redirect' => $redirect
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error during login. Please try again.'
            ];
        }
    }
}