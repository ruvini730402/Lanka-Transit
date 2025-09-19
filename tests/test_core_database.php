<?php
/**
 * Test Database Configuration
 * Core database functionality tests for Lanka Transit
 */

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../config/env_loader.php';

class TestDatabase {
    private $results = [];
    private $database;
    
    public function __construct() {
        $this->database = new Database();
    }
    
    /**
     * Run all database tests
     */
    public function runAllTests() {
        echo "<h2>🗄️ Database Tests</h2>";
        
        $this->testConnection();
        $this->testEnvironmentVariables();
        $this->testPreparedStatements();
        $this->testInputValidation();
        $this->testInputSanitization();
        
        $this->displayResults();
    }
    
    /**
     * Test database connection
     */
    private function testConnection() {
        try {
            $conn = $this->database->getConnection();
            if ($conn && $conn instanceof PDO) {
                $this->addResult('✅', 'Database Connection', 'Successfully established PDO connection');
                
                // Test a simple query
                $stmt = $conn->prepare("SELECT 1 as test");
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result && $result['test'] == 1) {
                    $this->addResult('✅', 'Basic Query', 'Simple SELECT query executed successfully');
                } else {
                    $this->addResult('❌', 'Basic Query', 'Failed to execute simple query');
                }
                
            } else {
                $this->addResult('❌', 'Database Connection', 'Failed to establish connection');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Database Connection', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test environment variables loading
     */
    private function testEnvironmentVariables() {
        $required_vars = ['DB_HOST', 'DB_NAME', 'DB_USERNAME'];
        $all_present = true;
        
        foreach ($required_vars as $var) {
            if (!EnvLoader::has($var)) {
                $this->addResult('❌', 'Environment Variables', "Missing required variable: $var");
                $all_present = false;
            }
        }
        
        if ($all_present) {
            $this->addResult('✅', 'Environment Variables', 'All required database environment variables present');
        }
    }
    
    /**
     * Test prepared statements functionality
     */
    private function testPreparedStatements() {
        try {
            $conn = $this->database->getConnection();
            
            // Test parameterized query
            $stmt = $conn->prepare("SELECT ? as param_test, ? as param_test2");
            $stmt->execute(['value1', 'value2']);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['param_test'] === 'value1' && $result['param_test2'] === 'value2') {
                $this->addResult('✅', 'Prepared Statements', 'Parameter binding works correctly');
            } else {
                $this->addResult('❌', 'Prepared Statements', 'Parameter binding failed');
            }
        } catch (Exception $e) {
            $this->addResult('❌', 'Prepared Statements', 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Test input validation methods
     */
    private function testInputValidation() {
        // Test email validation
        $valid_email = Database::validateInput('test@example.com', 'email');
        $invalid_email = Database::validateInput('invalid-email', 'email');
        
        if ($valid_email && !$invalid_email) {
            $this->addResult('✅', 'Email Validation', 'Valid email accepted, invalid email rejected');
        } else {
            $this->addResult('❌', 'Email Validation', 'Email validation not working correctly');
        }
        
        // Test phone validation
        $valid_phone = Database::validateInput('0771234567', 'phone');
        $invalid_phone = Database::validateInput('invalid-phone', 'phone');
        
        if ($valid_phone && !$invalid_phone) {
            $this->addResult('✅', 'Phone Validation', 'Valid phone accepted, invalid phone rejected');
        } else {
            $this->addResult('❌', 'Phone Validation', 'Phone validation not working correctly');
        }
        
        // Test date validation
        $valid_date = Database::validateInput('2025-09-19', 'date');
        $invalid_date = Database::validateInput('invalid-date', 'date');
        
        if ($valid_date && !$invalid_date) {
            $this->addResult('✅', 'Date Validation', 'Valid date accepted, invalid date rejected');
        } else {
            $this->addResult('❌', 'Date Validation', 'Date validation not working correctly');
        }
    }
    
    /**
     * Test input sanitization
     */
    private function testInputSanitization() {
        $dangerous_input = "<script>alert('xss')</script>";
        $sanitized = Database::sanitizeInput($dangerous_input);
        
        if (strpos($sanitized, '<script>') === false) {
            $this->addResult('✅', 'Input Sanitization', 'XSS attempt successfully sanitized');
        } else {
            $this->addResult('❌', 'Input Sanitization', 'XSS sanitization failed');
        }
        
        // Test SQL injection attempt
        $sql_injection = "'; DROP TABLE users; --";
        $sanitized_sql = Database::sanitizeInput($sql_injection);
        
        if ($sanitized_sql !== $sql_injection) {
            $this->addResult('✅', 'SQL Injection Protection', 'SQL injection attempt sanitized');
        } else {
            $this->addResult('⚠️', 'SQL Injection Protection', 'Basic sanitization applied (rely on prepared statements)');
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
        echo "<p><strong>Database Tests: {$passed}/{$total} passed</strong></p>";
    }
}

// Run tests if accessed directly
if (basename($_SERVER['PHP_SELF']) === 'test_core_database.php') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Lanka Transit - Database Tests</title>
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
            <h1>Lanka Transit - Database Tests</h1>
            <?php
            $test = new TestDatabase();
            $test->runAllTests();
            ?>
            <p><a href="run_all_tests.php">← Back to Test Suite</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>