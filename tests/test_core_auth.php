<?php
/**
 * Test User Authentication
 * Core user authentication functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../classes/Database.php';

class TestUserAuth {
    private $results = [];
    private $user;
    private $auth;
    private $testUserId = null;
    
    public function __construct() {
        $this->user = new User();
        $this->auth = new Auth();
    }
    
    /**
     * Run all authentication tests
     */
    public function runAllTests() {
        echo "<h2>👤 User Authentication Tests</h2>";
        
        $this->testUserRegistration();
        $this->testUserLogin();
        $this->testPasswordHashing();
        $this->testDuplicateRegistration();
        $this->testInvalidLogin();
        $this->testPasswordReset();
        $this->cleanup();
        
        $this->displayResults();
    }
    
    /**
     * Test user registration
     */
    private function testUserRegistration() {
        try {
            $email = 'test_' . time() . '@example.com';
            $result = $this->user->register(
                'Test User',
                $email,
                'testpassword123',
                '0771234567'
            );
            
            if ($result['success']) {
                $this->addResult('✅', 'User Registration', 'New user registered successfully');
                
                // Verify user was created
                $userData = $this->user->findByEmail($email);
                if ($userData) {
                    $this->testUserId = $userData['ID'];
                    $this->addResult('✅', 'User Data Retrieval', 'Registered user found in database');
                } else {
                    $this->addResult('❌', 'User Data Retrieval', 'Registered user not found in database');
                }
            } else {
                $this->addResult('❌', 'User Registration', 'Registration failed: ' . $result['message']);
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'User Registration', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test user login
     */
    private function testUserLogin() {
        if (!$this->testUserId) {
            $this->addResult('⚠️', 'User Login', 'Skipped - no test user available');
            return;
        }
        
        try {
            $userData = $this->user->findByEmail('test_' . (time() - 1) . '@example.com');
            if (!$userData) {
                // Find the test user we just created
                $database = new Database();
                $conn = $database->getConnection();
                $stmt = $conn->prepare("SELECT * FROM User WHERE ID = ?");
                $stmt->execute([$this->testUserId]);
                $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            if ($userData) {
                $result = $this->user->login($userData['Email'], 'testpassword123');
                
                if ($result['success']) {
                    $this->addResult('✅', 'User Login', 'Valid credentials accepted');
                } else {
                    $this->addResult('❌', 'User Login', 'Valid credentials rejected: ' . $result['message']);
                }
                
                // Test invalid password
                $invalidResult = $this->user->login($userData['Email'], 'wrongpassword');
                if (!$invalidResult['success']) {
                    $this->addResult('✅', 'Invalid Password Rejection', 'Invalid password correctly rejected');
                } else {
                    $this->addResult('❌', 'Invalid Password Rejection', 'Invalid password incorrectly accepted');
                }
            } else {
                $this->addResult('❌', 'User Login', 'Test user not found for login test');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'User Login', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test password hashing
     */
    private function testPasswordHashing() {
        $password = 'testpassword123';
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);
        
        // Hashes should be different (salt effect)
        if ($hash1 !== $hash2) {
            $this->addResult('✅', 'Password Hashing', 'Unique salts generated for same password');
        } else {
            $this->addResult('❌', 'Password Hashing', 'Same hash generated (potential salt issue)');
        }
        
        // But both should verify correctly
        if (password_verify($password, $hash1) && password_verify($password, $hash2)) {
            $this->addResult('✅', 'Password Verification', 'Both hashes verify correctly');
        } else {
            $this->addResult('❌', 'Password Verification', 'Hash verification failed');
        }
    }
    
    /**
     * Test duplicate registration prevention
     */
    private function testDuplicateRegistration() {
        if (!$this->testUserId) {
            $this->addResult('⚠️', 'Duplicate Registration', 'Skipped - no test user available');
            return;
        }
        
        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare("SELECT Email FROM User WHERE ID = ?");
            $stmt->execute([$this->testUserId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $result = $this->user->register(
                    'Duplicate User',
                    $userData['Email'],
                    'anotherpassword',
                    '0771234568'
                );
                
                if (!$result['success']) {
                    $this->addResult('✅', 'Duplicate Email Prevention', 'Duplicate email registration correctly rejected');
                } else {
                    $this->addResult('❌', 'Duplicate Email Prevention', 'Duplicate email registration incorrectly allowed');
                }
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Duplicate Registration', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test invalid login attempts
     */
    private function testInvalidLogin() {
        // Test non-existent user
        $result = $this->user->login('nonexistent@example.com', 'password');
        if (!$result['success']) {
            $this->addResult('✅', 'Non-existent User', 'Non-existent user login correctly rejected');
        } else {
            $this->addResult('❌', 'Non-existent User', 'Non-existent user login incorrectly accepted');
        }
        
        // Test empty credentials
        $result = $this->user->login('', '');
        if (!$result['success']) {
            $this->addResult('✅', 'Empty Credentials', 'Empty credentials correctly rejected');
        } else {
            $this->addResult('❌', 'Empty Credentials', 'Empty credentials incorrectly accepted');
        }
    }
    
    /**
     * Test password reset functionality
     */
    private function testPasswordReset() {
        if (!$this->testUserId) {
            $this->addResult('⚠️', 'Password Reset', 'Skipped - no test user available');
            return;
        }
        
        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare("SELECT Email FROM User WHERE ID = ?");
            $stmt->execute([$this->testUserId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Set reset token
                $this->user->setResetToken($userData['Email'], $token, $expiry);
                
                // Verify token was set
                $tokenData = $this->user->findByToken($token);
                if ($tokenData && $tokenData['reset_token'] === $token) {
                    $this->addResult('✅', 'Password Reset Token', 'Reset token set and retrieved correctly');
                    
                    // Test password update
                    $newPassword = password_hash('newpassword123', PASSWORD_DEFAULT);
                    $updateResult = $this->user->updatePassword($this->testUserId, $newPassword);
                    
                    if ($updateResult) {
                        $this->addResult('✅', 'Password Update', 'Password updated successfully');
                        
                        // Verify token was cleared
                        $clearedTokenData = $this->user->findByToken($token);
                        if (!$clearedTokenData || !$clearedTokenData['reset_token']) {
                            $this->addResult('✅', 'Token Cleanup', 'Reset token cleared after password update');
                        } else {
                            $this->addResult('❌', 'Token Cleanup', 'Reset token not cleared');
                        }
                    } else {
                        $this->addResult('❌', 'Password Update', 'Password update failed');
                    }
                } else {
                    $this->addResult('❌', 'Password Reset Token', 'Reset token not set correctly');
                }
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Password Reset', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Cleanup test data
     */
    private function cleanup() {
        if ($this->testUserId) {
            try {
                $database = new Database();
                $conn = $database->getConnection();
                
                // Clean up User_2 first (if exists)
                $stmt = $conn->prepare("DELETE FROM User_2 WHERE user_id = ?");
                $stmt->execute([$this->testUserId]);
                
                // Clean up User
                $stmt = $conn->prepare("DELETE FROM User WHERE ID = ?");
                $stmt->execute([$this->testUserId]);
                
                $this->addResult('✅', 'Test Cleanup', 'Test user data cleaned up successfully');
            } catch (Exception $e) {
                $this->addResult('⚠️', 'Test Cleanup', 'Cleanup warning: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Add test result
     */
    private function addResult($status, $test, $message) {
        $this->results[] = [
            'status' => $status,
            'test' => $test,
            'message' => $message
        ];
    }
    
    /**
     * Display test results
     */
    private function displayResults() {
        echo "<div class='test-results'>";
        foreach ($this->results as $result) {
            echo "<div class='test-item'>";
            echo "<span class='status'>{$result['status']}</span> ";
            echo "<strong>{$result['test']}:</strong> {$result['message']}";
            echo "</div>";
        }
        echo "</div>";
        
        $passed = count(array_filter($this->results, function($r) { return $r['status'] === '✅'; }));
        $total = count($this->results);
        echo "<p><strong>Authentication Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_auth.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Authentication Tests</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
            .test-results { margin: 20px 0; }
            .test-item { padding: 8px; margin: 4px 0; border-left: 4px solid #007bff; background: #f8f9fa; }
            .status { font-size: 1.2em; }
            h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Lanka Transit - Authentication Tests</h1>
            <?php
            $test = new TestUserAuth();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>