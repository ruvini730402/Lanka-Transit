
<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT user_id FROM User WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already exists. Please use a different email.';
            } else {
                // Insert new user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO User (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $success = $stmt->execute([$name, $email, $hashedPassword]);
                
                if ($success) {
                    $_SESSION['success'] = 'Registered successfully! Please log in.';
                    header('Location: ../pages/login-form.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Registration failed. Please try again.';
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Database error occurred.';
            error_log("Registration error: " . $e->getMessage());
        }
        $database->closeConnection();
    } else {
        $_SESSION['error'] = 'Database connection failed.';
    }

    header('Location: ../pages/register-form.php');
    exit();
}




